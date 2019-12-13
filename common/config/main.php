<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => '',
            'currencyCode' => 'CNY',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //阿利大鱼
        'aliyun' => [
            'class' => 'saviorlv\aliyun\Sms',
            'accessKeyId' => '',
            'accessKeySecret' => '',
        ],
        //redis 缓存
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '',
            'port' => ,
            'database' => ,
        ],
        //自定义的语言包
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'language' => 'language.php',
                    ]
                ]
            ],
        ],
        'log' => [
            'flushInterval' => 500,
        ]
    ],
    'modules' => [
        'ethereum' => '',
        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
    ]
];

