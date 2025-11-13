# 📌 Task Management API  
### Laravel 12 + Sanctum + Redis + Notifications + Custom Commands

A complete REST API for managing tasks and comments, built using **Laravel 12**, **Laravel Sanctum**, **Redis caching**, and **email notifications**.

# 🚀 Features

### 🔐 Authentication (Sanctum)
- Register users  
- Login users  
- Token-based authentication  
- Protect routes with `auth:sanctum`

### 📋 Tasks Module
- Create new tasks  
- View task details  
- Update existing tasks  
- Delete tasks  
- Optional status filter  
- Uses API Resource formatting  
- Tasks belong to a user

### 💬 Comments Module
- Comment on tasks  
- Optional file attachment  
- Files stored locally in `storage/comments`  
- Each comment belongs to a task and user  
- API Resource formatting  

### ✉️ Email Notifications
- When a comment is added, the task owner receives an email notification  
- Uses Laravel Notifications  
- For development, emails are logged in `storage/logs/laravel.log`

### ⚡ Redis Caching
- GET `/api/tasks` cached for performance  
- Cache auto invalidates when tasks are created, updated, or deleted  
- Uses Docker Redis container

### 🛠 Custom Artisan Commands
- `php artisan tasks:per-user` → Number of tasks per user  
- `php artisan comments:per-user` → Number of comments per user  

---

# 📦 Installation & Setup

## 1️⃣ Clone the project

git clone https://github.com/AhmadShamouka/task-management-api.git
cd task


project duration 4hours 30 min
