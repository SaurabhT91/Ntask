Laravel Task Management API

This project is a Task Management API developed using PHP (Laravel) and MySQL. The API allows users to manage tasks, including creating, updating, deleting, and viewing tasks.
Getting Started
Prerequisites

    PHP >= 7.3
    Composer
    MySQL

Installation

    Clone the repository:

bash

git clone https://github.com/SaurabhT91/Ntask.git

    Install dependencies using Composer:

bash

composer install

    Set up environment variables:
        Copy the .env.example file to .env:

    bash

cp .env.example .env

    Configure the database connection in the .env file:

env

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ntask
    DB_USERNAME=<your-database-username>
    DB_PASSWORD=<your-database-password>

    Run migrations and seeders to set up the database:

bash

php artisan migrate:fresh --seed

    Serve the application:

bash

php artisan serve

    Access the API at http://localhost:8000.

API Documentation

You can find the Postman collection for API endpoints here.
Routes

    POST /login: Authenticate user.
    GET /tasks: Get all tasks.
    POST /tasks: Create a new task.
    GET /tasks/list: Get filtered list of tasks.
    PUT /tasks/{id}: Update a task.
    DELETE /tasks/{id}: Delete a task.
    PUT /tasks/{taskId}/change-status: Change task status.
    GET /user-list: Get list of users.
    GET /fetch-user-list: Fetch list of users.
    POST /assign-task: Assign task to user(s).
    DELETE /unassign-task/{userId}/{taskId}: Unassign task from user.
    GET /user/{userId}/tasks: Get tasks assigned to a user.
    GET /tasks/assigned-to-current-user: Get tasks assigned to currently logged-in user.

Contributors

    Saurabh Tidgam

Feel free to contribute by submitting bug reports, feature requests, or pull requests.
