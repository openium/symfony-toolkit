# What's changing

- In your project, create an exceptionFormatService to receive the exception you want to format.
- To override the service of the bundle, you have to add your own service in config/services.yaml<br>
For example:
```yaml
    openium_symfony_toolkit.exception_format:
        class: App\Service\ExceptionFormatService
        arguments:
            - '@kernel'
        public: true
  ```
- To use it, you have to extend the bundle in your file.<br>