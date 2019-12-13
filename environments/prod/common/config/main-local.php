<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8mb4',
            'masters' => [
                ['dsn' => 'mysql:host=rm-wz93t84m3dbwc4o47.mysql.rds.aliyuncs.com;dbname=iec'],
            ],
            'masterConfig' => [
                'username' => 'iec',
                'password' => 'qwer!!!!1234',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 5
                ],
            ],
            'slaves' => [
                ['dsn' => 'mysql:host=rr-wz937acbanvc53l07.mysql.rds.aliyuncs.com;dbname=iec'],
            ],
            'slaveConfig' => [
                'username' => 'iec',
                'password' => 'qwer!!!!1234',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 5
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
