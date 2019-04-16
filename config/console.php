<?php

/**
 * Configuration used when the app is launched from its script version, i.e.,
 * yii and yii.bat.
 */

// Get local configuration.
$local = require(__DIR__ . '/local.php');

return [
    'id' => 'M3S-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => $local['cache'],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $local['db'],
    ],
];
