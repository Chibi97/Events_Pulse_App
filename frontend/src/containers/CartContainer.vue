<template>
  <CartView
    v-if="cart !== null"
    :cartItems="cartItems"
    :calculations="cartCalculations"
    @updateItemQuantity="cartStore.updateItemQuantity"
    @removeItem="cartStore.removeCartItem"
  />
  <Loader v-else />
</template>

<script lang="ts" setup>
import Loader from '@/components/shared/Loader.vue'
import { useCartStore } from '@/store/cart'
import CartView from '@/views/CartView.vue'
import { storeToRefs } from 'pinia'
import { onMounted } from 'vue'

const cartStore = useCartStore()
const { cart, cartItems, cartCalculations } = storeToRefs(cartStore)

onMounted(async () => {
  if (!cart.value) {
    await cartStore.getCart()
  }
})
</script>

<style lang="scss" scoped></style>
