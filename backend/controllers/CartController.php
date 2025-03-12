<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\exceptions\TicketPulseException;
use yii\web\Response;
use app\models\Cart;
use app\models\CartItem;
use app\services\CartService;

class CartController extends ActiveController
{
    public $modelClass = 'app\models\Cart';

    private CartService $cartService;

    public function __construct($id, $module, CartService $cartService, $config = [])
    {
        $this->cartService = $cartService;
        parent::__construct($id, $module, $config);
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);

        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
        ];
        return $actions;
    }

    /**
     * Fetch cart with cart_items
     *
     * @returns Response
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Response
    {
        $cart = $this->cartService->getCartWithItemsById($id);

        if (!$cart instanceof Cart) {
            throw new NotFoundHttpException("Cart you are trying to query is not found");
        }

        return $this->asJson([
            'data' => $cart,
        ]);
    }

    /**
     * Create an empty cart with possibility of creating first cart_item
     *
     * @returns Response
     * @throws ServerErrorHttpException
     * @throws TicketPulseException
     */
    public function actionCreate(): Response
    {
        try {
            $data = Yii::$app->request->bodyParams;

            $cart = $this->cartService->createCart();

            if (isset($data['event_id'])) {
                $cart = $this->cartService->addToCart($cart->id, $data['event_id'], $data['quantity'] ?? 1);
            }

            $this->cartService->populateCartItems($cart);
            return $this->asJson([
                'data' => $cart
            ]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
    }

    /**
     * Add an item to a cart (and create cart if it doesn't exist)
     *
     * @param int $id
     * @return Response
     * @throws BadRequestHttpException|ServerErrorHttpException|TicketPulseException
     */
    public function actionAddToCart(int $id): Response
    {
        $data = Yii::$app->request->bodyParams;
        if (!isset($data['event_id'])) {
            throw new BadRequestHttpException("Event ID is required");
        }

        try {
            $cart = $this->cartService->addToCart($id, $data['event_id'], $data['quantity'] ?? 1);
            $this->cartService->populateCartItems($cart);

            return $this->asJson([
                'data' => $cart
            ]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException("Cart item couldn't be added to cart");
        }
    }

    /**
     * Update an item in a cart (quantity)
     *
     * @param $id
     * @param $item_id
     * @return Response
     * @throws BadRequestHttpException|ServerErrorHttpException|Exception
     */
    public function actionUpdateCartItem($id, $item_id): Response
    {
        $body = Yii::$app->request->bodyParams;
        $quantity = $body['quantity'] ?? null;

        if (!is_numeric($quantity) || $quantity < 1) {
            throw new BadRequestHttpException("Quantity is required and must be a number greater than 0.");
        }

        $cart_item = $this->cartService->getCartItemById($item_id);
        if (!$cart_item instanceof CartItem) {
            throw new NotFoundHttpException("Cart item you are trying to query is not found");
        }

        try {
            $cart = $this->cartService->updateCartQuantityAndRecalculateTotals($cart_item, $quantity);
            $this->cartService->populateCartItems($cart);

            return $this->asJson([
                'data' => $cart
            ]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException("Cart item couldn't be updated");
        }
    }


    /**
     * @param int $id
     * @param int $item_id
     * @return Response
     * @throws TicketPulseException|ServerErrorHttpException
     */
    public function actionRemoveCartItem(int $id, int $item_id): Response
    {
        try {
            $this->cartService->validateCartExists($id);

            $cart = $this->cartService->removeCartItemAndRecalculateTotals($id, $item_id);
            $this->cartService->populateCartItems($cart);

            return $this->asJson(["data" => $cart]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException("Cart item couldn't be deleted");
        }
    }
}
