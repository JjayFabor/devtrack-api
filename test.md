
# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Project Overview

**Haka Live** is a social audio/video live streaming platform similar to Taka, BIGO Live, and Yalla. Users can join live rooms as hosts, speakers, or listeners. Features include real-time chat, virtual gifting, leaderboards, a wallet system, and an agent/family system for host management.

---

## Commands

### Backend (Node.js)

```bash
# Start all services (dev)
docker compose -f docker-compose.dev.yml up --build

# Run Prisma migrations (dev — generates migration files)
docker compose -f docker-compose.dev.yml exec backend npx prisma migrate dev

# Deploy migrations (prod/CI)
docker compose -f docker-compose.dev.yml exec backend npx prisma migrate deploy

# Regenerate Prisma client after schema changes
docker compose -f docker-compose.dev.yml exec backend npx prisma generate

# Open Prisma Studio (DB GUI)
docker compose -f docker-compose.dev.yml exec backend npx prisma studio

# Run all tests
docker compose -f docker-compose.dev.yml exec backend npm test

# Run a single test file
docker compose -f docker-compose.dev.yml exec backend npm test -- src/modules/accounts/tests/auth.test.ts

# Format code
docker compose -f docker-compose.dev.yml exec backend npm run format

# Lint
docker compose -f docker-compose.dev.yml exec backend npm run lint

# Build (TypeScript compile)
docker compose -f docker-compose.dev.yml exec backend npm run build
```

API docs: `http://localhost:3000/api/docs/` (Swagger UI)

### Mobile (React Native / Expo)

```bash
cd apps/mobile
npm install
npm start          # Expo dev server
npm run android    # Run on Android
npm run ios        # Run on iOS
npm run lint       # ESLint
npm run type-check # TypeScript check (tsc --noEmit)
```

### Building Android APK

**EAS Build (recommended):**

```bash
cd apps/mobile
eas build --platform android --profile preview --local
```

- Uses the `preview` profile from `eas.json` which sets `"buildType": "apk"` and all required env vars (`EXPO_PUBLIC_API_URL`, `EXPO_PUBLIC_GOOGLE_WEB_CLIENT_ID`, etc.)
- `--local` runs the build on your machine instead of Expo's cloud servers
- Output APK path is printed at the end of the build

**EAS Build profiles (`eas.json`):**
- `development` — dev client with debugging, internal distribution
- `preview` — release APK for testers (internal distribution)
- `production` — release APK for store submission

**Firebase prerequisite:**
The signing keystore's SHA-1 fingerprint must be registered in Firebase Console (Project Settings > Android app > Add fingerprint). Get the debug keystore SHA-1 with:
```bash
keytool -list -v -keystore apps/mobile/android/app/debug.keystore -alias androiddebugkey -storepass android
```

**Direct Gradle build (fallback):**

If EAS is unavailable, build directly with Gradle. Note: env vars from `eas.json` are NOT applied — they must be set in `apps/mobile/.env` instead (ensure `EXPO_PUBLIC_API_URL` points to the deployed backend, not a local IP).

```bash
cd apps/mobile/android
./gradlew assembleRelease -Dorg.gradle.workers.max=2
```

- `-Dorg.gradle.workers.max=2` limits parallel workers to prevent OOM on machines with <=16 GB RAM.
- `gradle.properties` is set to `reactNativeArchitectures=arm64-v8a` (single architecture) to reduce memory usage. For emulator builds, override with `-PreactNativeArchitectures=x86_64`.
- Output APK: `apps/mobile/android/app/build/outputs/apk/release/app-release.apk`

**If the Gradle cache is corrupted** (e.g. after an OOM crash):
```bash
rm -rf ~/.gradle/caches/journal-1/file-access.bin
```

---

## Architecture

### Monorepo Layout

```
haka-live/
├── apps/
│   ├── backend/          → Node.js/Express API (TypeScript)
│   └── mobile/           → React Native (Expo 54)
├── packages/
│   └── shared-types/     → Shared TypeScript constants (single source of truth)
├── docker/
│   ├── backend/Dockerfile
│   └── nginx/nginx.conf
├── docker-compose.yml      (production)
└── docker-compose.dev.yml  (dev, hot reload)
```

### Backend — Module Layout (`apps/backend/src/modules/`)

