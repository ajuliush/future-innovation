<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Install Instructions

1. **Clone the repository:**

    ```bash
    git clone (https://github.com/ajuliush/future-innovation.git)
    ```

2. **Navigate to the project directory:**

    ```bash
    cd future-innovation/
    ```

3. **Install PHP dependencies using Composer:**

    ```bash
    composer install
    ```

4. **Copy the `.env.example` file to create a new `.env` file:**

    ```bash
    cp .env.example .env
    ```

5. **Update the `.env` file with your database credentials.**

6. **Generate an application key:**

    ```bash
    php artisan key:generate
    ```

7. **Install JavaScript dependencies using npm:**

    ```bash
    npm install
    ```

8. **Compile assets:**

    ```bash
    npm run dev
    ```

9. **Start the Laravel development server:**

    ```bash
    php artisan serve
    ```

Now, you should be able to access your Laravel application at [http://localhost:8000](http://localhost:8000) in your web browser.

**Note:** Adjust the port number in the last step (`php artisan serve`) if needed. Ensure that your web server and database server are running as expected.


