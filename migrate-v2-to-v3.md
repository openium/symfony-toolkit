# Migration V2 to V3

## What's changing

The ExceptionFormatService has changed.
Now you need to add a new service in your code to extend the default service and override the service. 

## Code changes

### Before

Nothing special in your code

### After

- In your project, create an ExceptionFormatService to handle exceptions you want
- You have to extend the default toolkit service `Openium\SymfonyToolKitBundle\Service\ExceptionFormatService`

Example :
```php
<?php
namespace App\Service;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService as BaseExceptionFormatService;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;

class ExceptionFormatService extends BaseExceptionFormatService implements ExceptionFormatServiceInterface {

    public function genericExceptionResponse(Exception $exception): array
    {
        if (is_a($exception, "Symfony\Component\Security\Core\Exception\AuthenticationException")) {
            $code = Response::HTTP_UNAUTHORIZED;
            $text = Response::$statusTexts[$code];
            $message = $text;
        } elseif (
            is_a($exception, "Firebase\Auth\Token\Exception\ExpiredToken")
            || is_a($exception, "Firebase\Auth\Token\Exception\IssuedInTheFuture")
            || is_a($exception, "Firebase\Auth\Token\Exception\InvalidToken")
        ) {
            // Firebase part
            $code = Response::HTTP_UNAUTHORIZED;
            /* @phpstan-ignore-next-line */
            $text = $exception->getMessage();
            $message = $text;
        } else {
            $code = $this->getStatusCode($exception);
            $text = $this->getStatusText($exception);
            $message = null;
        }
        return [$code, $text, $message];
    }
```

You can add or remove cases as you like. 

- To override the service of the bundle, you have to add your own service in config/services.yaml  
For example:
```yaml
    openium_symfony_toolkit.exception_format:
        class: App\Service\ExceptionFormatService
        arguments:
            - '@kernel'
        public: true
  ```
  
