# DPASS-REST

DPASS-REST is a RESTful API implementation built on the Slim Framework for the DPASS (Digital Password) system.

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

The application reads database configuration in the following priority order:

1. **Environment variables** (highest priority)
2. **Hardcoded defaults** in `DatabaseConfiguration.php` (fallback)

This approach ensures:
- **Security**: Sensitive credentials are not baked into the Docker image
- **Flexibility**: Same image can be used across different environments
- **Backward compatibility**: Existing deployments continue to work without changes

### Configuration File

For non-Docker deployments, you can still use the traditional configuration file approach:

1. Copy `DatabaseConfiguration.default.php` to `DatabaseConfiguration.php`
2. Edit `DatabaseConfiguration.php` with your database settings
3. The application will use the values from this file if environment variables are not set

## Development

For development setup and contribution guidelines, please refer to the main [README.markdown](README.markdown).

## License

The Slim Framework is released under the MIT public license.
