# DPASS-REST

DPASS-REST is a RESTful API implementation built on the Slim Framework.

## Getting Started

### Database Setup

The database schema is provided in the `dpass-rest.sql` file. To set up the database:

```bash
# Create the database
CREATE DATABASE `dpass-lite` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# Import the schema
mysql -u username -p dpass-lite < dpass-rest.sql
```

The schema includes the following tables:
- **api_keys** - API authentication key management
- **configurations** - System-wide configuration parameters
- **log** - Application activity and audit log
- **records** - Main records table for entry data
- **record_approvals** - Approval workflow for records
- **record_status** - Status tracking for records
- **staffs** - Staff information and work schedules

## Docker Deployment

This application is available as a Docker image: `wengpang/dpass`

### Environment Variables

As of version 1.2.0, the application supports configuration through environment variables, making it suitable for containerized deployments.

The following environment variables are supported for database configuration:

| Environment Variable | Description | Default Value |
|---------------------|-------------|---------------|
| `DB_HOST` | Database host address | `127.0.0.1` |
| `DB_NAME` | Database name | `dpass-lite` |
| `DB_USER` | Database username | `dpass-lite` |
| `DB_PASSWORD` | Database password | `dpass-lite` |

### Usage Examples

#### Docker Run

```bash
docker run -d \
  -p 8080:8080 \
  -e DB_HOST=mysql-server \
  -e DB_NAME=your_database \
  -e DB_USER=your_username \
  -e DB_PASSWORD=your_password \
  wengpang/dpass:latest
```

#### Docker Compose

Create a `docker-compose.yml` file:

```yaml
version: '3.8'

services:
  dpass:
    image: wengpang/dpass:latest
    ports:
      - "8080:8080"
    environment:
      DB_HOST: mysql-server
      DB_NAME: your_database
      DB_USER: your_username
      DB_PASSWORD: your_password
```

Then run:

```bash
docker-compose up -d
```

#### Using .env File (Recommended for Production)

Create a `.env` file:

```env
DB_HOST=mysql-server
DB_NAME=your_database
DB_USER=your_username
DB_PASSWORD=your_password
```

**Important:** Add `.env` to your `.gitignore` file to prevent committing sensitive information.

Create a `docker-compose.yml` file:

```yaml
version: '3.8'

services:
  dpass:
    image: wengpang/dpass:latest
    ports:
      - "8080:8080"
    env_file:
      - .env
```

### Database Configuration

The application reads database configuration in the following order (preferred method listed first):

1. **Environment variables** (preferred method)
2. **Hardcoded defaults** in `DatabaseConfiguration.php` (fallback)

This approach ensures:
- **Security**: Sensitive credentials are not baked into the Docker image
- **Flexibility**: Same image can be used across different environments
- **Backward compatibility**: Existing deployments continue to work without changes

### Configuration File

The `DatabaseConfiguration.php` file is included in the repository with environment variable support and safe default values. The application will automatically use environment variables when they are set, overriding any hardcoded values.

For local development or non-Docker deployments, you can edit `DatabaseConfiguration.php` directly to change the default values if needed.

## Development

### System Requirements

You need **PHP >= 5.3.0**. If you use encrypted cookies, you'll also need the `mcrypt` extension.
