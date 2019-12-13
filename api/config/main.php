<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module', //版本一
        ],
        'bank' => [
            'class' => 'api\modules\bank\Module', //理财
        ],
        'v2' => [
            'class' => 'api\modules\v2\Module', //版本二
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'qr' => [
            'class' => '\Da\QrCode\Component\QrCodeComponent',
            // ... you can configure more properties of the component here
        ],
        'response' => [
            // 定制化返回
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;

                switch ($response->statusCode) {
                    case 200:
                        $response->format = $response::FORMAT_JSON;
                        $response->data = [
                            'data' => $response->data['data'],
                            'msg' => $response->data['msg'],
                            'status' => $response->data['status'],
                        ];
                        break;
                    case 400:
                        $response->format = $response::FORMAT_JSON;
                        $response->data = [
                            'data' => '',
                            'msg' => $response->data['message'],
                            'status' => 400,
                        ];

                        break;
                    case 401:
                        $response->format = $response::FORMAT_JSON;
                        $response->data = [
                            'data' => '',
                            'msg' => $response->data['message'],
                            'status' => 401,
                        ];
                        break;
                    case 500:
                        // $response->format = $response::FORMAT_JSON;
                        // $response->data = [
                        //     'data' => '',
                        //     'msg' => 'Server internal error',
                        //     'status' => 500,
                        // ];
                        break;
                    default:
                        # code...
                        break;
                }
            },
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableSession' => false,
            'enableAutoLogin' => true,
            'loginUrl' => null,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
            // 'enableAutoLogin' => true,
            // 'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['mill'],
                    'logFile' => '@api/runtime/logs/mill.log',
                    'logVars' => ['*'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['call'],
                    'logFile' => '@api/runtime/logs/call.log',
                    'logVars' => ['*'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['shop'],
                    'logFile' => '@api/runtime/logs/shop.log',
                    'logVars' => ['*'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user', 'v1/sms', 'v1/user-action', 'v1/friend-circle', 'v1/address', 'v1/currency'],
                    'pluralize' => false,
                ],
                'GET v1/user-action/start-page' => 'v1/user-action/start-page',
                'POST v1/sms/index' => 'v1/sms/index', //  获取验证码
                'POST v1/sms/forget' => 'v1/sms/forget', //  获取忘记密码/忘记支付密码验证码
                'POST v1/sms/verification' => 'v1/sms/verification', //  验证码验证
                'POST v1/user/is-blacklist' => 'v1/user/is-blacklist',    //  是否黑名单
                'POST v1/user/signup' => 'v1/user/signup', //  注册
                'POST v1/user/login' => 'v1/user/login', //  登陆
                'POST v1/user/location' => 'v1/user/location', //  更新用户坐标经纬度
                'GET v1/user/index' => 'v1/user/index', //  用户基本信息
                'POST v1/user/reset-password' => 'v1/user/reset-password',  //重置密码
                'POST v1/user/forget-password' => 'v1/user/forget-password',    //忘记密码
                'POST v1/user/set-payment' => 'v1/user/set-payment',    //设置支付密码
                'POST v1/user/reset-payment' => 'v1/user/reset-payment',  //修改支付密码
                'POST v1/user/forget-payment' => 'v1/user/forget-payment',  //忘记支付密码
                'POST v1/user/payment-verification' => 'v1/user/payment-verification',  //验证支付密码
                'GET v1/user/is-payment' => 'v1/user/is-payment',   //是否设置支付密码
                'POST v1/user-action/search' => 'v1/user-action/search', //查询用户
                'POST v1/user-action/auto-search' => 'v1/user-action/auto-search', //智能查询
                'GET v1/friend/index' => 'v1/friend/index', //好友列表/申请列表
                'POST v1/friend/match' => 'v1/friend/match',    //匹配通讯类
                'POST v1/friend/add' => 'v1/friend/add', //添加用户
                'POST v1/friend/completed' => 'v1/friend/completed',    //确认添加好友
                'POST v1/friend/completeds' => 'v1/friend/completeds',  //一键添加好友
                'POST v1/friend/update' => 'v1/friend/update',  //更新好友信息
                'POST v1/friend/delete' => 'v1/friend/delete',  //删除好友
                'POST v1/friend-moment/add' => 'v1/friend-moment/add',  //发布朋友圈
                'GET v1/friend-moment/index' => 'v1/friend-moment/index',   //朋友圈列表
                'GET v1/friend-moment/hot' => 'v1/friend-moment/hot',   //热门朋友圈
                'POST v1/friend-moment/delete' => 'v1/friend-moment/delete',    //删除朋友圈
                'POST v1/friend-moment/like' => 'v1/friend-moment/like',    //朋友圈点赞
                'POST v1/friend-moment/reply' => 'v1/friend-moment/reply',  //朋友圈回复
                'POST v1/friend-moment/delete-reply' => 'v1/friend-moment/delete-reply',    //删除评论
                'GET v1/friend-moment/new-message' => 'v1/friend-moment/new-message',  //朋友圈新消息
                'POST v1/friend-moment/clear-message' => 'v1/friend-moment/clear-message',    //删除朋友圈新消息
                'POST v1/group/add' => 'v1/group/add',  //创建群主
                'GET v1/group/info' => 'v1/group/info', //获取群信息
                'POST v1/group/delete' => 'v1/group/delete',    //解散群
                'POST v1/group/index' => 'v1/group/index',  //群列表
                'POST v1/group-user/add' => 'v1/group-user/add',    //添加群成员
                'GET v1/group/index' => 'v1/group/index',   //  热门群聊
                'GET v1/group/self' => 'v1/group/self', //  已加入的群组
                'POST v1/group/is-group' => 'v1/group/is-group',    //是否在群组
                'POST v1/group-user/delete' => 'v1/group-user/delete',    //退出群组
                'POST v1/group-user/update' => 'v1/group-user/update',  //更新群成员信息
                'GET v1/group-user/index' => 'v1/group-user/index', //群组成员列表
                'POST v1/group-user/request-add' => 'v1/group-user/request-add',    //申请加入群
                'GET v1/group-user/request-list' => 'v1/group-user/request-list',   //申请列表
                'POST v1/group-user/agree' => 'v1/group-user/agree',    //同意加入群
                'POST v1/group-user/set-admin' => 'v1/group-user/set-admin',    //设置群管理员
                'POST v1/group-user/drop-admin' => 'v1/group-user/drop-admin',  //取消管理员
                'POST v1/group-user/transfer' => 'v1/group-user/transfer',   //转让群主
                'POST v1/group-user/out' => 'v1/group-user/out',    //踢出群成员失败
                'POST v1/group-user/join' => 'v1/group-user/join',  //加入群
                'POST v1/group/update' => 'v1/group/update',    //更新群
                'POST v1/group/self-update' => 'v1/group/self-update',  // self update
                'POST v1/group-user/ban' => 'v1/group-user/ban',    /// 开启禁言/关闭禁言

                'POST v1/user/update' => 'v1/user/update',  //更新用户
                'POST v1/user/admin-sms' => 'v1/user/admin-sms',  //更新用户
                'GET v1/ad/index' => 'v1/ad/index', //获取广告
                'GET v1/friend-moment/last' => 'v1/friend-moment/last', //获取自己朋友圈最后一个ID
                'GET v1/user-action/poster' => 'v1/user-action/poster', //获取海报链接
                'GET v1/user-action/candy' => 'v1/user-action/candy',   //  糖果海报
                'GET v1/soft/index' => 'v1/soft/index', //获取版本号
                'POST v1/group/ban-add' => 'v1/group/ban-add',  //添加群禁言
                'POST v1/group/ban-rollback' => 'v1/group/ban-rollback',    //解除群禁言
                'POST v1/user/update-iec' => 'v1/user/update-iec',  //  修改IEC



                //  热门朋友圈
                'GET v1/friend-hot-moment/index' => 'v1/friend-hot-moment/index',   //  热门朋友圈列表
                'GET v1/friend-hot-moment/reply' => 'v1/friend-hot-moment/reply',   //热门朋友圈详情
                'GET v1/friend-hot-moment/details' => 'v1/friend-hot-moment/details',   //朋友圈详情
                'GET v1/user-friend-moment/index' => 'v1/user-friend-moment/index', //  个人朋友圈
                'GET v1/friend-moment/one' => 'v1/friend-moment/one',   // 单条朋友圈


                'GET v1/area/province' => 'v1/area/province',           //  省级
                'GET v1/area/city' => 'v1/area/city',                   //  市级
                'GET v1/area/county' => 'v1/area/county',               //  县级

                'GET v1/supernode/member' => 'v1/supernode/member',     //  节点团队成员
                'GET v1/supernode/index' => 'v1/supernode/index',       //  超姐节点团队列表
                'GET v1/supernode/params' => 'v1/supernode/params',     //  购买超级节点的参数
                'GET v1/supernode/verify' => 'v1/supernode/verify',     //  是否超级节点
                'POST v1/supernode/buy' => 'v1/supernode/buy',          //  购买超级节点
                'GET v1/supernode/profit' => 'v1/supernode/profit',     //  超级节点收益
                'POST v1/supernode/redeem' => 'v1/supernode/redeem',    //  退出超级节点

                //  消息通知
                'GET v1/message/index' => 'v1/message/index',           //  消息通知接口
                'GET v1/message/detail' => 'v1/message/detail',         //  消息内容
                'GET v1/message/official' => 'v1/message/official',
                'GET v1/message/official-detail' => 'v1/message/official-detail',

                'POST v1/currency/index' => 'v1/currency/index',
                'POST v1/currency/value' => 'v1/currency/value',
                'POST v1/currency/add2user' => 'v1/currency/add2user',
                'POST v1/currency/address' => 'v1/currency/address',,
                'POST v1/address/add' => 'v1/address/add',
                'POST v1/address/list' => 'v1/address/list',
                'POST v1/address/delete' => 'v1/address/delete',
                'POST v1/address/update' => 'v1/address/update',
                'POST v1/deposit/list' => 'v1/deposit/list',
                'POST v1/deposit/view' => 'v1/deposit/view',
                'POST v1/gift-money/check' => 'v1/gift-money/check',
                'POST v1/gift-money/add' => 'v1/gift-money/add',
                'POST v1/gift-money/view' => 'v1/gift-money/view',
                'POST v1/gift-money/take' => 'v1/gift-money/take',
                'POST v1/gift-money/operation' => 'v1/gift-money/operation',
                'POST v1/invite/reward' => 'v1/invite/reward',
                'POST v1/invite/team' => 'v1/invite/team',
                'POST v1/invite/exploit' => 'v1/invite/exploit',//业绩统计
                'POST v1/invite/mall-wt' => 'v1/invite/mall-wt',//商城酒链购买

                'POST upload/index' => 'upload/index',  //上传单张图片
                'POST upload/image' => 'upload/image',


                'GET site/index' => 'site/index',
                'POST site/index' => 'site/index',
                'POST v1/user/test' => 'v1/user/test',
                'GET v1/user/get' => 'v1/user/get',
                'GET v1/ad/start-page' => 'v1/ad/start-page', //插入启动页图片
                'GET geetest/index' => 'geetest/index',
                'GET v1/modules/index' => 'v1/modules/index',    //  模块列表
                'POST callback/btc' => 'callback/btc',

                // 'POST SHOP'
                'POST v1/shop/index' => 'v1/shop/index',
                'POST v1/shop/type' => 'v1/shop/type',
                'POST v1/shop/city-list' => 'v1/shop/city-list',
                'POST v1/shop/upload-image' => 'v1/shop/upload-image',
                'POST v1/shop/get-area' => 'v1/shop/get-area',
                'POST v1/shop/list' => 'v1/shop/list',
                'POST v1/shop/create' => 'v1/shop/create',
                'POST v1/shop/detail' => 'v1/shop/detail',
                'POST v1/shop/view' => 'v1/shop/view',
                'POST v1/shop/update' => 'v1/shop/update',
                'GET  v1/shop/location' => 'v1/shop/location',

                //post 认证
                'POST v1/verification/submit' => 'v1/verification/submit',
                'POST v1/verification/view' => 'v1/verification/view',
                'POST v1/verification/update' => 'v1/verification/update',

                //post wallet exchange

                //post user tree
                'POST v1/user/tree' => 'v1/user/tree',
                'POST v1/user/code' => 'v1/user/code',
                'POST v1/user/currency' => 'v1/user/currency',
                'POST v1/user/signup' => 'v1/user/signup',

                //pos
                'POST posback/user-list'=>'posback/user-list', //获取用户列表
                'POST posback/user-info'=>'posback/user-info', // 获取用户信息
                'POST posback/operation'=>'posback/operation',//pos机用户操作
                'POST posback/earn'=>'posback/earn',// 商家操作

            ],
        ],
    ],
    'params' => $params,
];
