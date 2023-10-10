# Task Manager API using Laravel

This project is a simple RESTful API for managing tasks, built with Laravel.
## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/aqibhassan/task-manager-using-laravel.git
    ```

2. Change directory:
    ```bash
    cd your-project-name
    ```

3. Install dependencies:
    ```bash
    composer install
    composer require laravel/passport 
    php artisan migrate 
    php artisan passport::install        
    ```

4. Copy the `.env.example` file to create your own configuration file and also create database change the name of database in env file:
    ```bash
    cp .env.example .env 
    ```

5. Generate an application key:
    ```bash
    php artisan key:generate
    ```

6. Run migrations (and seeders if any):
    ```bash
    php artisan migrate --seed

    ```

7. Start the local development server:
    ```bash
    php artisan serve
    ```
8. Testing:
    ```bash
   ./vendor/bin/phpunit
    ```
## Features

- CRUD operations for task
- Pagination for task listings
- Incorporated pagination for tasks listing.
- Implemented passport authentication to secure the API endpoints, using Token.
- Implemented data validation for requests
- Detailed documentation is available in postman file, outlining endpoints, accepted HTTP methods, and expected request/response formats.




