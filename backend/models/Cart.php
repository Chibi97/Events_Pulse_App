<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * A Model class for table "{{%carts}}".
 *
 * @property int $id
 * @property float $subtotal
 * @property float $delivery_price
 * @property float $total
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Cart extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%carts}}';
    }

    public function rules(): array
    {
        return [
            [['subtotal', 'delivery_price', 'total'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getCartItems(): ActiveQuery
    {
        return $this->hasMany(CartItem::class, ['cart_id' => 'id']);
    }

    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['id' => 'event_id'])->via('cartItems');

    }

    public function toArray($fields = [], $expand = [], $recursive = true): array
    {
        $fields[] = 'id';
        $fields[] = 'subtotal';
        $fields[] = 'total';
        $fields[] = 'delivery_price';
        $expand[] = 'cartItems';
        $fields[] = 'cartItems.id';
        $fields[] = 'cartItems.event_id';
        $fields[] = 'cartItems.cart_id';
        $fields[] = 'cartItems.quantity';
        $fields[] = 'cartItems.price';

        $to_format = ['subtotal', 'total', 'delivery_price'];
        foreach ($to_format as $field) {
            $this->$field = number_format($this->$field, 2, '.', '');
        }

        return parent::toArray($fields, $expand);
    }
}
