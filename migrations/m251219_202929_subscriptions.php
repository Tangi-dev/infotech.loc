<?php

use yii\db\Migration;

/**
 * Class m251219_202929_subscriptions
 */
class m251219_202929_subscriptions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscriptions}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey(
            'fk-subscriptions-author_id',
            '{{%subscriptions}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx-subscriptions-phone', '{{%subscriptions}}', 'phone');
        $this->createIndex('idx-subscriptions-author_id', '{{%subscriptions}}', 'author_id');
        $this->createIndex('idx-subscriptions-phone_author', '{{%subscriptions}}', ['phone', 'author_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subscriptions-author_id', '{{%subscriptions}}');
        $this->dropTable('{{%subscriptions}}');
    }
}
