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
 * @param array $payload - payload array
 */
 $payload = [
     'project' => $id,    // int|string name or id of an app, required
     'pushType' => $type, // int type, required
     'badge' => null,     // int|null, optional
     'headers' => [],     // array of headers, optional
     'extra' => [],       // additional info array, optional
 ];

 $credentials = [
     'certificate' => $fullPathToCertificate, // required
     'passPhrase' => $passPhrase,
     'certificationAuthorityFile' => $fullPathToCertificationAuthorityFile,
 ];
$container->get('push.pusher')->send($deviceId, $text, $payload, $creadentials);
```

###Send notifications on background
Run ``push:worker:run --process_id=devFirst`` command on background.
Use ``--process_id`` option if there's multiple projects on a server using this command.
```php
$payload = [
     'project' => $id,    // int|string name or id of an app, required
     'pushType' => $type, // int type, required
     'badge' => null,     // int|null, optional
     'headers' => [],     // array of headers, optional
     'extra' => [],       // additional info array, optional
 ];

$credentials = [
  'certificate' => $fullPathToCertificate, // required
  'passPhrase' => $passPhrase,
  'certificationAuthorityFile' => $fullPathToCertificationAuthorityFile,
];

$pusher = $container->get('push.pusher');
$pusher->addPush($deviceId, $text, $payload, , $creadentials);
```

###Send bulk notifications
```php
$payload = [
     'project' => $id,    // int|string name or id of an app, required
     'pushType' => $type, // int type, required
     'badge' => null,     // int|null, optional
     'headers' => [],     // array of headers, optional
     'extra' => [],       // additional info array, optional
 ];
$pusher = $container->get('push.apns');
$pusher->addMessage(
    $pusher->createMessage($deviceId, $text, $payload)
);
// Use addMessage as much as needed

$credentials = [
  'certificate' => $fullPathToCertificate, // required
  'passPhrase' => $passPhrase,
  'certificationAuthorityFile' => $fullPathToCertificationAuthorityFile,
];

$pusher->runQueue($credentials);

