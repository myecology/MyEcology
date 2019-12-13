<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_bank_product`.
 */
class m181016_093718_create_iec_bank_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_bank_product', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('名称'),
            'symbol' => $this->string()->notNull()->comment('币种标识'),
            'amount' => $this->money(20,8)->notNull()->comment('数量'),
            'rate' => $this->money(20,8)->notNull()->comment('收益率'),
            'super_rate' => $this->money(20,8)->notNull()->comment('超级节点收益'),
            'min_amount' => $this->money(20,8)->notNull()->comment('最小数量'),
            'max_amount' => $this->money(20,8)->notNull()->comment('最大数量'),
            'user_amount' => $this->money(20,8)->notNull()->comment('个人最大数量'),
            'income_id' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('收益类型'),
            'income_description' => $this->string()->notNull()->comment('收益描述'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('产品类型'),
            'fee' => $this->money(20,8)->notNull()->comment('费用'),
            'fee_explain' => $this->string()->notNull()->comment('费用说明'),
            'day' => $this->smallInteger()->notNull()->comment('周期天数'),
            'description' => $this->string()->notNull()->comment('描述'),
            'statime' => $this->integer()->notNull()->comment('开始时间'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'endtime' => $this->integer()->notNull()->comment('结束时间'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_bank_product');
    }
}