| Module | Purpose |
|---|---|
| `accounts` | Firebase Auth verification, user profiles, roles, follow/special-attention, visitors, location |
| `rooms` | Live streaming room lifecycle, seat management, MIC configs |
| `chat` | Room + DM messaging (Socket.io) |
| `gifts` | Virtual gifts catalogue + send + bean distribution |
| `wallet` | In-app coins/beans, atomic transactions, records, exchange |
| `levels` | Rich Level + Charm Level, 21-tier XP progression |
| `family` | Host–agent family relationships, Gold/Silver/Bronze tiers |
| `leaderboard` | Redis sorted-set rankings (daily/weekly/monthly) |
| `notifications` | Push (FCM via Firebase Admin) + in-app Socket.io notifications |
| `moderation` | Reports, bans, content moderation |
| `payments` | Stripe card, Apple Pay, Google Pay, agent top-up, withdrawals |
| `agents` | Agency centre, host roster, commission tracking, coin seller |
| `invites` | Invite friends/creators, rewards, rules |

### Backend — Directory Structure (`apps/backend/src/`)

```
src/
├── modules/              → Feature modules
│   └── accounts/
│       ├── accounts.controller.ts
│       ├── accounts.service.ts
│       ├── accounts.routes.ts
│       └── accounts.test.ts
├── config/               → env, Firebase Admin init, Agora, Stripe, Redis, BullMQ
├── middleware/           → JWT auth, Firebase token verifier, error handler, rate limiter
├── sockets/              → Socket.io namespace handlers (rooms, chat, notifications)
├── jobs/                 → BullMQ job definitions + processors
├── utils/                → Shared helpers (response builder, logger, etc.)
└── app.ts                → Express app setup (routes, middleware, Socket.io attach)
prisma/
└── schema.prisma         → Single source of truth for DB schema
```

**Config:** `src/config/` — `env.ts` (typed env vars), `firebase.ts`, `agora.ts`, `stripe.ts`, `redis.ts`, `bullmq.ts`

**WebSocket routing** is in `src/sockets/`, delegating to `rooms.socket.ts`, `chat.socket.ts`, `notifications.socket.ts`.

**Request flow:**
```
Mobile → Nginx → Express.js (HTTP REST) → PostgreSQL (via Prisma)
               → Socket.io (WebSocket) → Redis pub/sub
Background: BullMQ Workers via Redis broker
Auth: Firebase Admin SDK (ID token verification) → backend-issued JWT
Media: Agora RTC (direct to Agora infra, RTC token generated by backend)
Push notifications: Firebase Admin SDK → FCM → Mobile
```

### Authentication Flow (Firebase)

```
1. Mobile: Firebase Client SDK → Phone OTP or Google Sign-in → Firebase ID Token
2. Mobile: POST /api/v1/auth/firebase  { idToken }
3. Backend: firebase-admin.auth().verifyIdToken(idToken) → decoded uid, phone/email
4. Backend: upsert User in DB (firebaseUid as unique key)
5. Backend: issue own access JWT (15min) + refresh token (30d)
6. Mobile: store JWTs in SecureStore → use for all subsequent API calls
7. 401 on access token → POST /api/v1/auth/refresh → new access JWT
```

### Mobile — `apps/mobile/src/`

- `api/` — Axios REST client
- `store/` — Redux Toolkit (global state)
- `screens/` — Screen components (navigation targets)
- `components/` — Reusable UI
- `navigation/` — React Navigation (native-stack + bottom-tabs)
- `hooks/` — Custom React hooks
- `types/` — TypeScript types (imports from `@haka-live/shared-types`)

### Shared Types — `packages/shared-types/`

Single source of truth for constants used by both backend and mobile: roles, room types, WS event names, level types, gift types, error codes.

---

## Database Rules — NEVER DEVIATE

- All PKs: **UUIDs** (`@id @default(uuid())` in Prisma schema)
- All models: **`createdAt` + `updatedAt`** (auto via `@default(now())` and `@updatedAt`)
- All wallet/balance operations: **`prisma.$transaction()` with raw `SELECT ... FOR UPDATE`** for row-level locking
- `agentId` FK on User is nullable but **IMMUTABLE once set** (enforced at service layer)
- Nullable string fields: use `String?` in Prisma — do not default to empty string for nullable relations
- Optional non-null string fields: `String @default("")` — never `null`
- Never fake migrations — always generate real Prisma migrations (`prisma migrate dev`)
- API response envelope: `{ "success": true, "data": {}, "message": "" }`

---

## User Roles & System

