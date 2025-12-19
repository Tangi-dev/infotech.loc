<?php

use yii\db\Migration;

/**
 * Class m251222_075608_add_author_id_to_sms_log
 */
class m251222_075608_add_author_id_to_sms_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sms_log', 'author_id', $this->integer()->null()->after('phone'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('sms_log', 'author_id');
    }
}
