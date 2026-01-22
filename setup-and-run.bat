@echo off
echo Setting up Leave Management Application...
echo ========================================

echo.
echo Step 1: Generate application key...
php artisan key:generate

echo.
echo Step 2: Create SQLite database...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo Database file created.
) else (
    echo Database file already exists.
)

echo.
echo Step 3: Run database migrations...
php artisan migrate

echo.
echo Step 4: Seed database with sample data...
php artisan db:seed

echo.
echo Step 5: Build frontend assets...
npm run build

echo.
echo Step 6: Starting Laravel development server...
echo.
echo ========================================
echo Leave Management App is now running!
echo ========================================
echo.
echo Open your browser and go to: http://localhost:8000
echo.
echo Default login credentials:
echo Email: test@example.com
echo Password: password
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve --host=0.0.0.0 --port=8000