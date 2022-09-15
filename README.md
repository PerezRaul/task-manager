# Task Manager Project

This is the main repo of the task manager.

## Table of contents

- [Setup in local development](#setup-in-local-development)
    - [Requirements](#requirements)
    - [Installation](#installation)
- [Code analysis](#code-analysis)
    - [All code analysis](#all-code-analysis)

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
