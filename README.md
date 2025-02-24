# Blog Platform API

A RESTful API for managing blog posts built with Laravel. This API provides endpoints for creating, reading, updating, and deleting blog posts, along with search functionality.

This project is based on the [Blogging Platform API](https://roadmap.sh/projects/blogging-platform-api) project from roadmap.sh.

## Features

- CRUD operations for blog posts
- Search functionality to filter posts
- Swagger/OpenAPI documentation
- Docker containerization
- Comprehensive test suite
- API versioning
- Rate limiting
- Pagination
- CORS support

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

Interactive API documentation is available via Swagger UI at:
```
http://localhost:8000/api/documentation
```

### API Endpoints

All endpoints are prefixed with `/api/v1`

#### Posts

- **GET** `/posts`
  - Get all posts with pagination (10 per page)
  - Query Parameters:
    - `term` (optional): Search term to filter posts
    - `page` (optional): Page number for pagination
  - Response: 200 OK
  ```json
  {
    "data": [
      {
        "id": 1,
        "title": "Post Title",
        "content": "Post content",
        "category": "Category",
        "tags": ["tag1", "tag2"],
        "created_at": "2024-02-23T12:00:00Z",
        "updated_at": "2024-02-23T12:00:00Z"
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/v1/posts?page=1",
      "last": "http://localhost:8000/api/v1/posts?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "total": 1
    }
  }
  ```

- **POST** `/posts`
  - Create a new post
  - Request Body:
  ```json
  {
    "title": "Post Title",
    "content": "Post content",
    "category": "Category",
    "tags": ["tag1", "tag2"]
  }
  ```
  - Response: 201 Created

- **GET** `/posts/{id}`
  - Get a specific post
  - Response: 200 OK

- **PUT** `/posts/{id}`
  - Update a post
  - Request Body: Same as POST
  - Response: 200 OK

- **DELETE** `/posts/{id}`
  - Delete a post
  - Response: 204 No Content

### Error Responses

- **404 Not Found**
  ```json
  {
    "message": "Resource not found."
  }
  ```

- **422 Unprocessable Entity**
  ```json
  {
    "message": "The given data was invalid.",
    "errors": {
      "title": ["The title field is required."],
      "content": ["The content field is required."]
    }
  }
  ```

- **429 Too Many Requests**
  ```json
  {
    "message": "Too Many Attempts."
  }
  ```

### Rate Limiting

The API is rate-limited to 60 requests per minute per IP address.

## Testing

Run the test suite:
```bash
docker exec laravel-app php artisan test
```

The test suite includes:
- CRUD operation tests
- Validation tests
- Search functionality tests
- Pagination tests
- Rate limiting tests

## Database Configuration

The application uses MySQL with the following default configuration:
- Database: `laravel`
- Username: `laravel`
- Password: `secret`

These can be modified in the `.env` file.

## Security

- CORS is configured in `config/cors.php`
- Input validation using dedicated request classes
- Rate limiting to prevent API abuse
- Standardized error responses

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
