<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\Poster;
use common\models\Invitation;
use common\models\InvitePool;
use common\models\InviteReward;
use api\models\User;
use dosamigos\qrcode\QrCode;
use api\models\FriendMoment;
use yii\imagine\Image;
use api\models\Image as ImageModel;
use yii\helpers\Url;
use Yii;
use yii\helpers\FileHelper;
use backend\models\Setting;

/**
 * 用户操作
 */
class UserActionController extends BaseController
{
    /**
     * 行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['start-page'];
        return $behaviors;
    }
    /**
     * 查找好友
     *
     * @return void
     */
    public function actionSearch()
    {
        $type = Yii::$app->request->post('type', 0);
        $username = Yii::$app->request->post('keyword');

        switch ($type) {
            //  userid
            case 1:
                $where = ['userid' => $username, 'status' => 10];
                break;
            case 2:
                $where = ['iecid' => $username, 'status' => 10];
                break;
            default:
                $where = ['username' => $username, 'status' => 10];
                break;
        }

        $data = User::find()->with(['friend'])->select(['username', 'nickname', 'userid', "iecid", 'headimgurl', 'province', 'city', 'country', 'description'])
            ->where($where)
            ->asArray()
            ->one();

        if ($data) {
            $data['images'] = ImageModel::find()
                ->select('a.thumbnail')
                ->where(['a.userid' => $data['userid']])
                ->alias('a')
                ->leftJoin(
                FriendMoment::tableName() . ' b',
                'b.id=a.origin'
            )->andWhere('b.status=10')->limit(4)->orderBy('a.created_at desc')->column();

            return APIFormat::success($data);
        } else {
            return APIFormat::error(3901, '搜索不到信息');
        }
    }

    /**
     * 智能查询
     *
     * @return void
     */
    public function actionAutoSearch()
    {
        $username = Yii::$app->request->post('keyword');

        $where = ['and', 'status=10', ['or', "username='{$username}'", "iecid='{$username}'"]];

        $data = User::find()->with(['friend'])->select(['username', 'nickname', 'userid', "iecid", 'headimgurl', 'province', 'city', 'country', 'description'])
            ->where($where)
            ->asArray()
            ->all();
        if($data){
            $list = [];
            foreach($data as $key=>$val){
                $list[$key] = $val;
                $list[$key]['images'] = ImageModel::find()->select("thumbnail")->where(['userid' => $val['userid']])->limit(4)->orderBy('created_at desc')->column();
            }
            return APIFormat::success($data);
        }
        return APIFormat::error(3902, '搜索不到信息');
    }


    /**
     * 生成海报地址
     *
     * @return void
     */
    public function actionPoster()
    {
        $posterFunction = function ($model, $basePath) {
            $url = Yii::$app->params['frontendUrl'] . '/site/index?code=' . Yii::$app->user->identity->code;
            $path = Yii::getAlias('@images/web/' . $basePath);
            $qrcode = QrCode::png($url, $path . '/qrcode.jpg', 'L', 13, 2);
            Image::watermark($model->url, $path . '/qrcode.jpg', [320,1260])->save($path . '/' . $model->id . '.jpg');
        };

        $poster = Poster::find()->where(['status' => 10])->orderBy('sort asc')->all();

        $list = [];
        foreach ($poster as $val) {
            $basePath = 'uploads/poster/' . Yii::$app->user->identity->userid;
            $path = Yii::getAlias('@images/web/' . $basePath);
            
            if (!file_exists($path)) {
                FileHelper::createDirectory($path);
            }

            //  判断是否存在
            if (!file_exists($path . '/' . $val->id . '.jpg')) {
                $posterFunction($val, $basePath);
            }
            $list[] = Yii::$app->params['imagesUrl'] . '/' . $basePath . '/' . $val->id . '.jpg';
        }

        return APIFormat::success($list);        
    }

    /**
     * 糖果海报
     *
     * @return void
     */
    public function actionCandy()
    {
//        $pool = InvitePool::getPool(Yii::$app->user->identity->pool_id, false);
        $pool = InvitePool::getPool(0, false);

        $url = Yii::$app->params['frontendUrl'] . '/site/index?code=' . Yii::$app->user->identity->code;
        //  生成海报
        $p = 'uploads/poster/user_20190815/' . Yii::$app->user->identity->userid;
        $path = Yii::getAlias('@images/web/' . $p);
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }

