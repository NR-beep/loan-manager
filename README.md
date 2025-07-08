# Loan Management System

A Laravel-based application for managing loans and generating amortization schedules. Built with Laravel, Livewire, and MySQL.

## 🚀 Features

- Create and manage loans
- Automatic amortization schedule generation
- Monthly payment breakdown (principal, interest, total payment, balance)
- Livewire-powered interactive UI
- Unit tests for amortization calculation

---

## ⚙️ Setup Instructions

### 1. Clone the repository

git clone https://github.com/NR-beep/loan-manager.git
cd loan-manager

### 2. Install dependencies
composer install

If using frontend assets (optional):

npm install
npm run dev

### 3. Configure environment
cp .env.example .env
php artisan key:generate

Edit .env and set your database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loan_manager
DB_USERNAME=root
DB_PASSWORD=

### 4. Run migrations
php artisan migrate

💻 Usage

Start the development server:

php artisan serve

Visit http://127.0.0.1:8000


Create user or login with existing
Creating a Loan
Enter loan amount, interest rate, loan term (months), and start date.
Submit to generate amortization schedule.
Viewing the Amortization Schedule
View detailed monthly breakdowns: principal, interest, total payment, remaining balance.

🧱 Technologies
Laravel 10+
Livewire
PostgreSQL

📁 Important Directories

app/
├── Http/
│   └── Livewire/
├── Models/
│   ├── Loan.php
│   └── Payment.php
tests/
└── Unit/
    └── LoanManagerTest.php
resources/views/loan/


