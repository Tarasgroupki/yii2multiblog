<?php

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../config/main.php'),
    require(__DIR__ . '/../../config/main-local.php'),
    require(__DIR__ . '/../_config.php'),
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=localhost;dbname=yii2_blog_unit',
            ],
        ],
        'id' => 'app-common',
        'basePath' => dirname(__DIR__),
    ]
);
