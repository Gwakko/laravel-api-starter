# Laravel API Starter

Task management REST API with queue-based notifications. Demonstrates Laravel best practices: Form Requests, Events, Listeners, Jobs, and Eloquent relationships.

## Stack

- **Laravel 12** + PHP 8.5
- **PostgreSQL** — relational storage
- **Redis** — queue driver, caching
- **Sanctum** — API token authentication
- **Docker Compose** — app + queue worker + PostgreSQL + Redis

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate

# Run with Docker
docker compose up -d
php artisan migrate

# Or run locally
php artisan serve
php artisan queue:work  # separate terminal
```

## API Endpoints

### Projects
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/projects` | List projects (paginated) |
| POST | `/api/projects` | Create project |
| GET | `/api/projects/{id}` | Get project with tasks |
| PUT | `/api/projects/{id}` | Update project |
| DELETE | `/api/projects/{id}` | Delete project |

### Tasks
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/projects/{id}/tasks` | List tasks (filterable) |
| POST | `/api/projects/{id}/tasks` | Create task |
| GET | `/api/projects/{id}/tasks/{taskId}` | Get task |
| PUT | `/api/projects/{id}/tasks/{taskId}` | Update task |
| DELETE | `/api/projects/{id}/tasks/{taskId}` | Delete task |

Query params for task listing: `?status=pending&priority=high`

## Architecture

```
app/
├── Http/
│   ├── Controllers/     # ProjectController, TaskController
│   └── Requests/        # Form Request validation
├── Models/              # Eloquent models with relationships
├── Events/              # TaskCompleted event
├── Listeners/           # SendTaskNotification → dispatches job
└── Jobs/                # ProcessTaskNotification (queued)
```

**Event flow:** Task marked done → `TaskCompleted` event → `SendTaskNotification` listener → `ProcessTaskNotification` job (queued via Redis)

## TODO

- [ ] RabbitMQ queue driver
- [ ] API documentation (Scribe/Swagger)
- [ ] Task assignment to multiple users
- [ ] Webhook notifications
- [ ] Feature tests
