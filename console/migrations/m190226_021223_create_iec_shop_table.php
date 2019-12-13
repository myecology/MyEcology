<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_shop`.
 */
class m190226_021223_create_iec_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_shop', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->defaultValue(null)->comment('商家名称'),
            'contact' => $this->string(32)->defaultValue(null)->comment('联系人'),
            'phone' => $this->string(32)->defaultValue(null)->comment('电话号码'),
            'type_id' => $this->integer(11)->defaultValue(0)->comment('行业类别'),
            'province_id' => $this->integer(11)->defaultValue(0)->comment('省id'),
            'city_id' => $this->integer(11)->defaultValue(0)->comment('市id'),
            'district_id' => $this->integer(11)->defaultValue(0)->comment('区id'),
            'address' => $this->string(255)->defaultValue(null)->comment('详细地址'),
            'introduction' =>  $this->text()->defaultValue(null)->comment('店铺简介'),
            'license' => $this->text()->defaultValue(null)->comment('营业执照'),
            'store_photos' => $this->text()->defaultValue(null)->comment('店铺照片'),
            'status' => $this->tinyInteger(4)->comment('审核状态 0审核中 10已通过 20已拒绝'),
            'refuse_reason' => $this->text()->comment('拒绝原因'),
            'created_at' => $this->integer(11)->comment('提交时间'),
            'updated_at' => $this->tinyInteger(11)->comment('更新时间'),
        ],"COMMENT '商家表'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_shop');
    }
}
