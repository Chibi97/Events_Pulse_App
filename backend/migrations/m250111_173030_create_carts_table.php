<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%carts}}`.
 */
class m250111_173030_create_carts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%carts}}', [
            'id' => $this->primaryKey(),
            'subtotal' => $this->decimal(10, 2)->notNull(),
            'delivery_price' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'total' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'updated_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP'))->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carts}}');
    }
}
