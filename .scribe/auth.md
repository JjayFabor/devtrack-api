# Authenticating requests

To authenticate requests, include a **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

## Authentication Required

- **Bearer Token**: First, log in to obtain your user token. Send it in the `Authorization` header as `Bearer {YOUR_AUTH_TOKEN}`.
- **API Key**: You must also generate an API key using the **API Key Management** endpoints. Provide your API key in the `X-API-KEY` header for all authenticated requests.

### **Note**: Most endpoints require <u>both</u> the Bearer token and the API key in the headers.