Every account starts as `normal_user`. Role transitions are **forward-only**.

| Role | Can transition to | Description |
|---|---|---|
| `normal_user` | `host`, `agent` | Default. Can gift, join rooms, follow users |
| `host` | — | Live streaming. Two sub-types: `independent` or `agent_host` |
| `agent` | — | Agency centre. Earns commission. Manages host roster |

### Bean Distribution (CRITICAL — atomic)

Single function `distributeBeans(giftTransactionId)` in `gifts/gifts.service.ts`:
- `agent_host` with agent set → 3-way split: **host 70% / agent 20% / company 10%**
- All others → 2-way split: **recipient 70% / company 30%**
- Must run inside one `prisma.$transaction()` with `SELECT ... FOR UPDATE`
- **Never credit beans outside this function**

### Host Onboarding — 3 Paths

| Path | `hostApplicationPath` | `hostType` |
|---|---|---|
| Agent sends invitation | `agency_invitation` | `agent_host` |
| User self-applies with agent ID | `self_apply_with_agent` | `agent_host` |
| User self-applies, no agent | `self_apply_independent` | `independent` |

---

## Payment Rules (CRITICAL)

- **Coins credited ONLY inside the Stripe webhook handler** — never at checkout time
- `coinsCredited` Boolean on `PaymentTransaction` prevents double-crediting on duplicate webhooks
- **Apple Pay + Google Pay** are handled via Stripe PaymentIntent (`payment_method_types: ['card', 'apple_pay', 'google_pay']`) — no separate integration
- **Agent top-up** is manual (no gateway) — agent logs it in dashboard, protected endpoint credits coins via `AgentTransaction` model

---

## Key Models (Prisma schema — must match exactly)

### User

All role/host/agent fields live directly on `User` — no separate profile table.

```prisma
model User {
  id                    String   @id @default(uuid())
  firebaseUid           String   @unique           // Firebase UID — auth link
  role                  String   @default("normal_user") // normal_user | host | agent
  hostType              String   @default("")       // independent | agent_host | ""
  hostApplicationPath   String   @default("")       // agency_invitation | self_apply_with_agent | self_apply_independent | ""
  agentId               String?                     // FK to User (agent), nullable, IMMUTABLE once set
  agent                 User?    @relation("AgentHosts", fields: [agentId], references: [id])
  hosts                 User[]   @relation("AgentHosts")
  hakaId                String   @unique            // public-facing ID
  onboardingComplete    Boolean  @default(false)
  createdAt             DateTime @default(now())
  updatedAt             DateTime @updatedAt
}
```

### Payments

```prisma
model CoinPackage {
  id          String   @id @default(uuid())
  coins       Int
  bonusCoins  Int      @default(0)
  priceGbp    Decimal
  isActive    Boolean  @default(true)
  createdAt   DateTime @default(now())
  updatedAt   DateTime @updatedAt
}

model PaymentTransaction {
  id                    String   @id @default(uuid())
  userId                String
  packageId             String
  method                String   // card | apple_pay | google_pay
  stripePaymentIntentId String   @unique
  amountGbp             Decimal
  status                String   // pending | succeeded | failed
  coinsCredited         Boolean  @default(false)
  createdAt             DateTime @default(now())
  updatedAt             DateTime @updatedAt
}

model AgentTransaction {
  id              String   @id @default(uuid())
  agentId         String
  customerId      String
  coinsSold       Int
  amountCollected Decimal
  currency        String   @default("GBP")
  notes           String   @default("")
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt
}
```

---

## Design System

### Colors — `src/theme/colors.ts`

```ts
export const Colors = {
  // Backgrounds
  background:       '#FFFFFF',
  surface:          '#13131F',
  surfaceElevated:  '#1C1C2E',
  surfaceHighlight: '#252538',

  // Brand
  primary:          '#7B4FFF',
  primaryDark:      '#5B2FD4',
  primaryLight:     '#9D7FFF',
  primarySubtle:    '#7B4FFF22',

  // Accent
  gold:             '#E8A020',
  goldLight:        '#F5C842',
  goldSubtle:       '#E8A02018',

  // Economy
  coin:             '#E8A020',
  bean:             '#22C97A',
  beanSubtle:       '#22C97A18',

  // Semantic
  success:          '#22C97A',
  danger:           '#FF4D4D',
  warning:          '#E8A020',
  info:             '#4DA6FF',

  // Text
  textPrimary:      '#FFFFFF',
  textSecondary:    '#9090AA',
  textTertiary:     '#55556A',
  textInverse:      '#0B0B14',

  // Borders
  border:           '#2A2A40',
  borderLight:      '#333350',

  // Live / Status
  live:             '#FF4444',
  online:           '#22C97A',

  // Gradients (arrays for LinearGradient)
  gradientPurple:   ['#7B4FFF', '#4A1FCC'],
  gradientGold:     ['#F5C842', '#E8A020'],
  gradientDark:     ['#1C1C2E', '#0B0B14'],
  gradientCard:     ['#1C1C2E00', '#1C1C2EFF'],
} as const;
```

