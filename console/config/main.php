<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','queue'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
    ],


    'components' => [
        //redis 缓存
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 3,
        ],
        'queue' => [
            'class' => '\yii\queue\redis\Queue',
            'as log' => '\yii\queue\LogBehavior',//错误日志 默认为 console/runtime/logs/app.log
            'redis' => 'redis', // 连接组件或它的配置
            'channel' => 'queue', // Queue channel key
        ],
        'log' => [
            //'flushInterval' => 50,
            'targets' => [
                //保存日志到数据库
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['currency\*'],
                    'logFile' => '@console/runtime/logs/currency.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['eth\*'],
                    'logFile' => '@console/runtime/logs/eth.log',
                    'exportInterval' => 50,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['ethTx\*'],
                    'logFile' => '@console/runtime/logs/eth-tx.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['giftmoney\*'],
                    'logFile' => '@console/runtime/logs/giftmoney.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['withdraw'],
                    'logFile' => '@console/runtime/logs/withdraw.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['bitcoin'],
                    'logFile' => '@console/runtime/logs/bitcoin.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['lockbank'],
                    'logFile' => '@console/runtime/logs/lockbank.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['ethCollect\*'],
                    'logFile' => '@console/runtime/logs/collect.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['mall'],
                    'logFile' => '@console/runtime/logs/mall.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['wine'],
                    'logFile' => '@console/runtime/logs/wine.log',
                    'exportInterval' => 10,
                    'logVars' => [],

                ],
            ],
        ],

        //console RBAC
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'auth_item',
            'assignmentTable' => 'auth_assignment',
            'itemChildTable' => 'auth_item_child',
            'ruleTable' => 'auth_rule',
            'defaultRoles' => ['guest'],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'suffix' => '.html',
            'rules' => [

            ],
            'baseUrl' => '',
        ],
    ],
    'params' => $params,
];
