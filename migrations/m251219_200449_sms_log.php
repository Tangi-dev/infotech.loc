<?php

use yii\db\Migration;

/**
 * Class m251219_200449_sms_log
 */
class m251219_200449_sms_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sms_log}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'message' => $this->text()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'),
            'response' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx-sms_log-phone', '{{%sms_log}}', 'phone');
        $this->createIndex('idx-sms_log-status', '{{%sms_log}}', 'status');
        $this->createIndex('idx-sms_log-created_at', '{{%sms_log}}', 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_log}}');
    }
}
