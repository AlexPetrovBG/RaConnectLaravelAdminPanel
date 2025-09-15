# RaConnect Laravel Admin Panel

A Laravel 12 application with user management, roles/permissions, and API authentication using Sanctum and Spatie Permission.

## Features

- **User Authentication**: Laravel Breeze with email verification
- **API Authentication**: Laravel Sanctum for SPA/API tokens
- **Roles & Permissions**: Spatie Laravel Permission package
- **Admin Panel**: Filament v4 with Shield for role-based access
- **Security**: Rate limiting, CORS, and comprehensive testing

## Stack

- Laravel 12
- PHP 8.2+
- PostgreSQL
- Laravel Sanctum
- Spatie Laravel Permission
- Filament v4 + Shield

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd RaConnectLaravelAdminPanel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with PostgreSQL credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=raconnect
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

6. **Create admin user**
   ```bash
   php artisan make:admin-user admin@example.com "Admin User" "SecurePassword123!"
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the server**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

- `POST /api/login` - Login with email and password
- `POST /api/logout` - Logout (requires authentication)
- `GET /api/me` - Get current user info (requires authentication)

### Projects

- `GET /api/projects` - List projects (requires `projects.view` permission)
- `POST /api/projects` - Create project (requires `projects.edit` permission)

## API Usage Examples

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "SecurePassword123!"}'
```

Response:
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  },
  "abilities": ["*"]
}
```

### Get Current User
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer 1|abc123..."
```

### List Projects
```bash
curl -X GET http://localhost:8000/api/projects \
  -H "Authorization: Bearer 1|abc123..."
```

### Create Project
```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"name": "New Project", "description": "Project description"}'
```

## Admin Panel

Access the admin panel at `http://localhost:8000/admin` using the admin credentials created above.

## Roles and Permissions

### Default Roles
- **admin**: Full access to all permissions
- **manager**: Can view and edit projects
- **viewer**: Can only view projects

### Default Permissions
- `projects.view`: View projects
- `projects.edit`: Create, update, and delete projects
- `users.manage`: Manage users

## Testing

Run the test suite:
```bash
php artisan test
```

## Security Features

- Rate limiting on login endpoint (60 requests per minute)
- CORS configuration for API access
- Email verification for user accounts
- Role-based access control
- API token authentication

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
