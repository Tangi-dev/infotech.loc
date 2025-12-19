<?php

use yii\db\Migration;

/**
 * Class m251219_124415_author
 */
class m251219_124415_author extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%authors}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'middle_name' => $this->string(100),
            'last_name' => $this->string(100)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx-authors-last_name', '{{%authors}}', 'last_name');
        $this->createIndex('idx-authors-full_name', '{{%authors}}', ['last_name', 'first_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%authors}}');
    }
}
