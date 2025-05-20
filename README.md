# 🚀 DevTrack API – Developer Productivity Platform (Backend)

**DevTrack** is a personal productivity API built for developers to manage tasks, log learning sessions, track project progress, and record bugs or lessons learned — all in a centralized, versioned RESTful API.

---

## 📌 Table of Contents

- [🎯 Project Overview](#-project-overview)
- [📦 Features](#-features)
- [🧱 Tech Stack](#-tech-stack)
- [🗂️ ER Diagram](#️-er-diagram)
- [📁 Project Structure](#-project-structure)
- [⚙️ Installation & Setup](#️-installation--setup)
- [🔐 Authentication](#-authentication)
- [📡 API Endpoints](#-api-endpoints)
- [🧪 Testing](#-testing)
- [📈 Future Enhancements](#-future-enhancements)
- [🧑‍💻 Author](#-author)

---

## 🎯 Project Overview

**DevTrack** is designed to boost developer productivity by providing a self-managed API that:

- ✅ Manages daily programming tasks with deadlines and priorities
- 📚 Logs learning sessions with topics, durations, and takeaways
- 🐞 Tracks bugs with code snippets and resolutions
- 📁 Organizes side projects and links tasks/bugs to them
- 🔖 Categorizes entries using tags for better filtering and analytics

---

## 📦 Features

### 🔐 Authentication

- User registration and login (via Laravel Sanctum)
- Token-based authentication for CLI/web/mobile clients
- Logout and token revocation support

### ✅ Task Management

- Create, update, delete, and retrieve tasks
- Support for priority, status (`todo`, `in-progress`, `done`)
- Tagging system
- Associate tasks with projects

### 🧠 Learning Logs

- Track learning sessions with title, topics, duration, and resources
- Supports full-text summary storage

### 🐞 Error/Bug Tracker

- Record bugs and unexpected behavior
- Include code snippets, cause, resolution, and severity

### 🛠️ Project Tracker

- Create and manage development projects
- Automatically compute project progress from associated task completion
- Include GitHub links or documentation references

### 📊 Developer Dashboard (Planned)

- Visualize weekly task progress, bugs fixed, and learning time
- Calculate productivity metrics like streaks, project completion %, etc.

---

## 🧱 Tech Stack

- **Framework**: Laravel 12.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **ORM**: Eloquent & Database Query Builder (complex queries)
- **Containerization**: Docker / Laravel Sail (optional)
- **Testing**: Pest PHP
- **Dev Tools**: Postman, Laravel Telescope (optional)

---

## 🗂️ ER Diagram

You can view the full ERD here:
👉 [**DevTrack ERD on Eraser**](https://app.eraser.io/workspace/FJpN1yXtsoatypxTX0J1?origin=share)

### 📘 Description of Relationships

- **User**
  ↳ has many **Projects**

- **Project**
  ↳ belongs to a **User**
  ↳ has many **Tasks**
  ↳ stores its own **tags** directly (e.g., `["laravel", "backend"]`)

- **Task**
  ↳ belongs to a **Project**
  ↳ has many **LearningLogs**
  ↳ has many **Bugs**
  ↳ stores its own **status** and metadata like `priority`, `status`, `deadline`

- **LearningLog**
  ↳ belongs to a **Task**
  ↳ contains `topics`, `resources`, `duration`

- **Bug/Error**
  ↳ belongs to a **Task**
  ↳ contains `title`, `description`, `code_snippet`, `status`, `cause`, `resolution`

---

### 🧱 Data Model Notes

| Model            | Notable Fields                                                       |
|------------------|----------------------------------------------------------------------|
| **User**         | `name`, `email`, `password`                                          |
| **Project**      | `name`, `description`, `status`, `tags` (array/string), `github_url` |
| **Task**         | `title`, `description`, `status`, `priority`, `deadline`             |
| **LearningLog**  | `topic`, `summary`, `resources`, `duration`                          |
| **Bug/Error**    | `title`, `code_snippet`, `cause`, `resolution`, `severity`           |

## 🧑‍💻 Author

**Jaylord Vhan Fabor**
*Software Developer – Laravel & Full Stack*
📍 *Iloilo City, Philippines*
🔗 [Portfolio](https://jjayfabor.github.io) | [GitHub](https://github.com/JjayFabor) | [LinkedIn](https://www.linkedin.com/in/jjayfabor/)
