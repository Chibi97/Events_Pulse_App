<?php

namespace app\repositories;

use app\models\Cart;
use app\models\CartItem;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

class CartRepository
{
    /**
     * Fetch a specific cart by ID
     *
     * @param int $id
     * @return Cart|null
     */
    public function getCartById(int $id): ?Cart
    {
        return Cart::findOne($id);
    }

    /**
     * Fetch a specific cart item by ID
     *
     * @param int $id
     * @return CartItem
     */
    public function getCartItemById(int $id): ?CartItem
    {
        return CartItem::findOne($id);
    }

    /**
     * Fetch a specific cart by ID
     *
     * @param int $id
     * @return Cart|array|ActiveRecord|null
     */
    public function getCartWithItemsById(int $id): ?Cart
    {
        return Cart::find()
            ->where(['id' => $id])
            ->with(['cartItems', 'cartItems.event'])
            ->one();
    }

    /**
     * Fetch a specific cart by ID and its expired items
     *
     * @param int $id
     * @return Cart|array|ActiveRecord|null
     */
    public function getExpiredCartItemsForCartId(int $id): ?Cart
    {
        return Cart::find()
            ->where(['id' => $id])
            ->with([
                'cartItems' => function ($query) {
                    $query->joinWith('event')->where(['<', 'date', new Expression('NOW()')]);
                },
                'cartItems.event'
            ])
            ->one();
    }

    /**
     * Retrieve an existing cart item
     *
     * @param int $cart_id
     * @param int $event_id
     * @return ActiveRecord|array|null|CartItem
     */
    public function getCartItem(int $cart_id, int $event_id): ?CartItem
    {
        return CartItem::find()
            ->where(['cart_id' => $cart_id, 'event_id' => $event_id])
            ->one();
    }

    /**
     * @param int $cart_id
     * @return bool|int
     */
    public function recalculateCartTotals(int $cart_id)
    {
        return CartItem::find()
            ->where(['cart_id' => $cart_id])
            ->sum('quantity * price');
    }

    /**
     * Includes cartItems (with an event relation) in the cart
     *
     * @param Cart $cart
     * @return void
     */
    public function populateCartItems(Cart $cart): void
    {
        $cart->populateRelation('cartItems', $cart->getCartItems()->with('event')->all());
    }

    /**
     * Extract the cart from a cart item
     *
     * @param CartItem $cart_item
     * @return CartItem|array|ActiveRecord|null
     */
    public function getCartFromCartItem(CartItem $cart_item): ?Cart
    {
        return $cart_item->getCart()->one();
    }

    /**
     * Save a cart (update or create)
     *
     * @param Cart $cart
     * @return bool
     * @throws Exception
     */
    public function saveCart(Cart $cart): bool
    {
        return $cart->save();
    }

    /**
     * Save a cart item (update or create)
     *
     * @param CartItem $cart_item
     * @return bool
     * @throws Exception
     */
    public function saveCartItem(CartItem $cart_item): bool
    {
        return $cart_item->save();
    }

    /**
     * Remove an item from the cart
     *
     * @param CartItem $cart_item
     * @return bool
     * @throws Exception|\Throwable
     */
    public function removeCartItem(CartItem $cart_item): bool
    {
        return $cart_item->delete() !== false;
    }
}
