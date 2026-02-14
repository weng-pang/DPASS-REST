# DPASS-REST

DPASS-REST is a RESTful API implementation built on the Slim Framework.

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

The `DatabaseConfiguration.php` file is included in the repository with environment variable support built-in. If you need to customize the default values:

1. Edit `DatabaseConfiguration.php` directly, or
2. Copy `DatabaseConfiguration.default.php` to `DatabaseConfiguration.php` and modify as needed

**Note:** The application will use environment variables if they are set, regardless of the hardcoded values in the configuration file.

## Development

### System Requirements

You need **PHP >= 5.3.0**. If you use encrypted cookies, you'll also need the `mcrypt` extension.

### Installation

You may install the Slim Framework with Composer (recommended) or manually.

[Read how to install Slim](http://docs.slimframework.com/#Installation)

### Hello World Tutorial

Instantiate a Slim application:

```php
$app = new \Slim\Slim();
```

Define a HTTP GET route:

```php
$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});
```

Run the Slim application:

```php
$app->run();
```

## Slim Framework Features

* Powerful router
    * Standard and custom HTTP methods
    * Route parameters with wildcards and conditions
    * Route redirect, halt, and pass
    * Route middleware
* Resource Locator and DI container
* Template rendering with custom views
* Flash messages
* Secure cookies with AES-256 encryption
* HTTP caching
* Logging with custom log writers
* Error handling and debugging
* Middleware and hook architecture
* Simple configuration

## Documentation

<http://docs.slimframework.com/>

## How to Contribute

### Pull Requests

1. Fork the Slim Framework repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the **develop** branch

It is very important to separate new features or improvements into separate feature branches, and to send a pull request for each branch. This allows me to review and pull in new features or improvements individually.

### Style Guide

All pull requests must adhere to the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) standard.

### Unit Testing

All pull requests must be accompanied by passing unit tests and complete code coverage. The Slim Framework uses `phpunit` for testing.

[Learn about PHPUnit](https://github.com/sebastianbergmann/phpunit/)

## Community

### Forum and Knowledgebase

Visit Slim's official forum and knowledge base at <http://help.slimframework.com> where you can find announcements, chat with fellow Slim users, ask questions, help others, or show off your cool Slim Framework apps.

### Twitter

Follow [@slimphp](http://www.twitter.com/slimphp) on Twitter to receive news and updates about the framework.

## Author

The Slim Framework is created and maintained by [Josh Lockhart](http://www.joshlockhart.com). Josh is a senior web developer at [New Media Campaigns](http://www.newmediacampaigns.com/). Josh also created and maintains [PHP: The Right Way](http://www.phptherightway.com/), a popular movement in the PHP community to introduce new PHP programmers to best practices and good information.

## License

The Slim Framework is released under the MIT public license.

<http://www.slimframework.com/license>
