<?php

namespace api\controllers;

use Yii;

class APIFormat
{
    public static $code = [
        404 => 'Found Not!',                //  错误的方法
        1000 => 'Upload FileInput Error',   //  上传文件失败
        3001 => 'SMS Send Error',           //  短信发送失败
        3002 => '验证码错误',                   //  验证码检验错误
        3901 => 'Search Error', //搜索失败
        3902 => '搜索不到数据', //  搜索失败
        4001 => 'Signup Error',             //  注册失败
        4002 => 'Login Error',              //  登陆失败
        4003 => 'User Info Get Error',      //  用户信息
        4004 => 'User Update Error',    //  用户更新失败
        4005 => 'Reset Password Error', //  重置密码
        4006 => 'Forget Password Error',    //忘记密码
        4007 => 'Set Payment Error',    //设置支付密码错误
        4008 => 'Reset Payment Error',  //修改支付密码错误
        4009 => 'Forget Payment Error', //忘记支付密码错误
        4016 => 'Invitation info saving failed while Signup',             //  注册邀请记录失败
        4014 => 'Payment Password Verification Error',  //支付密码验证失败
        4015 => 'Payment Password Not Found',   //  支付密码未设置
        4080 => 'User Update IEC Error',    //修改IEC失败

        4010 => 'Add Friend Error',         //  添加好友失败
        4011 => 'Friend Completed Error',   //  好友确认失败
        4012 => 'Update Friend Error',  // 修改朋友失败
        4013 => 'Delete Friend Error',  //删除朋友失败
        4020 => 'Friend Moment Error',      //  朋友圈创建失败  
        4021 => 'Friend Moment Delete Error',   //  删除朋友圈失败
        4022 => '一键添加好友失败', //  一键添加好友失败

        4030 => 'Friend Moment Like Error', //  点赞朋友圈失败
        4040 => 'Friend moment Reply Error',    //  回复失败
        4041 => 'Friend Moment Reply Delete Error',     //朋友圈回复删除失败
        4042 => '获取新消息参数错误',   // 获朋友圈参数错误

        4050 => 'Group Add Error',                  //  添加群失败
        4051 => 'Group Add User Error', //群添加好友失败
        4052 => 'Group Out User Error', //群退出失败
        4053 => 'Group Request Error',  //申请加入群
        4054 => 'Group Request List Error', //申请列表
        4055 => 'Group Agree Error',    //群同意失败
        4056 => 'Setting Group Admin Error',    //设置群管理员失败
        4057 => 'Out Group Member Error',   //踢出群成员失败
        4058 => 'Group Update Error',   //更新群失败
        4059 => 'Drop Group Admin Error',   //取消管理员是吧
        4060 => 'Transfer Group Error', //转让群失败
        4061 => 'Group User update Error',  //修改群成员信息失败
        4062 => 'Group Ban Add Error',  //  禁言群失败
        4063 => 'Group Ban Rollback Error', // 接触群禁言
        4064 => 'Group self update Error',  //  self update
        4065 => '开启/关闭禁言失败',

        4100 => '购买超级节点失败',             //  购买超级节点失败
        4101 => '退出超级节点失败',             //  退出超级节点失败


        5001 => '添加钱包地址非法', //Address illegal, 添加钱包地址非法
        5002 => '钱包地址保存失败', //Address saving failed, 钱包地址保存失败
        5009 => '钱包地址不存在或者不属于您', //Address not exists or not belongs to the request user，钱包地址不存在或者不属于您
        5011 => '钱包地址删除失败', //Address removing failed， 钱包地址删除失败

        5003 => '币种不存在或者不可用', //Currency not exists or invalid，币种不存在或者不可用
        5004 => '金额非法', //Amount illegal，金额非法
        5005 => '没有该币种的市价', //No market price requested，没有相应币种的市价
        5006 => '提交用户钱包验证失败', //Adding wallet for user illegal，提交用户钱包验证失败
        5007 => '保存用户钱包失败', //Saving wallet for user failed，保存用户钱包失败
        5008 => '隐藏用户钱包失败', //hide user's wallet failed，隐藏用户钱包失败
        5012 => '钱包地址加载失败', //WalletAddress matched failed，钱包地址加载失败
        5013 => '用户启用钱包失败', //Wallet adding failed，用户启用钱包失败
        5014 => '用户钱包地址创建失败', //WalletAddress adding failed，用户钱包地址创建失败
        5015 => '币种已关闭转账', //Transfer has been disabled now
        5016 => '每日发红包限额到达上限', //Limit for sending gift-money daily
        5017 => '每日转账限额到达上限', //Limit for sending transfer daily

        5010 => '币种配置不全', //Currency configuration incomplete，币种配置不全
        5020 => '充值记录不存在', //Deposit not exists，充值记录不存在
        5030 => '提交提现请求验证失败', //Adding deposit illegal，提交提现请求验证失败
        5031 => '提交提现请求保存失败', //Adding deposit failed，提交提现请求保存失败

        5040 => '提交提现请求保存失败', //Adding deposit failed，提交提现请求保存失败
        5041 => '钱包余额不足', //User's balance not enough， 钱包余额不足
        5042 => '手续费不足，请进行充值', //User's wallet for fee not exists, 提现手续费钱包不存在
        5043 => '提现余额不足', //Balance not enough，提现余额不足
        5044 => '用户提现地址不存在', //User address not exists，用户提现地址不存在
        5045 => '用户钱包不存在', //User\s wallet not exists，用户钱包不存在
        5048 => '地址不合法', //User address invalid，地址不合法
        5053 => '随机红包金额不足以分配', //Amount too small while sending random gift-money
        5054 => '红包金额超过限额', //Amount out of limit
        5055 => '提现数量应大于手续费数量', //Actual amount unexpected

        5046 => '锁定手续费失败', //Locking fee failed，锁定手续费失败
        5047 => '锁定余额失败', //Locking balance failed，锁定余额失败

        5049 => '提交提现保存失败', //Withdraw saving failed，提交提现保存失败
        5050 => '单笔提现金额太低', //Amount too small while withdraw
        5051 => '单笔提现金额太高', //Amount too large while withdraw
        5052 => '单日提现金额太高', //Amount too large while withdraw in one day

        5100 => '发红包验证失败', //GiftMoney validating failed，发红包验证失败
        5101 => '钱包扣减失败', //Wallet reducing failed，钱包扣减失败
        5102 => '红包记录创建失败', //GiftMoney adding failed，红包记录创建失败
        5104 => '红包领取记录不存在', //GiftMoney taker not exists， 红包领取记录不存在
        5105 => '个人红包不能发给自己', //User can't be the taker in single giftMoney， 个人红包不能发给自己
        5106 => '未知的type值', //Unexpected type value，未知的type值
        5107 => '创建待分配红包失败', //Creating gift-money-taker records failed.

        5200 => '发红包保存失败', //GiftMoney saving failed，发红包保存失败
        5300 => '领取红包失败', //GiftMoney taking failed，领取红包失败
        5301 => '红包已领过或者已领完', //Has taken this Giftmoney or has been taken out
        5302 => '红包分配失败', //Calculate taker's amount in GiftMoney unexpected， 红包分配失败
        5303 => '创建红包领取记录失败', //UserWalletTake creating failed，创建红包领取记录失败
        5304 => '领红包后更新钱包失败', //UserWallet updating failed in GiftMoneyTake，领红包后更新钱包失败
        5305 => '创建用于领红包的钱包失败', //UserWallet creating failed in GiftMoneyTake，创建用于领红包的钱包失败
        5306 => '金额更新失败', //UserWallet increasing amount failed，金额更新失败
        5307 => '红包记录不存在', //GiftMoney not exists，红包记录不存在
        5400 => '领取红包失败', //GiftMoney query failed，领取红包失败
        5501 => '创建红包领取记录失败', //GiftMoneyTake creating failed，创建红包领取记录失败
        5900 => '无查看红包权限', //No access for this GiftMoney，无查看红包权限

        6000 => '发送人无效或者未指定',
        6001 => '接收人无效或者未指定',
        6002 => '不可以转账给自己',
        6003 => '发送/接收人钱包不存在',
        6004 => '余额不足',
        6005 => '转账扣款失败',
        6006 => '转账收款失败',
        6007 => '转账记录保存失败',

        6008 => '转账检查失败',
        6009 => '发起转账失败',

        6010 => '扣款失败',
        6011 => '入款失败',

        6100 => '转账记录不存在',
        6101 => '无查看转账详情权限',
        6102 => '转账查看失败',

        7000 => '认证提交失败',
        7001 => '用户未提交审核信息',
        7002 => '用户实名认证未通过',
        7003 => '提交的认证正在审核中',


        9000 => '未知异常',
    ];

