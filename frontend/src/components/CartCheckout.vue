<template>
  <div class="flex flex-col gap-6">
    <div class="inline-flex justify-between items-center">
      <div>Subtotal</div>
      <div>${{ subtotal }}</div>
    </div>
    <div class="inline-flex justify-between items-center font-bold">
      <div>
        With delivery
        <span v-if="isFreeDelivery">(free)</span>
        <span v-else>(${{ deliveryPrice }})</span>
      </div>
      <div>${{ total }}</div>
    </div>
    <button class="w-full border-black bg-blue-400 mx-auto py-1">Check out</button>
    <div class="font-bold" v-if="isFreeDelivery">Congrats - you get free delivery!</div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = defineProps<{
  subtotal: string
  total: string
  deliveryPrice: string
}>()

const isFreeDelivery = computed(() => parseFloat(props.deliveryPrice) === 0)
</script>

<style lang="scss" scoped></style>
