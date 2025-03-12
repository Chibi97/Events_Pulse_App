<template>
  <div class="h-full bg-white">
    <template v-if="isCartEmpty">
      <div class="h-full flex justify-center items-center text-4xl">Your cart is empty</div>
    </template>
    <template v-else>
      <div class="sticky top-0 bg-white z-10 py-6">Your cart: {{ cartItemCount }} items</div>
      <div class="grid grid-cols-10 h-5/6 gap-6 divide-x-2 divide-black">
        <div class="flex-auto col-span-6 max-h-full overflow-auto">
          <div
            v-for="(item, idx) in cartItems"
            :key="idx"
            class="flex flex-col gap-2 border-b-2 border-b-black last:border-b-0"
          >
            <CartItem
              :cart-item="item"
              @update-quantity="updateItemQuantity"
              @remove-item="removeItem"
            />
          </div>
        </div>
        <div class="flex-none col-span-4 px-3">
          <CartCheckout
            :subtotal="calculations.subtotal"
            :total="calculations.total"
            :deliveryPrice="calculations.delivery_price"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import type { CartCalculations, CartItem as CartItemType } from '@/models/cart'
import CartItem from '@/components/CartItem.vue'
import CartCheckout from '@/components/CartCheckout.vue'

const props = defineProps<{
  cartItems: CartItemType[]
  calculations: CartCalculations
}>()

const emit = defineEmits(['updateItemQuantity', 'removeItem'])

const isCartEmpty = computed((): boolean => {
  return !(props?.cartItems?.length > 0)
})

const cartItemCount = computed((): number => {
  return props?.cartItems?.length || 0
})

const updateItemQuantity = (payload: { id: number; quantity: number }) => {
  emit('updateItemQuantity', payload)
}

const removeItem = (id: number) => {
  emit('removeItem', id)
}
</script>

<style lang="scss" scoped></style>
