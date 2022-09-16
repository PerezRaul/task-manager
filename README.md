# Task Manager Project

This is the main repo of the task manager.

## Table of contents

- [Setup in local development](#setup-in-local-development)
    - [Requirements](#requirements)
    - [Installation](#installation)
- [Code analysis](#code-analysis)
    - [All code analysis](#all-code-analysis)
- [Code explanation](#code-explanation)

## Setup in local development

### Requirements

- [Docker repository](https://github.com/PerezRaul/docker) ( `task-manager` branch )

### Installation
1. Add `127.0.0.1 task-manager.localhost` on `/etc/hosts`.
2. Clone repository inside `~/Sites`:
3. Copy the file **.env.example** to **.env**.
    ```shell
    > cp .env.example .env
    ```
4. Go inside the workspace with the following command:
    ```shell
    > dockerbash
    ```
5. Execute the following commands on task manager folder _/var/www/task-manager_:
    ```shell
    > composer install
    > php artisan migrate:fresh --seed
    ```

## Code analysis

### All code analysis

```shell
> sh analysis.sh
```

## Code Explanation
### */app/Http/Controllers*
Path where we find the controllers to the routes of the endpoints that we have configured.

### */app/Http/Requests*
Path where we find the rules of the form requests of each controller.

### */app/Http/Providers*
Path where we find providers that we want to inject in the project.

### */config/shared/ioc.php*
Path where we are going to resolve the dependencies. For example when calling TaskRepository the dependency will be EloquentTaskRepository (because we use eloquent in this project).

### */database*
Path where we are going to find the migrations and seeders of the database.

### */etc*
Route where we are going to find the endpoints that we can execute to collect the data. We can run them through PHPStorm with the `HTTP Client` plugin. Or we can run the following curl calls through Postman.
```shell
# Get all tasks in the database
curl -X GET --location "http://task-manager.localhost/tasks" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d "{
            \"per_page\": 20,
            \"page\": 1
        }"

# Put task with new uuid
curl -X PUT --location "http://task-manager.localhost/tasks/3be42fc8-afc4-4ef0-b1e5-2581fae2b404" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d "{
            \"title\": \"Test Example\",
            \"is_finished\": true
        }"

# Get task by uuid in the database
curl -X GET --location "http://task-manager.localhost/tasks/3be42fc8-afc4-4ef0-b1e5-2581fae2b404" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d "{
            \"per_page\": 20,
            \"page\": 1
        }"

# Delete task by uuid in the database
curl -X DELETE --location "http://task-manager.localhost/tasks/3be42fc8-afc4-4ef0-b1e5-2581fae2b404" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json"
```

### */routes/web.php*
Path where we find the application routes.

### */src/Shared*
Path where we find the code structure that can be shared between other modules.

### */src/Tasks*
Path where we find the code structure of the initial project. Where the valueobjects of the tasks are generated, the queries with the database, the repository....
