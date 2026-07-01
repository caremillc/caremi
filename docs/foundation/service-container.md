# Careminate Service Container

The Careminate Service Container is the dependency injection and inversion of control engine used by the framework.

It is responsible for resolving classes, injecting dependencies, managing shared instances, supporting contextual bindings, and allowing framework services to be extended.

## Responsibilities

The container handles:

- Class resolution
- Interface-to-implementation binding
- Singleton services
- Scoped services
- Instance storage
- Constructor injection
- Method injection
- Contextual binding
- Service tagging
- Service extension
- Lifecycle callbacks
- Circular dependency detection

## Accessing the Container

The application itself extends the container.

```php
$app->make(UserService::class);
```

You may also use the helper:

app(UserService::class);

To get the container instance:

app();
Binding Services

Use bind() to register a service.

$app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);

Resolve the service:

$gateway = $app->make(PaymentGatewayInterface::class);
Singleton Services

A singleton is resolved once and reused.

$app->singleton(LoggerInterface::class, FileLogger::class);

Every call returns the same instance:

$loggerA = $app->make(LoggerInterface::class);
$loggerB = $app->make(LoggerInterface::class);
Scoped Services

Scoped services are reused only during the current request or lifecycle scope.

$app->scoped(RequestContext::class);

Clear scoped services:

$app->forgetScopedInstances();

This is useful for request-specific services such as:

Request context
Tenant context
Auth context
Correlation ID
Locale context
Instance Binding

Register an existing object or value:

$app->instance('app.name', 'Careminate');

Resolve it:

$name = $app->make('app.name');
Closure Bindings

Use closures when a service needs custom construction logic.

$app->bind(ReportExporter::class, function ($app) {
    return new ReportExporter(
        $app->make(DatabaseConnection::class)
    );
});
Auto-Wiring

If a class has dependencies, Careminate can resolve them automatically.

class UserController
{
    public function __construct(
        protected UserRepository $users
    ) {
    }
}

Resolve:

$controller = $app->make(UserController::class);
Method Injection

The container can inject dependencies into methods.

$app->call([$controller, 'store']);

String controller syntax is also supported:

$app->call('App\Http\Controllers\UserController@store');
Passing Runtime Parameters

You can override constructor or method parameters.

$app->make(ReportExporter::class, [
    'format' => 'pdf',
]);
Contextual Binding

Contextual binding allows different classes to receive different implementations of the same dependency.

$app->when(AdminReportService::class)
    ->needs(LoggerInterface::class)
    ->give(AdminLogger::class);

$app->when(ApiReportService::class)
    ->needs(LoggerInterface::class)
    ->give(ApiLogger::class);
Tags

Tags allow multiple services to be grouped.

$app->tag([
    SmsNotifier::class,
    MailNotifier::class,
], 'notifiers');

Resolve tagged services:

foreach ($app->tagged('notifiers') as $notifier) {
    $notifier->send($message);
}
Extending Services

Extenders allow a resolved service to be decorated.

$app->extend(LoggerInterface::class, function ($logger, $app) {
    return new AuditLoggerDecorator($logger);
});
Resolving Callbacks

Run logic while a service is being resolved.

$app->resolving(DatabaseConnection::class, function ($connection, $app) {
    $connection->enableQueryLogging();
});
After Resolving Callbacks

Run logic after a service has resolved.

$app->afterResolving(Router::class, function ($router, $app) {
    $router->loadRoutesFrom($app->basePath('routes/web.php'));
});
Aliases

Aliases allow a short name to point to a binding.

$app->alias(LoggerInterface::class, 'logger');

Resolve:

$logger = $app->make('logger');
Removing Services

Remove a binding and its stored instance:

$app->remove(LoggerInterface::class);

Forget only a singleton instance:

$app->forgetInstance(LoggerInterface::class);
Flushing the Container

Flush all bindings, instances, aliases, tags, callbacks, and resolved state.

$app->flush();

This is mostly useful in tests.

Common Use Cases
Controller Resolution
$controller = $app->make(UserController::class);
Service Provider Registration
$app->singleton(CacheManager::class);
Request-Scoped Context
$app->scoped(TenantContext::class);
Plugin Systems
$app->tag([
    StripePaymentProvider::class,
    PaypalPaymentProvider::class,
], 'payment.providers');
Best Practices
Bind interfaces to implementations.
Use singletons for stateless shared services.
Use scoped bindings for request-specific services.
Avoid resolving services manually inside domain objects.
Prefer constructor injection.
Use contextual binding when different consumers need different implementations.
Use tags for plugin-like extensibility.
Use extenders for decorators and instrumentation.
Clear scoped services after each request.
Troubleshooting
Target class does not exist

This means the class name is wrong, the namespace is wrong, or Composer autoload needs refreshing.

Run:

composer dump-autoload
Target is not instantiable

This usually means you tried to resolve an interface without binding it.

Fix:

$app->bind(LoggerInterface::class, FileLogger::class);
Unable to resolve parameter

This means a constructor or method has a primitive dependency without a default value.

Fix:

$app->make(ReportExporter::class, [
    'format' => 'pdf',
]);
Circular dependency detected

This means two or more classes depend on each other directly or indirectly.

Example:

A depends on B
B depends on A

Refactor by introducing an interface, event, factory, or mediator service.

End-User Impact

The service container improves application reliability by ensuring services are created consistently and dependencies are injected automatically.

End users benefit from:

Faster development of application features
More stable request handling
Better modular architecture
Easier testing
Cleaner package integration

---

# Feature 2 Complete

The **Service Container / IoC / Dependency Injection** feature is now complete.

Next:

```txt
Phase 1 — Feature 3: Service Providers