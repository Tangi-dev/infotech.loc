<?php

use yii\db\Migration;

/**
 * Class m251219_132124_books
 */
class m251219_132124_books extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year' => $this->integer(4)->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(20)->notNull()->unique(),
            'image' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx-books-title', '{{%books}}', 'title');
        $this->createIndex('idx-books-year', '{{%books}}', 'year');
        $this->createIndex('idx-books-isbn', '{{%books}}', 'isbn', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
