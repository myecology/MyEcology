<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'name' => 'MyEcology管理平台',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        //RBAC - 01
        'admin' => [
            'class' => 'mdm\admin\Module',
            // 'layout' => 'left-menu',                             //yii2-admin的导航菜单
            'viewPath' => '@backend/template/view-admin'            //设置视图文件
        ],
        //  锁仓银行
        'bank' => [
            'class' => 'backend\modules\bank\Module',
        ],
        //  币种管理
        'currency' => [
            'class' => 'backend\modules\currency\Module',
        ],
		'treemanager' =>  [
        	'class' => '\kartik\tree\Module',
        	// other module settings, refer detailed documentation
        ],
        //  资产管理
        'assets' => [
            'class' => 'backend\modules\assets\Module',
        ],
        //  会员管理
        'member' => [
            'class' => 'backend\modules\member\Module',
        ],
        //  活动管理
        'activation' => [
            'class' => 'backend\modules\activation\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                //保存日志到文件
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                //保存日志到数据库
                // [
                //     'class' => 'yii\log\DbTarget',
                //     'levels' => ['error', 'warning'],
                // ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        //RBAC - 02
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        //adminLET - 主题
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',     // 其他主题 "skin-blue","skin-black","skin-red","skin-yellow","skin-purple", "skin-green", "skin-blue-light", "skin-black-light", "skin-red-light","skin-yellow-light","skin-purple-light","skin-green-light"
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'suffix' => '.html',
           'rules' => [

           ],
        ],
    ],
    //RBAC - 03
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            // 'admin/*'
        ]
    ],
    //RBAC - 04
    'aliases' => [
        '@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
    ],

    'params' => $params,
];
