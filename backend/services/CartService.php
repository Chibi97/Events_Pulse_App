<?php

namespace app\services;

use Yii;
use Exception;
use yii\db\ActiveRecord;
use app\helpers\EventHelper;
use app\models\Event;
use app\repositories\CartRepository;
use app\services\commands\AddToCartCommand;
use app\models\Cart;
use app\models\CartItem;
use app\exceptions\TicketPulseException;

class CartService
{
    private PricingService $pricingService;
    private EventService $eventService;
    private CartRepository $cartRepository;

    public function __construct(
        PricingService $pricingService,
        EventService $eventService,
        CartRepository $cartRepository
    ) {
        $this->pricingService = $pricingService;
        $this->eventService = $eventService;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Fetch a specific cart by ID
     *
     * @param int $id
     * @return Cart|null
     */
    public function getCartById(int $id): ?Cart
    {
        return $this->cartRepository->getCartById($id);
    }

    /**
     * Fetch a specific cart by ID
     *
     * @param int $id
     * @return Cart|array|ActiveRecord|null
     */
    public function getCartWithItemsById(int $id): ?Cart
    {
        return $this->cartRepository->getCartWithItemsById($id);
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
        return $this->cartRepository->getCartItem($cart_id, $event_id);
    }

    /**
     * Retrieve an existing cart item by its ID
     *
     * @param int $cart_item_id
     * @return ActiveRecord|array|null|CartItem
     */
    public function getCartItemById(int $cart_item_id): ?CartItem
    {
        return $this->cartRepository->getCartItemById($cart_item_id);
    }


    /**
     * Includes cartItems (with an event relation) in the cart
     *
     * @param Cart $cart
     * @return void
     */
    public function populateCartItems(Cart $cart): void
    {
        $this->cartRepository->populateCartItems($cart);
    }


    /**
     * Add an event to the cart
     *
     * @param ?int $cart_id
     * @param int $event_id
     * @param int $quantity
     * @return Cart
     * @throws TicketPulseException|Exception
     */
    public function addToCart(int $cart_id, int $event_id, int $quantity = 1): Cart
    {
        if (!isset($cart_id)) {
            throw new TicketPulseException('Cart ID is required', 500, null, [
                'method' => 'addToCart',
            ]);
        }
        if (!isset($event_id)) {
            throw new TicketPulseException('Event ID is required', 400, null, [
                'method' => 'addToCart',
            ]);
        }

        $command = new AddToCartCommand($cart_id, $event_id, $quantity, $this);
        return $command->execute();
    }

    /**
     * @return Cart
     */
    public function prepareCart(): Cart
    {
        $cart = new Cart();
        $cart->delivery_price = 0; // Free for now, later it can be based on location and item quantity
        $cart->subtotal = 0; // Will be updated when item is added
        $cart->total = 0; // Will be updated when item is added

        return $cart;
    }

    /**
     * @return Cart
     * @throws TicketPulseException|\yii\db\Exception
     */
    public function createCart(): Cart
    {
        $cart = $this->prepareCart();
        $saved_successfully = $this->cartRepository->saveCart($cart);
        if (!$saved_successfully) {
            throw new TicketPulseException('Failed to create a new cart', 500, null, [
                'method' => 'createCart',
            ]);
        }
        return $cart;
    }

    /**
     * Prepare cart item for saving
     *
     * @param int $cart_id
     * @param Event $event
     * @param int $quantity
     * @return CartItem
     * @throws TicketPulseException
     */
    public function prepareCartItem(int $cart_id, Event $event, int $quantity = 1): CartItem
    {
        if (!isset($cart_id) || !$event instanceof Event) {
            throw new TicketPulseException('Cart ID and Event are required', 400, null, [
                'method' => 'prepareCartItem',
            ]);
        }

        $cart_item = new CartItem();
        $cart_item->cart_id = $cart_id;
        $cart_item->event_id = $event->id;
        $cart_item->quantity = $quantity <= 0 ? 1 : $quantity;
        $cart_item->price = $this->pricingService->calculatePrice($event);

        return $cart_item;
    }

    /**
     * Create a new cart item
     *
     * @param Cart $cart
     * @param Event $event
     * @param int $quantity
     * @return void
     * @throws TicketPulseException
     * @throws \yii\db\Exception
     */
    public function createCartItem(Cart $cart, Event $event, int $quantity = 1): CartItem
    {
        if (!$cart instanceof Cart || !$event instanceof Event) {
            throw new TicketPulseException('Cart ID and Event are required', 400, null, [
                'method' => 'createCartItem',
            ]);
        }

        $cart_item = $this->prepareCartItem($cart->id, $event, $quantity);

        $saved_successfully = $this->cartRepository->saveCartItem($cart_item);
        if (!$saved_successfully) {
            throw new TicketPulseException('Failed to add event to the cart', 500, null, [
                'method' => 'createCartItem',
            ]);
        }

        return $cart_item;
    }


    /**
     * Validate if a cart exists
     *
     * @param int $cart_id
     * @return Cart|null
     * @throws TicketPulseException
     */
    public function validateCartExists(int $cart_id): ?Cart
    {
        if (empty($cart_id)) {
            throw new TicketPulseException("Cart ID is required", 500, null, [
                'method' => 'validateCartExists'
            ]);
        }

        $cart = $this->getCartById($cart_id);
        if (!$cart instanceof Cart) {
            throw new TicketPulseException("Cart you are querying is not found", 500, null, [
                'method' => 'validateCartExists'
            ]);
        }
        return $cart;
    }


    /**
     * Validate if an event is valid for adding to the cart
     *
     * @param int $event_id
     * @return Event
     * @throws TicketPulseException|Exception
     */
    public function validateEventForCart(int $event_id): Event
    {
        if (empty($event_id)) {
            throw new TicketPulseException('Event ID is required', 500, null, [
                'method' => 'validateEventForCart'
            ]);
        }

        $event = $this->eventService->getEventById($event_id);
        if (!$event instanceof Event) {
            throw new TicketPulseException('Ticket you are trying to add to cart is not found', 404, null, [
                'method' => 'validateEventForCart'
            ]);
        }

        $is_event_in_future = EventHelper::validateEventIsInFuture($event->date);
        if (!$is_event_in_future) {
            throw new TicketPulseException('Cannot add expired event to the cart', 400, null, [
                'method' => 'validateEventForCart'
            ]);
        }

        return $event;
    }

    /**
     * Update the quantity of an existing cart item
     *
     * @param CartItem $cart_item
     * @param int $quantity
     * @param string $mode 'override' or 'add' quantity
     * @return CartItem
     * @throws TicketPulseException|\yii\db\Exception
     */
    public function updateCartItemQuantity(CartItem $cart_item, int $quantity = 1, string $mode = 'override'): CartItem
    {
        if (!$cart_item instanceof CartItem) {
            throw new TicketPulseException('Cart item is required', 400, null, [
                'method' => 'updateCartItemQuantity',
            ]);
        }

        if ($mode === 'add') {
            $cart_item->quantity += $quantity;
        } else if ($mode === 'override') {
            $cart_item->quantity = $quantity;
        }

        $saved_successfully = $this->cartRepository->saveCartItem($cart_item);
        if (!$saved_successfully) {
            throw new TicketPulseException('Failed to update cart item quantity', 500, null, [
                'method' => 'updateCartItemQuantity',
            ]);
        }

        return $cart_item;
    }


    /**
     * @param Cart $cart
     * @param Event $event
     * @param int $quantity
     * @return CartItem|null
     * @throws TicketPulseException
     * @throws \yii\db\Exception
     */
    public function addOrUpdateCartItem(Cart $cart, Event $event, int $quantity = 1): ?CartItem
    {
        if (!$cart instanceof Cart || !$event instanceof Event) {
            throw new TicketPulseException('Cart and Event are required', 400, null, [
                'method' => 'addOrUpdateCartItem',
            ]);
        }

        $cart_item = $this->getCartItem($cart->id, $event->id);

        if ($cart_item instanceof CartItem) {
            return $this->updateCartItemQuantity($cart_item, $quantity, 'add');
        }
        return $this->createCartItem($cart, $event, $quantity);
    }

    /**
     * Remove an item from the cart
     *
     * @param int $cart_item_id
     * @return bool
     * @throws TicketPulseException
     */
    public function removeCartItem(int $cart_item_id): bool
    {
        if (!isset($cart_item_id)) {
            throw new TicketPulseException('Cart Item ID is required', 400, null, [
                'method' => 'removeCartItem',
            ]);
        }

        $cart_item = CartItem::findOne($cart_item_id);
        if (!$cart_item instanceof CartItem) {
            throw new TicketPulseException("Cart item you are trying to delete is not found", 404);
        }

        try {
            return $this->cartRepository->removeCartItem($cart_item);
        } catch (\Exception | \Throwable $e) {
            throw new TicketPulseException('Failed to delete cart item', 500, $e, [
                'method' => 'removeCartItem',
            ]);
        }
    }

    /**
     * Update cart item quantity and recalculate cart totals
     *
     * @param CartItem $cart_item
     * @param int $quantity
     * @return Cart|array|null|ActiveRecord
     * @throws \yii\db\Exception|TicketPulseException
     */
    public function updateCartQuantityAndRecalculateTotals(CartItem $cart_item, int $quantity): Cart
    {
        if (!$cart_item instanceof CartItem) {
            throw new TicketPulseException('Cart item is required', 400, null, [
                'method' => 'updateCartQuantityAndRecalculateTotals',
            ]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->validateEventForCart($cart_item->event_id);
            $item = $this->updateCartItemQuantity($cart_item, $quantity, 'override');

            $cart = $this->cartRepository->getCartFromCartItem($item);
            $this->recalculateCartTotals($cart);

            $transaction->commit();
            $cart->refresh();
            return $cart;
        } catch (Exception $e) {
            throw new TicketPulseException('Failed to update cart item quantity and recalculate cart totals', 500, $e, [
                'method' => 'updateCartQuantityAndRecalculateTotals',
            ]);
        }
    }

    /**
     * Remove an item from the cart and recalculate cart totals
     *
     * @param int $cart_id
     * @param int $cart_item_id
     * @return Cart
     * @throws TicketPulseException
     */
    public function removeCartItemAndRecalculateTotals(int $cart_id, int $cart_item_id): Cart
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->removeCartItem($cart_item_id);
            $cart = $this->getCartById($cart_id);
            $this->recalculateCartTotals($cart);

            $transaction->commit();
            $cart->refresh();
            return $cart;
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new TicketPulseException('Failed to delete cart item quantity and recalculate cart totals', 500, $e, [
                'method' => 'removeCartItemAndRecalculateTotals',
            ]);
        }
    }

    /**
     * Recalculate cart subtotal and total
     *
     * @param Cart $cart
     * @throws \yii\db\Exception|TicketPulseException
     */
    public function recalculateCartTotals(Cart $cart): void
    {
        if (!$cart instanceof Cart) {
            throw new TicketPulseException('Cart is required', 500, null, [
                'method' => 'recalculateCartTotals'
            ]);
        }

        $subtotal = $this->cartRepository->recalculateCartTotals($cart->id) ?? 0;

        $cart->subtotal = $subtotal;
        $cart->total = $subtotal + ($cart->delivery_price ?? 0);

        if (!$this->cartRepository->saveCart($cart)) {
            throw new TicketPulseException('Failed to update cart totals', 500, null, [
                'method' => 'recalculateCartTotals',
            ]);
        }
    }
}
