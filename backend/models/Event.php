<?php

namespace app\models;

use app\enums\Event\EventState;
use app\enums\Event\EventStatus;
use app\enums\Event\EventType;
use yii\db\ActiveQuery;

/**
 * A Model class for table "{{%events}}".
 *
 * @property int $id
 * @property string $name
 * @property string $date
 * @property string|null $image_url
 * @property float $base_price
 * @property float $current_price
 * @property int $is_for_sale
 * @property string $event_type
 * @property string $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Event extends BaseModel
{
    const EVENT_TYPE_BASE_PRICES = [
        'Basic' => 39.99,
        'VIP' => 79.99,
        'Exclusive' => 200.00,
    ];

    public static function tableName(): string
    {
        return '{{%events}}';
    }

    public static function assignEventBasePrice($event_type): ?float
    {
        return self::EVENT_TYPE_BASE_PRICES[$event_type] ?? null;
    }

    public function getCartItems(): ActiveQuery
    {
        return $this->hasMany(CartItem::class, ['event_id' => 'id']);
    }

    public function rules(): array
    {
        return [
            [['name', 'event_type'], 'required'],
            [['name', 'date', 'created_at', 'updated_at', 'image_url'], 'safe'],
            [['base_price', 'current_price'], 'number'],
            [['is_for_sale'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['image_url'], 'string', 'max' => 512],
            [
                ['event_type'],
                'in',
                'range' => [EventType::BASIC, EventType::VIP, EventType::EXCLUSIVE],
                'message' => 'Invalid event type, it needs to be `Basic`, `VIP` or `Exclusive`'
            ],
            [['event_type'], 'default', 'value' => EventType::BASIC],
            [
                ['status'],
                'in',
                'range' => [EventStatus::UPCOMING, EventStatus::PAST, EventStatus::COLLECTORS_ITEM],
                'message' => 'Invalid event status, it needs to be `Upcoming`, `Past` or `CollectorsItem`'
            ],
            [['status'], 'default', 'value' => EventStatus::UPCOMING],
            [['is_for_sale'], 'default', 'value' => EventState::IS_FOR_SALE],
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['name', 'date', 'current_price', 'event_type', 'image_url'];
        $scenarios['update'] = ['name', 'date', 'event_type', 'image_url'];
        return $scenarios;
    }

    public function toArray($fields = [], $expand = [], $recursive = true): array
    {
        $data = parent::toArray($fields, $expand, $recursive);
        $fields_to_format = ['base_price', 'current_price'];
        $fields_to_bool = ['is_for_sale'];

        foreach ($fields_to_format as $field) {
            if (isset($data[$field])) {
                $data[$field] = number_format($this->$field, 2, '.', '');

            }
        }

        foreach ($fields_to_bool as $field) {
            if (isset($data[$field])) {
                $data[$field] = (bool)$data[$field];
            }
        }

        return $data;
    }
}
