<?php

namespace common\models;

use sugao2013\getui\igetui\DictionaryAlertMsg;
use sugao2013\getui\igetui\IGtAPNPayload;
use sugao2013\getui\igetui\template\IGtTransmissionTemplate;
use sugao2013\getui\Push;
use Yii;

class GetuiService
{
    public function getGetui()
    {
        return \Yii::$app->getui;
    }

    /**
     * @param $target_id
     * @param $title
     * @param $content
     * @param array $payload
     * @param int $type
     * @return array|bool
     */
    public function push($target_id, $title, $content, $payload, $type = 1)
    {
        $payload_config = [
            'type' => $type, //收到消息是否立即启动应用，1为立即启动，2则广播等待客户端自启动
            'template_type' => 4,
            'title' => $title,
            'content' => $content,
            'payload' => $payload,
        ];

        /**
         * @var Push $getui
         */
        $getui = $this->getGetui()->config($payload_config);
        $getui->template_var = $this->GtTransmissionTemplate($payload_config, $getui);

        try {
            if (is_array($target_id) && count($target_id) > 1) {
                $response = $getui->pushMessageToList($target_id);
            } else {
                is_array($target_id) && $target_id = $target_id[0];
                $response = $getui->pushMessageToSingle($target_id);
            }

            if (!is_array($response) || !isset($response['result']) || $response['result'] != 'ok') {
                throw new \ErrorException('request failed: ' . var_export($response, 1), 10000);
            }

            $result = $response;
        } catch (\ErrorException $e) {
            $result = false;
        }

        return $result;
    }

    private function GtTransmissionTemplate($config, &$getui_object)
    {
        $template = new IGtTransmissionTemplate();
        $template->set_appId($getui_object->appId); //应用appid
        $template->set_appkey($getui_object->appKey); //应用appkey
        $template->set_transmissionType($config['type']); //透传消息类型

        $android_payload = isset($config['payload']) ? $config['payload'] : $config['content'];
        if(is_array($android_payload)){
            isset($config['title']) && $android_payload['title'] = $config['title'];
            isset($config['content']) && $android_payload['content'] = $config['content'];
        }
        $template->set_transmissionContent(json_encode($android_payload)); //透传内容
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

        /**
         * APN简单推送
//        $template = new IGtAPNTemplate();
//        $apn = new IGtAPNPayload();
//        $alertmsg=new SimpleAlertMsg();
//        $alertmsg->alertMsg="";
//        $apn->alertMsg=$alertmsg;
////        $apn->badge=2;
////        $apn->sound="";
//        $apn->add_customMsg("payload","payload");
//        $apn->contentAvailable=1;
//        $apn->category="ACTIONABLE";
//        $template->set_apnInfo($apn);
//        $message = new IGtSingleMessage();
         */

        //APN高级推送
        $apn = new IGtAPNPayload();
        $alertmsg = new DictionaryAlertMsg();
        $alertmsg->body = $config['content'];
        $alertmsg->actionLocKey = "ActionLockey";
        isset($config['content']) && $alertmsg->locKey = $config['content'];
//        $alertmsg->locArgs = array("locargs");
        $alertmsg->launchImage = "launchimage";

        //        IOS8.2 支持
        isset($config['title']) && $alertmsg->title = $config['title'];
        $alertmsg->titleLocKey = "TitleLocKey";
        $alertmsg->titleLocArgs = array("TitleLocArg");

        $apn->alertMsg = $alertmsg;
        $apn->badge = 1;
        $apn->sound = "";
        isset($config['payload']) && $apn->add_customMsg("payload", json_encode($config['payload']));
        $apn->contentAvailable = 1;
        $apn->category = "ACTIONABLE";
        $template->set_apnInfo($apn);

        return $template;
    }
}