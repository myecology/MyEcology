<?php
namespace api\controllers;

use \RongCloud\RongCloud;

class RongCloudExpansion extends RongCloud
{
    //  群禁言
    public function groupBanAdd($groupId)
    {
        try {
            $params = array(
                'groupId' => $groupId,
            );

            $ret = $this->SendRequest->curl('/group/ban/add.json', $params, 'urlencoded', 'im', 'POST');
            if (empty($ret)) {
                throw new \Exception('bad request');
            }

            return $ret;
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    //  接触群禁言
    public function groupBanRollback($groupId)
    {
        try {
            $params = array(
                'groupId' => $groupId,
            );

            $ret = $this->SendRequest->curl('/group/ban/rollback.json', $params, 'urlencoded', 'im', 'POST');
            if (empty($ret)) {
                throw new \Exception('bad request');
            }

            return $ret;
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }

    }

    //  添加群白名单
    public function groupBanWhitelistAdd($userId, $groupId)
    {
        try {
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId,
            );

            $ret = $this->SendRequest->curl('/group/user/ban/whitelist/add.json', $params, 'urlencoded', 'im', 'POST');
            if (empty($ret)) {
                throw new \Exception('bad request');
            }

            return $ret;
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    //  移除群白名单
    public function groupBanWhitelistRollback($userId, $groupId)
    {
        try {
            $params = array(
                'userId' => $userId,
                'groupId' => $groupId,
            );

            $ret = $this->SendRequest->curl('/group/user/ban/whitelist/rollback.json', $params, 'urlencoded', 'im', 'POST');
            if (empty($ret)) {
                throw new \Exception('bad request');
            }

            return $ret;
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    //  获取白名单列表
    public function groupBanWhitelistQuery($groupId)
    {
        try {
            $params = array(
                'groupId' => $groupId,
            );

            $ret = $this->SendRequest->curl('/group/user/ban/whitelist/query.json', $params, 'urlencoded', 'im', 'POST');
            if (empty($ret)) {
                throw new \Exception('bad request');
            }

            return $ret;
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
