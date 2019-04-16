<?php

/**
 * Example app configuration.
 * Copy to config/local.php.
 */

return [
    // Secret key used for encrypting cookies. Change for production.
    'cookieValidationKey' => 'secretKey',
    // App cache.
    // A dummy cache can be used for development but this configuration will
    // not be suitable for production.
    'cache' => [
        'class' => 'yii\caching\DummyCache',
    ],
    // DB configuration.
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=host;dbname=dbname',
        'username' => 'username',
        'password' => 'passwword',
        'charset' => 'utf8',
    ],
];