    /**
     * 成功消息
     *
     * @param [string Array] $data
     * @return void
     */
    public static function success($data)
    {
        return [
            'status' => 200,
            'data' => $data,
            'msg' => '',
        ];
    }

    /**
     * 错误信息
     * @param $code
     * @param null|array $msg
     * @return array
     */
    public static function error($code, $msg = null)
    {
        $msg = self::resetArray($msg);
        isset(self::$code[$code]) || $code = 9000;

        return [
            'status' => $code,
            'data' => [],
            'msg' => is_null($msg) ? self::$code[$code] : $msg
        ];
    }

    /**
     * 递归获取首字符串
     *
     * @param [type] $msg
     * @return void
     */
    protected static function resetArray($msg)
    {
        if (is_array($msg)) {
            $msg = reset($msg);
            return self::resetArray($msg);
        }
        return $msg;
    }

    /**
     * 取ActiveRecord::getErrors() 错误方法中第一条错误信息
     * @param array $msg
     * @return bool
     */
    public static function popError(Array $msg)
    {
        $_error = is_array($msg) ? array_values($msg)[0] : false;
        return $_error ? $_error[0] : false;
    }

    /**
     * @param $number
     * @param int $decimal
     * @return string
     */
    public static function asMoney($number, $decimal = 8)
    {
        $result = $number;

        $_number = sprintf('%0.' . $decimal . 'f', $number);
        if (stripos($_number, '.') === false) {
            $result = $_number;
        } else {
            $_number = rtrim($_number, '0');
            $result = $_number[strlen($_number) - 1] == '.' ? rtrim($_number, '.') : $_number;
        }
        return $result;
    }
}
