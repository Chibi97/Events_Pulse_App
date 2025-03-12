import type { Event } from '@/models/event'

export interface Cart {
  readonly id: number
  readonly subtotal: string
  readonly delivery_price: string
  readonly total: string
  readonly cartItems: CartItem[]
}

export interface CartItem {
  readonly id: number
  readonly event_id: number
  readonly cart_id: number
  readonly quantity: number
  readonly price: string
  readonly event: Event
}

export interface CartCalculations {
  readonly total: string
  readonly subtotal: string
  readonly delivery_price: string
}
