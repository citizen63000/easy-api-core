# Easy API Core

A Symfony bundle providing core utilities for REST API development. This bundle offers tools for entity configuration, form serialization, command abstractions, and various utilities to accelerate API development.

## Installation in a third-party project

### 1. Installation via Composer

```bash
composer require citizen63000/easy-api-core
```

### 2. Bundle Configuration

Add the bundle to `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    EasyApiCore\EasyApiCore::class => ['all' => true],
];
```

### 3. Configuration

Create the file `config/packages/easy_api_core.yaml`:

```yaml
easy_api_core:
    user_class: 'App\Entity\User'    # Your user entity class
```

## Features

### Entity Configuration

Load and introspect entity metadata from Doctrine annotations/attributes or database schema:

```php
use EasyApiCore\Util\Entity\EntityConfigLoader;

// From entity class
$config = EntityConfigLoader::createEntityConfigFromEntityFullName(App\Entity\Product::class);

// From database table
$config = EntityConfigLoader::createEntityConfigFromDatabase($entityManager, 'Product', 'products');
```

### Form Serialization

Serialize Symfony forms for frontend consumption (useful for dynamic form generation):

```php
use EasyApiCore\Util\Forms\FormSerializer;

$serializer = new FormSerializer($formFactory, $router, $doctrine);
$form = $formFactory->create(ProductType::class);
$serializedForm = $serializer->normalize($form);
```

### Abstract Command

Base class for console commands with Doctrine and container access:

```php
use EasyApiCore\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->getRepository(Product::class);
        $this->writeLog($output, 'Processing...');

        return Command::SUCCESS;
    }
}
```

### Core Utilities Trait

Provides shortcuts for common operations in controllers or services:

```php
use EasyApiCore\Util\CoreUtilsTrait;

class MyService
{
    use CoreUtilsTrait;

    public function saveProduct(Product $product): void
    {
        $this->persistAndFlush($product);
    }
}
```

### String Utilities

Case conversion and pluralization helpers:

```php
use EasyApiCore\Util\String\CaseConverter;
use EasyApiCore\Util\String\Inflector;

// Case conversion
$camelCase = CaseConverter::convertSnakeCaseToCamelCase('my_variable'); // myVariable
$snakeCase = CaseConverter::convertCamelCaseToSnakeCase('myVariable');  // my_variable

// Pluralization
$plural = Inflector::pluralize('product');  // products
$singular = Inflector::singularize('products'); // product
```

### API Problem Responses

Standardized error responses following RFC 7807:

```php
use EasyApiCore\Util\ApiProblem;
use Symfony\Component\HttpFoundation\Response;

$error = new ApiProblem(Response::HTTP_NOT_FOUND, 'Product not found');
```

## Available Components

| Component | Description |
|-----------|-------------|
| `AbstractCommand` | Base console command with Doctrine access |
| `CoreUtilsTrait` | Common utilities for services/controllers |
| `EntityConfigLoader` | Entity metadata introspection |
| `DatabaseConfigurationLoader` | Database schema introspection |
| `FormSerializer` | Symfony form serialization |
| `CaseConverter` | String case conversion utilities |
| `Inflector` | Word pluralization/singularization |
| `ApiProblem` | Standardized API error responses |
| `MimeUtil` | MIME type utilities |
| `DirectoryManipulator` | Directory operations |

## Local Development with Docker

### Prerequisites

- Docker and Docker Compose installed

### Installation for Development

1. **Clone the repository:**
   ```bash
   git clone https://github.com/citizen63000/easy-api-core.git
   cd easy-api-core
   ```

2. **Start the Docker environment:**
   ```bash
   docker compose up -d
   ```

3. **Install dependencies:**
   ```bash
   docker compose exec app composer install
   ```

### Development Commands

#### Check code style
```bash
docker compose exec app vendor/bin/php-cs-fixer fix --dry-run --diff
```

#### Fix code style
```bash
docker compose exec app vendor/bin/php-cs-fixer fix
```

#### Access the container for debugging
```bash
docker compose exec app sh
```

### Development Project Structure

```
easy-api-core/
├── EasyApiCore.php              # Bundle definition
├── DependencyInjection/         # Symfony configuration
│   ├── EasyApiCoreExtension.php
│   └── Configuration.php
├── Framework/                   # Source code
│   ├── Command/                 # Console commands
│   │   └── AbstractCommand.php
│   ├── Model/                   # Data models
│   │   ├── EntityConfiguration.php
│   │   └── EntityField.php
│   ├── Form/                    # Form types
│   │   └── Type/
│   │       └── AbstractApiType.php
│   └── Util/                    # Utilities
│       ├── CoreUtilsTrait.php
│       ├── ApiProblem.php
│       ├── Entity/              # Entity utilities
│       ├── Forms/               # Form utilities
│       ├── String/              # String utilities
│       └── File/                # File utilities
├── docker-compose.yml           # Docker configuration
├── Dockerfile                   # PHP Docker image
└── README.md
```

### Contribution workflow

1. Create a feature branch
2. Develop your feature
3. Check code style with `php-cs-fixer`
4. Commit changes
5. Create a Pull Request

## Compatibility

- PHP >= 8.3
- Symfony 6.4, 7.x, 8.0
- Doctrine ORM 2.7+ / 3.x

## Support

- **Issues:** [GitHub Issues](https://github.com/citizen63000/easy-api-core/issues)
- **Documentation:** This README and docblocks in the code

## License

MIT License - see [LICENSE](LICENSE) file for details.
