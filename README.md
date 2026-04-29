# 🚀 TeamFlow - Team Task Management SaaS

TeamFlow is a full-stack SaaS application designed to help teams collaborate, manage tasks, and communicate in real-time.

## ✨ Features

* 🔐 Authentication (Email, Google, GitHub)
* 👥 Create or Join Teams
* 📋 Kanban Board (To-Do, In Progress, Done)
* 🧑‍💼 Team Roles (Admin / Members)
* 📨 Join Requests & Approval System
* 💬 Real-time Team Chat (Channels & Private Messages)
* 🔔 Real-time Notifications
* 💳 Subscription System (Free vs Pro - Stripe Integration)

## 🧠 Tech Stack

### Frontend

* Vue.js
* Tailwind CSS

### Backend

* Laravel
* Laravel Reverb (WebSockets)

### Database

* SQLite

### Payments

* Stripe

## 🔥 Key Highlights

* Built completely from scratch (solo project)
* Real-time features without page refresh
* Scalable SaaS architecture
* Clean UI with modern UX

## 📸 Screenshots

(Add screenshots here)

## ⚙️ Setup Instructions

```bash
git clone https://github.com/your-username/teamflow.git
cd teamflow

# Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Frontend
cd frontend
npm install
npm run dev
```

## 📬 Contact

* GitHub: https://github.com/your-username
* Email: [your-email@example.com](mailto:your-email@example.com)
