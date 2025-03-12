<?php

namespace app\models;

use yii\db\ActiveRecord;
use \yii\db\ActiveQuery;

/**
 * A Model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $cart_id
 * @property int|null $event_id
 * @property int $quantity
 * @property float $price
 */
class CartItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%cart_items}}';
    }

    public function rules(): array
    {
        return [
            [['event_id'], 'required'],
            [['cart_id', 'event_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getCart(): ActiveQuery
    {
        return $this->hasOne(Cart::class, ['id' => 'cart_id']);
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    public function toArray($fields = [], $expand = [], $recursive = true): array
    {
        $expand[] = 'event';
        $fields[] = 'event.id';
        $fields[] = 'event.name';
        $fields[] = 'event.date';
        $fields[] = 'event.base_price';
        $fields[] = 'event.current_price';
        $fields[] = 'event.is_for_sale';
        $fields[] = 'event.event_type';
        $fields[] = 'event.image_url';

        $to_format = ['price'];
        foreach ($to_format as $field) {
            $this->$field = number_format($this->$field, 2, '.', '');
        }

        return parent::toArray($fields, $expand);
    }
}
