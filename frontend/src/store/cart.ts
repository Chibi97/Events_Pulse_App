import { defineStore } from 'pinia'
import { computed, ref, type Ref } from 'vue'
import type { Cart, CartCalculations, CartItem } from '@/models/cart'
import http from '@/services/http'
import { useToast } from 'vue-toast-notification'
import { useLoading } from '../composables/useLoading'

export const useCartStore = defineStore('cart', () => {
  const cart: Ref<Cart | null> = ref(null)
  const $toast = useToast()
  const { showLoading, hideLoading } = useLoading()

  const cartCalculations = computed((): CartCalculations => {
    return {
      total: cart?.value?.total || '0.00',
      subtotal: cart?.value?.subtotal || '0.00',
      delivery_price: cart?.value?.delivery_price || '0.00',
    }
  })

  const cartItems = computed((): CartItem[] => {
    return cart?.value?.cartItems || []
  })

  const getCart = async () => {
    const cartId = localStorage.getItem('cartId')

    if (cartId) {
      const fetchedCart = await http.getCart(+cartId)

      if (fetchedCart) {
        cart.value = fetchedCart
      } else {
        console.error('Could not fetch cart')
        localStorage.removeItem('cartId')
        await initCart()
      }
    } else {
      await initCart()
    }
  }

  const initCart = async (eventId?: number) => {
    const newCart = await http.initCart(eventId)
    if (newCart?.id) {
      showLoading()
      localStorage.setItem('cartId', `${newCart.id}`)
      cart.value = newCart
      eventId && $toast.success('Ticket added to cart')
    } else {
      eventId && $toast.error('Could not add ticket to cart. Try again later.')
    }

    hideLoading()
  }

  const addToCart = async (eventId: number) => {
    const cartId = localStorage.getItem('cartId')
    if (!cartId) {
      await initCart(eventId)
      return
    }

    showLoading()
    const cartExists = await http.getCart(+cartId)

    if (cartExists) {
      const updatedCart = await http.addItemToCart(+cartId, eventId)
      if (updatedCart) {
        cart.value = updatedCart
        $toast.success('Ticket added to cart')
      } else {
        $toast.error('Could not add ticket to cart. Try again later.')
      }
    } else {
      await initCart(eventId)
    }

    hideLoading()
  }

  const removeCartItem = async (itemId: number) => {
    const cartId = localStorage.getItem('cartId')
    if (cartId) {
      showLoading()
      const updatedCart = await http.removeCartItem(+cartId, itemId)
      if (updatedCart) {
        cart.value = updatedCart
        $toast.success('Ticket removed from cart')
      }
      hideLoading()
      return
    }
    $toast.error('Could not remove ticket from cart. Try again later.')
  }

  const updateItemQuantity = async (payload: { id: number; quantity: number }) => {
    const cartId = localStorage.getItem('cartId')
    if (cartId) {
      showLoading()
      const updatedCart = await http.updateItemQuantity(+cartId, payload.id, payload.quantity)
      if (updatedCart) {
        cart.value = updatedCart
        $toast.success('Ticket updated')
      }
      hideLoading()
      return
    }
    $toast.error('Could not update ticket. Try again later.')
  }

  return {
    cart,
    cartItems,
    cartCalculations,
    getCart,
    addToCart,
    removeCartItem,
    updateItemQuantity,
  }
})
