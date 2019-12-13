<?php
namespace api\modules\v1\controllers;

use Yii;
use api\controllers\APIFormat;
use api\models\FriendMoment;
use api\models\FriendMomentReply;
use yii\data\Pagination;

/**
 * 朋友圈
 */
class FriendHotMomentController extends BaseController
{
    /**
     * 热门朋友圈列表
     *
     * @return void
     */
    public function actionIndex()
    {
        $endtime = time();
        $statime = $endtime - 86400 * 2;
        $data = FriendMoment::find()->with(['user', 'image', 'isMomentLike'])->where([
            'AND',
            ['between', 'created_at', $statime, $endtime],
            ['=', 'status', 10],
        ]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['hot' => SORT_DESC, 'created_at' => SORT_DESC])
            ->asArray()
            ->all();

        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_meta' => $_meta]);
    }

    /**
     * 详情
     *
     * @return void
     */
    public function actionDetails()
    {
        $momentid = Yii::$app->request->post('momentid');

        $data = FriendMoment::find()->with(['user', 'image'])->where(['id' => $momentid])->asArray()->one();
        return APIFormat::success($data);
    }

    /**
     * 回复详情
     *
     * @return void
     */
    public function actionReply()
    {
        $momentid = Yii::$app->request->get('momentid');

        $data = FriendMomentReply::find()->with(['inUser', 'toUser'])->where(['momentid' => $momentid, 'status' => 10]);
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
            ->all();

        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_meta' => $_meta]);
    }

}
