<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_wallet`.
 */
class m180911_151336_create_iec_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_currency', [
            'id' => $this->primaryKey(),
            'symbol' => $this->string(32)->notNull()->comment('英文缩写'),
            'description' => $this->string(128)->notNull()->comment('中文描述'),
            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('最后修改时间'),
            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),
            'model' => $this->string(32)->comment('币种模型'),
        ], 'comment "币种列表"');

        $this->createTable('iec_currency_param', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'key' => $this->string(128)->notNull()->comment('配置名'),
            'value' => $this->text()->comment('配置值'),
            'updated_at' => $this->integer()->comment('更改时间'),

        ], 'comment "币种配置"');

        $this->createTable('iec_currency_price', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'price' => $this->money(20,8)->defaultValue(0)->comment('中文描述'),
            'updated_at' => $this->Integer()->comment('最后修改时间'),
            'updated_date' => $this->integer()->comment('最后修改日期'),
            'source' => $this->string(128)->comment('来源标识'),
        ], 'comment "币种价格"');

        $this->createTable('iec_wallet', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('用户ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),
            'amount_lock' => $this->money(20, 8)->defaultValue(0)->comment('锁定金额'),
            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('更新时间'),
        ], 'comment "用户账户钱包"');

        $this->createTable('iec_wallet_log', [
            'id' => $this->primaryKey(),
            'wallet_id' => $this->integer()->comment('钱包ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'user_id' => $this->integer()->comment('用户ID'),
            'type' => $this->integer()->comment('类型'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),
            'balance' => $this->money(20, 8)->defaultValue(0)->comment('帐变前余额'),
            'created_at' => $this->integer()->comment('创建时间'),
            'remark' => $this->text()->comment('备注'),
        ], 'comment "用户账户钱包"');

        $this->createTable('iec_user_address', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('用户ID'),
            'name' => $this->string(64)->notNull()->comment('用户备注'),
            'address' => $this->string(128)->notNull()->comment('钱包地址'),
            'created_at' => $this->integer()->comment('钱包地址'),
        ], 'comment "用户地址钱包"');

        $this->createTable('iec_gift_money', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->comment('发红包用户ID'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),
            'amount_left' => $this->money(20, 8)->defaultValue(0)->comment('剩余金额'),
            'created_at' => $this->integer()->comment('创建时间'),
            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),
            'type' => $this->smallInteger(3)->defaultValue(10)->comment('类型'),
            'amount_unit' => $this->money(20,8)->defaultValue(0)->comment('单个金额'),
            'count' => $this->integer()->defaultValue(0)->comment('红包个数'),
        ], 'comment "红包"');

        $this->createTable('iec_gift_money_taker', [
            'id' => $this->primaryKey(),
            'taker_id' => $this->integer()->comment('领红包用户ID'),
            'created_at' => $this->integer()->comment('领取时间'),
            'amount' => $this->money(20, 8)->comment('领取金额'),
        ], 'comment "红包领取记录"');

        $this->createTable('iec_invite_pool', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'amount' => $this->money(28, 8)->defaultValue(0)->comment('奖金池金额'),
            'amount_left' => $this->money(28, 8)->defaultValue(0)->comment('奖金池剩余'),
            'created_at' => $this->integer()->comment('创建时间'),
            'expired_at' => $this->integer()->comment('过期时间'),
            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),
            'type' => $this->smallInteger(3)->defaultValue(0)->comment('0:项目方糖果，1:官方糖果'),
            'name' => $this->string(50)->notNull()->comment('糖果名称'),
            'icon' => $this->string()->notNull()->comment('图标'),
            'background' => $this->string()->notNull()->comment('糖果海报图片'),
            'uid' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
            'url' => $this->string()->notNull()->defaultValue('')->comment('白皮书链接'),
            'description' => $this->string()->notNull()->comment('描述'),
            'prize' => $this->money(28, 8)->defaultValue(0)->comment('奖金包金额'),
            'prize_registerer' => $this->integer()->comment('注册人比重'),
            'prize_inviter' => $this->integer()->comment('邀请人比重'),
            'prize_grand_inviter' => $this->integer()->comment('父级邀请人比重'),
        ], 'comment "红包领取记录"');

        $this->createTable('iec_invitation', [
            'id' => $this->primaryKey(),
            'registerer_id' => $this->integer()->comment('注册人ID'),
            'inviter_id' => $this->integer()->comment('邀请人ID'),
            'created_at' => $this->integer()->comment('创建时间'),
        ], 'comment "注册邀请记录"');

        $this->createTable('iec_invite_reward', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->comment('邀请记录ID'),
            'registerer_id' => $this->integer()->comment('注册人ID'),
            'level' => $this->integer()->defaultValue(1)->comment('层级'),

            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'amount' => $this->money(20,8)->comment('奖励金额'),
            'created_at' => $this->integer()->comment('创建时间'),
        ], 'comment "邀请注册奖励记录"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_currency');
        $this->dropTable('iec_currency_param');
        $this->dropTable('iec_currency_price');
        $this->dropTable('iec_wallet');
        $this->dropTable('iec_wallet_log');
        $this->dropTable('iec_user_address');
        $this->dropTable('iec_gift_money');
        $this->dropTable('iec_gift_money_taker');
        $this->dropTable('iec_invite_pool');
        $this->dropTable('iec_invitation');
        $this->dropTable('iec_invite_reward');
    }
}