        //  判断是否存在
        $md5ImageName = md5($pool->icon . Yii::$app->user->identity->pool_id . $pool->prize . $pool->prize_registerer);
        $imageName = '/' . $md5ImageName . '_candy.png';
        if (!file_exists($path . $imageName)) {
            $qr = Yii::$app->qr->setSize(186)->setText($url)->writeFile($path . '/qrcode.png');

            Image::watermark($pool->background, $path . '/qrcode.png', [450, 950])->save($path . $imageName);
            // $m1Amount = sprintf("%.2f", $pool->prize * $pool->prize_registerer / 10);
            // Image::text($path . $imageName, $m1Amount . ' ' .$pool->symbol, Yii::getAlias('@images/web/images/font/MSYH.TTF'), [500,762], ['size' => '34'])->save($path . $imageName);
            //添加昵称 头像
            $fontFile = Yii::getAlias('@images/web/images/font/MSYH.TTF');
            $headimgurl = yii::$app->user->identity->headimgurl;
            $headimgurlPath = $path. '/headimgurl.png';  //生成头像地址
            Image::thumbnail($headimgurl,50,50)->save($headimgurlPath);//头像压缩
            Image::watermark($path . $imageName, $headimgurlPath, [450,1200])->save($path . $imageName);
            Image::text($path . $imageName, Yii::$app->user->identity->nickname, $fontFile, [550,1210],['size' => 28])->save($path . $imageName);

            //  如果存在ICON    那么添加
            if($pool->icon){
                Image::watermark($path . $imageName, $pool->icon, [550, 170])->save($path . $imageName);
            }
        }
        //  邀请人数统
        if($pool->type == 1){
            $createTime = 0;
        }else{
            $createTime = $pool->created_at;
        }
        $number = Invitation::find()->select(["count(level) as num", "level"])->where(['AND', ['=', 'inviter_id', Yii::$app->user->identity->id], ['>', 'created_at', $createTime]])->groupBy('level')->asArray()->all();
        $amountSum = InviteReward::find()->where(['symbol' => $pool->symbol, 'user_id_rewarded' => Yii::$app->user->identity->id])->sum('amount');
        if($amountSum){
            $amountSum = sprintf("%.2f", $amountSum);
        }else{
            $amountSum = "0.00";
        }
        $rs = [
            'm1' => 0,
            'm2' => 0,
            'm3' => 0,
        ];
        foreach($number as $val){
            if($val['level'] == 1){
                $rs['m1']+=$val['num'];
            }elseif($val['level'] == 2){
                $rs['m2'] += $val['num'];
            }elseif($val['level'] == 3){
                $rs['m3'] += $val['num'];
            }
        }
        return APIFormat::success([
            'name' => $pool->name,
            'nums' => [
                'max' => sprintf("%.2f", $pool->amount),
                'left' => sprintf("%.2f", $pool->amount_left),
                'rate' => sprintf("%.4f", $pool->amount_left / $pool->amount),
            ],
            'number' => array_sum($rs),
            'amount_sum' => $amountSum,
            'link_url' => $url,
            'poster_url' => Yii::$app->params['imagesUrl'] . '/' . $p . $imageName,
            'explan_url' => $pool->url,
            'description' => $pool->description,
            'symbol' => $pool->symbol,
            'level' => [
                ['M1', $pool->prize * $pool->prize_inviter / 10, Setting::read('mining_m1'), $rs['m1']],
                ['M2', $pool->prize * $pool->prize_grand_inviter / 10, Setting::read('mining_m2'), $rs['m2']],
                ['M3', $pool->prize * $pool->prize_grand_grand_inviter / 10, Setting::read('mining_m3'), $rs['m3']]
            ],
        ]);
    }


    /**
     * 启动页
     *
     * @return void
     */
    public function actionStartPage()
    {
        $page = Setting::find()->where(['key' => 'init_page'])->one();
        $url = $page ? Yii::$app->request->hostInfo . '/' . $page['value'] : '';
        return APIFormat::success($url);
    }

}
