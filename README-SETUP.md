# Leave Management Application - Setup Guide

## 🚀 Quick Start

### Automated Setup (Recommended)

1. Wait for Composer to finish installing dependencies
2. Double-click `setup-and-run.bat` to automatically:
    - Generate application key
    - Create SQLite database
    - Run migrations
    - Seed with sample data
    - Start the development server

### Manual Setup

If you prefer to set up manually:

```bash
# 1. Generate application key
php artisan key:generate

# 2. Create SQLite database (if not exists)
# The app is configured to use SQLite by default

# 3. Run database migrations
php artisan migrate

# 4. Seed database with sample data
php artisan db:seed

# 5. Build frontend assets (optional)
npm run build

# 6. Start the development server
php artisan serve
```

## 🌐 Access the Application

Once setup is complete, open your browser and go to:
**http://localhost:8000**

### Default Login Credentials

-   **Email**: `test@example.com`
-   **Password**: `password`

## ✨ Features Available

### For All Users:

-   **Dashboard**: Overview of leave balances and recent requests
-   **Leave Requests**: Submit, view, edit, and cancel leave requests
-   **Leave Balance**: Track remaining leave days by type
-   **Responsive Design**: Works on desktop and mobile

### For Administrators:

-   **Leave Approvals**: Approve or reject pending requests
-   **Leave Type Management**: Create and manage leave types
-   **User Management**: Oversee all leave activities

## 📋 Sample Leave Types Included

The application comes pre-configured with these leave types:

-   **Annual Leave** (20 days/year)
-   **Sick Leave** (10 days/year)
-   **Casual Leave** (7 days/year)
-   **Maternity Leave** (90 days)
-   **Paternity Leave** (10 days)
-   **Emergency Leave** (3 days)

## 🛠️ Customization

### Adding New Leave Types

1. Go to `/leave-types` (admin only)
2. Click "Create Leave Type"
3. Set name, code, days per year, and approval requirements

### Modifying Leave Rules

-   Edit leave types to change days allocated
-   Set maximum consecutive days
-   Configure approval requirements

## 🔧 Troubleshooting

### Common Issues:

**"Composer dependencies not installed"**

-   Wait for `composer install` to complete
-   Or run `composer install --no-dev` manually

**"Database connection error"**

-   Ensure `database/database.sqlite` exists
-   Check `.env` file for correct database configuration

**"Port 8000 already in use"**

-   Close other applications using port 8000
-   Or use: `php artisan serve --port=8001`

## 📝 Development Notes

-   **Framework**: Laravel 11
-   **Database**: SQLite (file-based, no server required)
-   **Frontend**: Blade templates with Tailwind CSS
-   **Authentication**: Laravel Breeze (built-in)

## 🔒 Security Features

-   CSRF protection on all forms
-   Password hashing
-   Role-based access control
-   Input validation and sanitization
-   SQL injection prevention

---

**Need help?** Check the Laravel documentation at https://laravel.com/docs
