<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%cart_items}}`.
 */
class m250111_174444_create_cart_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cart_items}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer()->null(),
            'cart_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'updated_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP'))->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-cart_items-event_id',
            'cart_items',
            'event_id'
        );
        $this->addForeignKey(
            'fk-cart_items-event_id',
            'cart_items',
            'event_id',
            'events',
            'id',
            'SET NULL'
        );

        $this->createIndex(
            'idx-cart_items-cart_id',
            'cart_items',
            'cart_id'
        );
        $this->addForeignKey(
            'fk-cart_items-cart_id',
            'cart_items',
            'cart_id',
            'carts',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-cart_items-event_id', 'cart_items');
        $this->dropForeignKey('fk-cart_items-cart_id', 'cart_items');
        $this->dropIndex('idx-cart_items-event_id', 'cart_items');
        $this->dropIndex('idx-cart_items-cart_id', 'cart_items');
        $this->dropTable('{{%cart_items}}');
    }
}
