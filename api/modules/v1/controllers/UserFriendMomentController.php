<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\FriendMoment;
use yii\data\Pagination;

/**
 * 个人朋友圈
 */
class UserFriendMomentController extends BaseController
{

    //  个人朋友圈列表
    public function actionIndex($userid)
    {
        $data = FriendMoment::find()->with(['user', 'image', 'momentLike', 'isMomentLike', 'momentReply'])->where(['userid' => $userid, 'status' => 10]);

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
