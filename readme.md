# Property API Documentation

## Installation

### Requirements
- PHP 8.2+
- Composer
- Symfony CLI
- Redis (for caching)

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/czarist/jwt-symfony-api.git
   cd property-api
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Generate JWT keys:
   ```bash
   mkdir -p config/jwt
   openssl genrsa -out config/jwt/private.pem -aes256 4096
   openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
   ```
4. Set the JWT passphrase in `.env`:
   ```
   JWT_PASSPHRASE=your_passphrase
   ```
5. Clear the cache:
   ```bash
   php bin/console cache:clear
   ```
6. Start the server:
   ```bash
   symfony server:start
   ```

## Overview
This API provides endpoints for authentication and retrieving property data. Authentication is managed using JWT tokens.

## Authentication

### Login

**Endpoint:**
```
POST /api/login
```

**Request Body:**
```json
{
  "email": "admin",
  "password": "admin"
}
```

**Response:**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Error Responses:**
- `401 Unauthorized` - Invalid credentials
  ```json
  {
    "code": 401,
    "message": "Invalid credentials."
  }
  ```

## Property Data

### Get Properties

**Endpoint:**
```
GET /properties?page={page}&limit={limit}
```

**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response:**
```json
{
  "page": 1,
  "limit": 2,
  "total": 4,
  "data": [
    {
      "id": 1,
      "address": "123 Street",
      "price": 100000,
      "source": "source1"
    },
    {
      "id": 2,
      "address": "456 Road",
      "price": 200000,
      "source": "source1"
    }
  ]
}
```

**Error Responses:**
- `401 Unauthorized` - Token missing or invalid
  ```json
  {
    "code": 401,
    "message": "Full authentication is required to access this resource."
  }
  ```
- `403 Forbidden` - Token is valid but user lacks permissions
  ```json
  {
    "code": 403,
    "message": "Access denied."
  }
  ```
- `400 Bad Request` - Invalid parameters (e.g., non-numeric page/limit)
  ```json
  {
    "code": 400,
    "message": "Invalid request parameters."
  }
  ```

## Notes
- The token obtained from `/api/login` must be included in the `Authorization` header for all protected endpoints.
- Token expiration is determined by server settings.
- Use the `page` and `limit` parameters for paginated responses.

