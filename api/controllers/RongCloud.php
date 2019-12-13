<?php
namespace api\controllers;

use Yii;
use yii\helpers\Json;

class RongCloud
{
    private static $instance;
    public $server;

    private function __construct($config)
    {
        $this->server = new RongCloudExpansion($config['appKey'], $config['appSecret']);
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            $config = [
                'appKey' => Yii::$app->params['RongCloud']['appKey'],
                'appSecret' => Yii::$app->params['RongCloud']['appSecret'],
            ];
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    //  创建用户
    public function getToken($userid, $nickname, $headimgurl)
    {
        $result = $this->server->user()->getToken($userid, $nickname, $headimgurl);
        return Json::decode($result);
    }

    //  刷新用户
    public function refresh($userid, $nickname, $headimgurl)
    {
        $result = $this->server->user()->refresh($userid, $nickname, $headimgurl);
        return Json::decode($result);
    }

    //  发送好友请求
    public function sendPrivateMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:ContactNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送群系统消息
    public function sendPrivateGrpMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:GrpNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送朋友圈通知
    public function sendPrivateMomentMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:MomentNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送服务消息通知
    public function sendPrivateServiceMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:ServiceNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送修改群昵称通知
    public function sendPrivateGrpReNameMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:GrfNicknameNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  确认消息已经是好友了
    public function sendInfoMessage($userid, $users, $content)
    {
        $result = $this->server->message()->publishPrivate($userid, $users, 'RC:InfoNtf', $content, '', '', '4', '0', '0', '0', '0');
        return Json::decode($result);
    }

    //  用户黑名单列表
    public function queryBlacklist($userid)
    {
        $result = $this->server->user()->queryBlacklist($userid);
        return Json::decode($result);
    }

    //  添加黑名单
    public function addBlacklist($inUserid, $toUserid)
    {
        $result = $this->server->user()->addBlacklist($inUserid, $toUserid);
        return Json::decode($result);
    }

    //  重黑名单中移除
    public function removeBlacklist($inUserid, $toUserid)
    {
        $result = $this->server->user()->removeBlacklist($inUserid, $toUserid);
        return Json::decode($result);
    }

    //  发送朋友圈通知
    public function sendMomentMessage($userid, $users, $content)
    {
        $result = $this->server->message()->PublishSystem($userid, $users, 'RC:MomentNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送群聊信息
    public function sendGroupMessage($userid, $groupids, $content)
    {
        $result = $this->server->message()->publishGroup($userid, $groupids, 'RC:GrpNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  发送群聊info信息
    public function sendGroupInfoNtfMessage($userid, $groupids, $content)
    {
        $result = $this->server->message()->publishGroup($userid, $groupids, 'RC:InfoNtf', $content, '', '', '1', '0', '0');
        return Json::decode($result);
    }

    //  创建群
    public function createGroup(array $users, $groupid, $gourName)
    {
        $result = $this->server->group()->create($users, $groupid, $gourName);
        return Json::decode($result);
    }

    //  群刷新
    public function updateGroup($groupid, $gourName)
    {
        $result = $this->server->group()->refresh($groupid, $gourName);
        return Json::decode($result);
    }

    //  解散群
    public function dismissGroup($userid, $groupid)
    {
        $result = $this->server->group()->dismiss($userid, $groupid);
        return Json::decode($result);
    }

    //  添加群成员
    public function addGroupUser(array $users, $groupid, $gourName)
    {
        $result = $this->server->group()->join($users, $groupid, $gourName);
        return Json::decode($result);
    }

    //  删除群成员
    public function deleteGroupUser(array $users, $groupid)
    {
        $result = $this->server->group()->quit($users, $groupid);
        return Json::decode($result);
    }

    //  添加禁言群成员
    public function addGroupGagUser($userId, $groupId, $minute)
    {
        $result = $this->server->group()->addGagUser($userId, $groupId, $minute);
        return Json::decode($result);
    }

    //  移除禁言群成员
    public function rollBackGroupGagUser($userId, $groupId)
    {
        $result = $this->server->group()->rollBackGagUser($userId, $groupId);
        return Json::decode($result);   
    }

    //  禁言群
    public function banGroupAdd($groupid)
    {
        $result = $this->server->groupBanAdd($groupid);
        return Json::decode($result);
    }

    //  解除禁言群
    public function banGroupRollback($groupid)
    {
        $result = $this->server->groupBanRollback($groupid);
        return Json::decode($result);
    }

    //  添加群白名单
    public function banGroupWhitelistAdd($users, $groupid)
    {
        $result = $this->server->groupBanWhitelistAdd($users, $groupid);
        return Json::decode($result);
    }

    //  移除群白名单
    public function banGroupWhitelistRollback($users, $groupid)
    {
        $result = $this->server->groupBanWhitelistRollback($users, $groupid);
        return Json::decode($result);
    }

    //  获取群白名单列表
    public function banGroupWhitelistQuery($groupid)
    {
        $result = $this->server->groupBanWhitelistQuery($groupid);
        return Json::decode($result);
    }

}
