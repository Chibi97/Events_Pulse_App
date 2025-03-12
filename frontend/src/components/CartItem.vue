<template>
  <div class="inline-flex gap-7 py-1">
    <div class="flex flex-col items-center justify-center">
      <template v-if="cartItem.event.image_url">
        <div
          class="bg-cover bg-center bg-no-repeat border-black border-2 w-28 h-28"
          :style="{ backgroundImage: `url(${baseImageUrl}/${cartItem.event.image_url})` }"
        ></div>
      </template>
      <template v-else>
        <div class="px-3">
          <PhotoIcon class="size-12" />
        </div>
      </template>
    </div>
    <div class="flex-auto">
      <div>{{ cartItem.event.name }}</div>
      <div>{{ formatDate(cartItem.event.date) }}</div>
      <div>{{ cartItem.event.event_type }} Ticket</div>
    </div>
    <div class="inline-flex items-center">
      <label>Quantity: </label>
      <VueSelect
        v-model="itemQuantity"
        :options="quantityValues"
        :is-clearable="false"
        @option-selected="updateQuantity($event)"
      />
    </div>
    <div>
      <div>${{ cartItem.price }}</div>
      <button @click.prevent="removeItem" class="underline text-blue-400 font-bold">Remove</button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { PhotoIcon } from '@heroicons/vue/24/solid'
import type { CartItem } from '@/models/cart'
import VueSelect from 'vue3-select-component'
import { formatDate } from '../utils/date'

const props = defineProps<{
  cartItem: CartItem
}>()

const emit = defineEmits(['updateQuantity', 'removeItem'])

const itemQuantity = ref(1)
const baseImageUrl = import.meta.env.VITE_BASE_IMAGE_URL

const quantityValues = Array.from(Array(10).keys()).map((numLabel: number) => {
  return {
    label: `${numLabel + 1}`,
    value: numLabel + 1,
  }
})

watch(
  () => props.cartItem.quantity,
  (newValue) => {
    const foundQuantity = quantityValues.find((quantity) => quantity.value === newValue)
    itemQuantity.value = foundQuantity?.value || 1
  },
  { immediate: true },
)

const updateQuantity = (dropdownOption: { label: string; value: number }) => {
  emit('updateQuantity', {
    id: props.cartItem.id,
    quantity: dropdownOption.value,
  })
}

const removeItem = () => {
  emit('removeItem', props.cartItem.id)
}
</script>

<style lang="scss" scoped></style>
