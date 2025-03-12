import type { Cart } from '@/models/cart'
import type { Event } from '@/models/event'
import axiosInstance from './axios'

const ENDPOINTS = {
  events: '/events',
  cart: '/carts',
}

const getEvents = async (pageNumber = 1, pageSize = 10): Promise<Event[]> => {
  try {
    const response = await axiosInstance.get(
      ENDPOINTS.events + `?pageSize=${pageSize}&page=${pageNumber}`,
    )

    return response.data.data
  } catch (err) {
    console.log('🚀 ~ getEvents ~ err:', err)
    return []
  }
}

const initCart = async (eventId?: number): Promise<Cart | null> => {
  try {
    const response = await axiosInstance.post(
      ENDPOINTS.cart,
      eventId && {
        event_id: eventId,
      },
    )
    return response.data.data
  } catch (err) {
    console.log('🚀 ~ initCart ~ err:', err)
    return null
  }
}

const getCart = async (cartId: number): Promise<Cart | null> => {
  try {
    const response = await axiosInstance.get(ENDPOINTS.cart + `/${cartId}`)
    return response.data.data
  } catch (err) {
    console.log('🚀 ~ getCart ~ err:', err)
    return null
  }
}

const addItemToCart = async (cartId: number, eventId: number): Promise<Cart | null> => {
  try {
    const response = await axiosInstance.post(ENDPOINTS.cart + `/${cartId}/items`, {
      event_id: eventId,
    })
    return response.data.data
  } catch (err) {
    console.log('🚀 ~ addItemToCart:', err)
    return null
  }
}

const removeCartItem = async (cartId: number, itemId: number): Promise<Cart | null> => {
  try {
    const response = await axiosInstance.delete(ENDPOINTS.cart + `/${cartId}/items/${itemId}`)
    return response.data.data
  } catch (err) {
    console.log('🚀 ~ removeCartItem ~ err:', err)
    return null
  }
}

const updateItemQuantity = async (
  cartId: number,
  itemId: number,
  quantity: number,
): Promise<Cart | null> => {
  try {
    const response = await axiosInstance.put(ENDPOINTS.cart + `/${cartId}/items/${itemId}`, {
      quantity,
    })
    return response.data.data
  } catch (err) {
    console.log('🚀 ~ updateItemQuantity ~ err:', err)
    return null
  }
}

export default {
  getEvents,
  initCart,
  getCart,
  addItemToCart,
  removeCartItem,
  updateItemQuantity,
}
