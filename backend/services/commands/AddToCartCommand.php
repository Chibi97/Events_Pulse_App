<?php

namespace app\services\commands;

use Yii;
use Exception;
use app\models\Cart;
use app\services\CartService;
use app\exceptions\TicketPulseException;

class AddToCartCommand
{
    private ?int $cartId;
    private int $eventId;
    private int $quantity;

    private CartService $cartService;

    public function __construct(
        int $cartId,
        int $eventId,
        int $quantity,
        CartService $cartService
    ) {
        $this->cartId = $cartId;
        $this->eventId = $eventId;
        $this->quantity = $quantity;
        $this->cartService = $cartService;
    }

    /**
     * Execute the command to add an event to the cart
     *
     * @return Cart
     * @throws TicketPulseException|Exception
     */
    public function execute(): Cart
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $event = $this->cartService->validateEventForCart($this->eventId);
            $cart = $this->cartService->validateCartExists($this->cartId);

            $this->cartService->addOrUpdateCartItem($cart, $event, $this->quantity);

            $this->cartService->recalculateCartTotals($cart);
            $transaction->commit();

            return $cart;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
