# RonteLtdPushBundle
Provide functions to send push notifications.

Features include:

- Send single notification
- Send notifications on background
- Send bulk notifications

**Note:** For now supports only APNS.

## Install

### Composer
```sh
composer require ronte-ltd/push-bundle
```

## Init

### AppKernel.php
```php
new RonteLtd\PushBundle\RonteLtdPushBundle(),
```

### config.yml
```yaml
ronte_ltd_push:
    push_env: "%push_env%"
    push_sound: true // bool
    push_expiry: 12000 // message expiry, int value in seconds
    apns_certificates_dir: "%kernel.root_dir%/../var/apns/"
    gearman_server: "%gearman_server%"
    gearman_port: "%gearman_port%"
```

### parameters.yml
```yaml
push_env: "valid values: 'prod', 'dev'"
gearman_server: "Add gearman server here"
gearman_port: "Add gearman port here"
```

### Certificates
    Puth APNS sertificates files to 'var/apns' folder

## Use

### Send single notification
```php
/**
 * @param string $deviceId - recipient device token
 * @param string $text - text message
 * @param array $extra - custom properties array, default: empty array
 * @param int $badge - badge, default: null
 */
$container->get('push.pusher')->send($deviceId, $text, $extras, $badge);
```

###Send notifications on background
Run ``push:worker:run`` command on background.
```php
$pusher = $container->get('push.pusher');
$pusher->addPush($deviceId, $text, $extras, $badge);
```

###Send bulk notifications
```php
$pusher = $container->get('push.apns');
$pusher->addMessage(
    $pusher->createMessage($deviceId, $text, $extras, $badge)
);
// Use addMessage as much as needed
$pusher->runQueue();
```

