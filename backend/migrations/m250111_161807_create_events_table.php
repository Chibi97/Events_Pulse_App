<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Handles the creation of table `{{%events}}`.
 */
class m250111_161807_create_events_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%events}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'date' => $this->dateTime()->notNull(),
            'image_url' => $this->string(512),
            'base_price' => $this->decimal(10, 2)->notNull(),
            'current_price' => $this->decimal(10, 2)->notNull(),
            'is_for_sale' => $this->boolean()->notNull()->defaultValue(1),
            'event_type' => $this->string(50)->notNull()->defaultValue('Basic'),
            'status' => $this->string(50)->notNull()->defaultValue('Upcoming'),
            'created_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'updated_at' => $this->timestamp()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP'))->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->execute("ALTER TABLE {{%events}} CHANGE COLUMN `event_type` `event_type` ENUM('Basic', 'VIP', 'Exclusive') NOT NULL");
        $this->execute("ALTER TABLE {{%events}} CHANGE COLUMN `status` `status` ENUM('Upcoming', 'Past', 'CollectorsItem') NOT NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%events}}');
    }
}
