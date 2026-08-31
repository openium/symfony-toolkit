# Migration V6 to V7

## What's changing

v7 does not bump the minimum Symfony/PHP version (still Symfony `^8.1`, PHP `^8.4`). It carries
API breaking changes and deprecations instead. Skim the list below: most projects only need to
act on the first two items.

| Change | Action required? |
|---|---|
| Semantic bundle configuration | **Yes**, if you override any `openium_symfony_toolkit.*` parameter |
| `DoctrineExceptionHandlerService` constructor | **Yes**, only if you instantiate it yourself instead of injecting it |
| `AtHelper` shell escaping (security fix) | Only if `$cmd` relied on shell metacharacters being interpreted |
| `ServerService` deprecated | No (still works), but migrate when convenient |
| `AbstractCommand` deprecated | No (still works), but migrate when convenient |
| `ContentExtractorUtils` deprecated | No (still works), but migrate when convenient |
| `DateStringUtils` deprecated | No (still works), but migrate when convenient |
| `DoctrineExceptionHandlerService` subclass matching fix | No, but check custom Doctrine exception subclasses |
| Upload namer / multi-field upload support | No, purely additive |

## Code changes

### Semantic configuration (breaking)

`DependencyInjection/Configuration` was previously an empty tree; the bundle's actual settings
were plain `parameters:` hardcoded in `Resources/config/services.yaml`, and any override you
declared as raw `parameters:` in your own config worked only by coincidence (by redefining the
same parameter name). This no longer works: the bundle's extension now sets these parameters
itself from a real configuration tree, and will silently override whatever you set via
`parameters:`.

#### Before

```yaml
parameters:
    openium_symfony_toolkit.public_dir: '%kernel.project_dir%/public'
    openium_symfony_toolkit.uploads_dir_name: 'uploads'
    openium_symfony_toolkit.kernel_exception_listener_enable: true
    openium_symfony_toolkit.kernel_exception_listener_path: '/api'
    openium_symfony_toolkit.kernel_exception_listener_class: 'Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener'
```

#### After

```yaml
openium_symfony_toolkit:
    uploads:
        public_dir: '%kernel.project_dir%/public'
        dir_name: 'uploads'
    kernel_exception_listener:
        enabled: true
        path: '/api'
        class: 'Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener'
```

If you never overrode any of these parameters, there is nothing to do — the defaults are
unchanged.

### DoctrineExceptionHandlerService constructor (breaking)

The constructor now requires a `Symfony\Contracts\Translation\TranslatorInterface` as a second
argument, so the default error messages can be translated. The bundle's own service definition
already passes `@translator`; **this only affects code that instantiates the class directly**
instead of injecting `DoctrineExceptionHandlerServiceInterface`.

#### Before

```php
$handler = new DoctrineExceptionHandlerService($logger);
```

#### After

```php
$handler = new DoctrineExceptionHandlerService($logger, $translator);
```

The default messages are now translation ids resolved through the new `openium_symfony_toolkit`
translation domain (English and French catalogs shipped in `Resources/translations/`). If you
called one of the `set*Message()` setters with a literal custom string, nothing changes: an
unknown translation id is returned unchanged by the translator, so your custom message still
works regardless of locale.

### AtHelper shell escaping (security fix, potentially breaking)

`$cmd` and `$path` passed to `createAtCommand()` / `createAtCommandFromPath()` are now escaped
with `escapeshellarg()` before being interpolated into the shell command. This closes a shell
command injection vulnerability, but also means `$cmd` is now treated as literal data given to
`at`, not as a shell snippet. If you relied on shell metacharacters (`;`, `|`, `` ` ``, `$(...)`,
quotes, ...) in `$cmd` being interpreted by the shell, wrap your command in `sh -c '...'` yourself
before passing it in.

### ServerService (deprecated, no action required)

`getBasePath()` only ever duplicated `Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost()`.
The service still works (it now delegates to `getSchemeAndHttpHost()` internally), but
instantiating it triggers a deprecation notice. Migrate when convenient:

#### Before

```php
function myFunc(ServerServiceInterface $serverService): string
{
    return $serverService->getBasePath();
}
```

#### After

```php
function myFunc(RequestStack $requestStack): string
{
    $request = $requestStack->getCurrentRequest();
    return $request === null ? '' : $request->getSchemeAndHttpHost() . '/';
}
```

### AbstractCommand (deprecated, no action required)

The `--nl` option and `writeMessage()` helper only duplicate the standard `-q`/`--quiet` console
flag and `OutputInterface::isQuiet()`. Migrate when convenient:

#### Before

```php
class MyCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecute($input, $output);
        $this->writeMessage('Doing work...');
        return Command::SUCCESS;
    }
}
```

#### After

```php
class MyCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output->isQuiet()) {
            $output->writeln('Doing work...');
        }
        return Command::SUCCESS;
    }
}
```

Run with `-q`/`--quiet` instead of `--nl` to silence output.

### ContentExtractorUtils (deprecated, no action required)

Manually pulling and type-checking individual keys out of a decoded JSON array is deprecated in
favor of a typed DTO validated through Symfony's Validator. Migrate when convenient:

#### Before

```php
$name = ContentExtractorUtils::getString($content, 'name');
$age = ContentExtractorUtils::getInt($content, 'age', required: false, default: null, nullable: true);
```

#### After

```php
final class MyPayload
{
    #[Assert\NotBlank]
    public string $name;

    public ?int $age = null;
}
```

```php
public function myAction(#[MapRequestPayload] MyPayload $payload): Response
{
    // $payload->name, $payload->age are already validated
}
```

### DateStringUtils (deprecated, no action required)

The format guess based on string length/suffix is fragile. Migrate when convenient:

#### Before

```php
$date = DateStringUtils::getDateTimeFromString($dateString);
```

#### After

```php
$date = new DateTimeImmutable($dateString);
```

or, when deserializing through the Serializer, let `DateTimeNormalizer` handle it automatically.

### DoctrineExceptionHandlerService subclass matching (fixed, check your own exception subclasses)

`toHttpException()` used to compare `$throwable::class` exactly, so a project-specific subclass
of a handled Doctrine exception (e.g. a custom subclass of `TableNotFoundException`) fell through
to being rethrown as-is instead of being converted to an HTTP exception. It's now matched with
`instanceof`, so such subclasses — and any Doctrine driver exception not explicitly listed — are
now converted too. If your project relied on one of these previously-unhandled exceptions
propagating raw (e.g. to be caught by different code further up), double-check that behavior
after upgrading.

### Upload namer / multi-field upload support (additive, no action required)

- `FileUploaderService`'s constructor gained an optional third argument,
  `UploadFilenameGeneratorInterface`, to customize filename generation (defaults to the exact
  pre-7.0 behavior). See the README's FileUploaderService section.
- Entities needing more than one upload property can now implement the new
  `MultiUploadInterface`/`MultiUploadTrait` instead of `WithUploadInterface`/`WithUploadTrait`.
  Existing entities using the single-field API are unaffected.