### Typography — `src/theme/typography.ts`

```ts
export const Typography = {
  fontFamily: 'Inter',

  sizes:   { xs: 11, sm: 13, md: 15, lg: 17, xl: 20, xxl: 24, hero: 30 },
  weights: { regular: '400', medium: '500', semibold: '600', bold: '700' },
  lineHeights: { tight: 1.2, normal: 1.5, relaxed: 1.7 },

  styles: {
    hero:      { fontSize: 30, fontWeight: '700', color: '#FFFFFF' },
    h1:        { fontSize: 24, fontWeight: '700', color: '#FFFFFF' },
    h2:        { fontSize: 20, fontWeight: '600', color: '#FFFFFF' },
    h3:        { fontSize: 17, fontWeight: '600', color: '#FFFFFF' },
    body:      { fontSize: 15, fontWeight: '400', color: '#FFFFFF' },
    bodySmall: { fontSize: 13, fontWeight: '400', color: '#9090AA' },
    caption:   { fontSize: 11, fontWeight: '400', color: '#55556A' },
    label:     { fontSize: 13, fontWeight: '500', color: '#FFFFFF' },
    button:    { fontSize: 15, fontWeight: '600', color: '#FFFFFF' },
    buttonSm:  { fontSize: 13, fontWeight: '600', color: '#FFFFFF' },
    username:  { fontSize: 13, fontWeight: '400', color: '#9090AA' },
    badge:     { fontSize: 11, fontWeight: '700', color: '#FFFFFF' },
  },
} as const;
```

### Spacing — `src/theme/spacing.ts`

```ts
export const Spacing = {
  xs: 4, sm: 8, md: 12, lg: 16, xl: 24, xxl: 32, xxxl: 48,
} as const;
```

### Border Radius — `src/theme/radius.ts`

```ts
export const Radius = {
  xs: 4, sm: 8, md: 12, lg: 16, xl: 24, full: 999,
} as const;
```

### Shadows — `src/theme/shadows.ts`

```ts
export const Shadows = {
  card:   { shadowColor: '#000000', shadowOffset: { width: 0, height: 4 },  shadowOpacity: 0.3, shadowRadius: 12, elevation: 8  },
  purple: { shadowColor: '#7B4FFF', shadowOffset: { width: 0, height: 4 },  shadowOpacity: 0.4, shadowRadius: 12, elevation: 8  },
  bottom: { shadowColor: '#000000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.3, shadowRadius: 12, elevation: 12 },
} as const;
```

### Component Specs

**Buttons**
- Primary: bg `Colors.primary`, height 52, `borderRadius: Radius.lg`, font 15/600
- Outlined: transparent, `borderWidth: 1`, `borderColor: Colors.border`, height 44
- Ghost: no bg/border, color `Colors.primary`
- Small: height 34, `borderRadius: Radius.full`, paddingH 16, font 13/600

**Inputs**
- bg `Colors.surfaceElevated`, `borderWidth: 1`, border `Colors.border` → `Colors.primary` on focus
- `borderRadius: Radius.md`, height 52, paddingH 16, font 15

**Cards**: bg `Colors.surface`, `borderRadius: Radius.lg`, `borderWidth: 1`, border `Colors.border`, padding 16

**Badges**
- Live: bg `Colors.live`, white 11/700, paddingH 6 paddingV 2, `borderRadius: Radius.xs`
- Role/Host: bg `Colors.primarySubtle`, color `Colors.primaryLight`, `borderRadius: Radius.full`
- Level: bg `Colors.goldSubtle`, color `Colors.gold`, `borderRadius: Radius.full`
- Online dot: 10×10, `borderRadius: 5`, bg `Colors.online`, border 2 `Colors.surface`

