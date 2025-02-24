# Developer Reference for WC Price History

## Commands
- **Lint/Analyze**: `vendor/bin/phpstan analyse`
- **Run all tests**: `vendor/bin/phpunit`
- **Run single test**: `vendor/bin/phpunit tests/phpunit/HistoryStorageTest.php` or `vendor/bin/phpunit --filter=test_method_name`
- **Install dependencies**: `composer install` and `npm install`

## Code Style
- **Namespace**: Use `PriorPrice` namespace for all plugin files
- **Naming**: Classes use PascalCase, methods/variables use camelCase
- **Structure**: Follow PSR-4 autoloading in app/PriorPrice/
- **Testing**: Use WP_Mock for WordPress function mocks
- **Types**: Type hints encouraged, PHPStan level 8 is enforced
- **Comments**: PHPDoc format for classes and methods, especially hooks
- **Error handling**: Use WordPress error handling patterns (WP_Error)
- **Hook naming**: Use 'wc_price_history_' prefix for custom hooks
- **Format**: Tab indentation, avoid trailing whitespace

## Custom Rules
- RegisterHooks method must be called when class is instantiated (PHPStan custom rule)