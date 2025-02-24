# Blog Platform API

A RESTful API for managing blog posts built with Laravel. This API provides endpoints for creating, reading, updating, and deleting blog posts, along with search functionality.

## Features

- CRUD operations for blog posts
- Search functionality to filter posts
- Swagger/OpenAPI documentation
- Docker containerization
- Comprehensive test suite

## Requirements

- Docker
- Docker Compose

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd php-blog-platform-api
```

2. Start the Docker containers:
```bash
docker-compose up -d
```

3. Install dependencies:
```bash
docker exec laravel-app composer install
```

4. Run database migrations:
```bash
docker exec laravel-app php artisan migrate
```

## API Documentation

The API documentation is available via Swagger UI at:
```
http://localhost:8000/api/documentation
```

### Available Endpoints

- `POST /api/posts` - Create a new blog post
- `GET /api/posts` - Get all blog posts (with optional search)
- `GET /api/posts/{id}` - Get a specific blog post
- `PUT /api/posts/{id}` - Update a blog post
- `DELETE /api/posts/{id}` - Delete a blog post

## Testing

Run the test suite:
```bash
docker exec laravel-app php artisan test
```

## Database Configuration

The application uses MySQL with the following default configuration:
- Database: `laravel`
- Username: `laravel`
- Password: `secret`

These can be modified in the `.env` file.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