**Avatars**: sizes xs/28 sm/36 md/48 lg/64 xl/80 xxl/100 — all circles, `borderWidth: 2`, border `Colors.border`

**Tab bar**: bg `Colors.background`, height 86 + safe area, `paddingHorizontal: 15`, `justifyContent: space-between`, active `#FF2D55`, inactive `#DDDDDD`
- Home icon: 35×25
- Discover icon: 38×25
- Profile icon: 25×25
- Notifications icon: 25×26

**Room mic seat**: bg `Colors.surfaceElevated`, `borderRadius: Radius.full`
- Sizes: 5-mic 72px · 10-mic 60px · 15-mic 52px · 20-mic 48px
- Empty: `Colors.textTertiary` · Occupied: user avatar · Host: `borderWidth: 2`, border `Colors.gold`

### Layout Rules

- All screens: `backgroundColor: Colors.background`
- Safe area: always use `useSafeAreaInsets()`
- Screen horizontal padding: 16px both sides
- Section spacing between blocks: 24px
- List item height: 64px with avatar, 48px text-only
- Bottom sheets: `borderTopLeftRadius: 20, borderTopRightRadius: 20`
- All gradients: `expo-linear-gradient`
- Dark overlay on images: `rgba(0,0,0,0.5)` LinearGradient bottom-to-top

### Icon Style

- Library: `@expo/vector-icons` (Ionicons or MaterialCommunityIcons)
- Sizes: 20 nav · 24 actions · 16 inline
- Active: `Colors.primary` · Inactive: `Colors.textTertiary`

### UI Coding Rules

1. Never hardcode hex values — always use `Colors.*`
2. Never hardcode numbers — always use `Spacing.*` or `Radius.*`
3. Always use `StyleSheet.create()` — no inline style objects
4. Dynamic values (opacity, transform) are the only exception to rule 3
5. Every screen must handle: loading state, error state, empty state
6. Use skeleton loaders (grey animated boxes) during data fetching
7. All lists use `FlatList` — never `ScrollView` with `.map()`
8. Images use `expo-image` for caching — never `<Image>` from react-native

---

## Architecture Note — Role System Placement

The role system (`role`, `hostType`, `agentId` FK, `hostApplicationPath`) lives **directly on the `User` model** — there is no separate UserProfile table. All fields, constraints, and service-layer enforcement are in `accounts/accounts.service.ts`. Do not create a separate UserProfile model.

The `GET /api/v1/auth/me` response already includes `role`, `hostType`, and `hostApplicationPath`.

---

## Feature Build Order — ONE AT A TIME, WAIT FOR APPROVAL

> **Never start the next feature until the current one is approved.**
> All backend features require a full rebuild on Node.js. Mobile Scaffold (M1) needs an update pass for Firebase Auth client + Agora RTC SDK.

