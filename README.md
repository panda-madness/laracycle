# Laracycle - Cycle ORM in Laravel

```php
$user = new User('code.artisan@failedstartup.com');

$user->addPost(
    new Post('Yet another Medium article')
);

Cycle::transaction()->persist($user)->run();
```