| # | Feature | Status |
|---|---|---|
| 1 | Authentication & User Accounts (Firebase OTP/Google, JWT, onboarding, me) | ⬜ REBUILD |
| M1 | **Mobile Scaffold** — API client, Firebase Auth client, Agora SDK wiring, navigation | ⬜ UPDATE |
| 2 | User Profiles & Social Graph (follow, special attention, visitors, nearby) | ⬜ REBUILD |
| 3 | Room System (create room, MIC configs 5/10/15/20, seat management) | ⬜ REBUILD |
| 4 | Agora Integration (RTC token generation, room lifecycle webhooks) | ⬜ REBUILD |
| 5 | Voice Room WebSocket (join/leave/roles/hand-raise/seat changes via Socket.io) | ⬜ REBUILD |
| 6 | Real-time Chat (per-room messages + quick messages via Socket.io) | ⬜ REBUILD |
| 7 | Gift System (catalogue + send + Socket.io broadcast + bean distribution) | ⬜ REBUILD |
| 8 | Wallet System (coins, beans, atomic transactions, records, exchange) | ⬜ REBUILD |
| 9 | Level System (Rich Level + Charm Level, 21-tier, auto-upgrade) | ⬜ REBUILD |
| Agency | Agency Commission Tier System + Agency Center + Host Tasks | ⬜ REBUILD |
| 10 | Family System (create/join family, ranks, Gold/Silver/Bronze tiers) | ⬜ REBUILD |
| 11 | Payments (Stripe card, Apple Pay, Google Pay, agent top-up, withdrawal) | ⬜ REBUILD |
| 12 | Leaderboards (Redis sorted sets — daily/weekly/monthly) | ⬜ REBUILD |
| 13 | Host Application System (3 paths: agency invitation, self-apply with/without agent) | ⬜ REBUILD |
| 14 | Agent System (agency centre, host roster, commission tracking, coin seller) | ⬜ REBUILD |
| 15 | Invite System (invite friends/creators, rewards, rules) | ⬜ not started |
| 16 | Activity & Analytics (live data daily/weekly/monthly, income/host analysis) | ⬜ REBUILD |
| 17 | Notifications (push FCM via Firebase Admin + in-app Socket.io) | ⬜ not started |
| 18 | Moderation & Settings (reports, bans, admin centre, account security) | ⬜ not started |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend API | Node.js 20 + Express.js 4 + TypeScript |
| Real-time (WebSocket) | Socket.io 4 |
| Database | PostgreSQL 16 |
| ORM / Migrations | Prisma 5 |
| Cache / Leaderboards | Redis 7 (sorted sets for rankings) |
| Task Queue | BullMQ 5 (Redis-backed, replaces Celery) |
| Auth | Firebase Auth (Phone OTP + Google) + Firebase Admin SDK (token verification) |
| Storage | AWS S3 via aws-sdk v3 |
| Audio/Video | Agora RTC (`agora-access-token` for server-side RTC token generation) |
| Payments | Stripe + Apple Pay + Google Pay (via Stripe PaymentIntent) |
| Mobile | React Native 0.76.7 (Expo 54) |
| Mobile Auth | Firebase Auth Client SDK (react-native-firebase) |
| Mobile Media | Agora RTC React Native SDK |
| State | Redux Toolkit 2.2.7 |
| Web Server | Nginx 1.25 (reverse proxy) |
| API Docs | swagger-jsdoc + swagger-ui-express at `/api/docs/` |
| Testing | Jest + Supertest |
| Notifications | Firebase Admin SDK → FCM (push) + Socket.io (in-app) |

---

## Mobile Scaffold Spec (Feature M1 — Updated)

> Runs in parallel with backend feature development. Goal: app is always runnable regardless of backend progress.

### API Client — `src/api/client.ts`

Axios instance with:
- `baseURL` from `EXPO_PUBLIC_API_URL` environment variable
- Request interceptor: reads backend JWT from `expo-secure-store` and injects `Authorization: Bearer <token>` header
- 401 handler: auto-refreshes using `/api/v1/auth/refresh`, retries once; on second 401, clears tokens and redirects to AuthStack
- Response interceptor: unwraps `{ success, data, message }` envelope — resolves with `data`, rejects with `message` on `success: false`

### Auth Client — `src/api/auth.ts`

Firebase Auth client SDK handles:
- Phone OTP: `signInWithPhoneNumber()` → confirmation → `confirm(code)` → Firebase ID token
- Google Sign-in: `GoogleSignin.signIn()` → Firebase ID token
- Both flows end by calling `POST /api/v1/auth/firebase` with the Firebase ID token to get backend JWTs

### Mock Layer — `src/api/mock/`

Toggle in `src/api/config.ts`:
```ts
export const useMock = true; // set false to hit real backend
```

Fixture files:
- `src/api/mock/auth.ts` — `register`, `login`, `me` responses
- `src/api/mock/rooms.ts` — room list, room detail
- `src/api/mock/gifts.ts` — gift catalogue
- `src/api/mock/wallet.ts` — wallet balance, records

### Navigation Structure — `src/navigation/`

**AuthStack** (shown when no valid JWT in SecureStore):
- `SplashScreen`
- `OnboardingScreen` (steps 1, 2, 3)
- `LoginScreen`
- `RegisterScreen`
- `VerifyScreen`

**MainStack** (shown when token exists and is valid) — bottom-tab navigator (5 tabs):
- `HomeScreen` (tab 0: Live feed — `pulse` icon)
- `CreateRoomScreen` (tab 1: Go Live — `videocam` icon)
- `DiscoverScreen` (tab 2: Discover — `game-controller` icon)
- `ChatScreen` (tab 3: Messages — `chatbubbles` icon)
- `ProfileScreen` (tab 4: Me — `person-circle` icon)

**RoomStack** (modal presented over MainStack):
- `RoomScreen`

### Feature Placeholders

Every screen that depends on a backend feature not yet built renders:
```tsx
<FeaturePlaceholder name="Gift System" eta="Feature 7" />
```
