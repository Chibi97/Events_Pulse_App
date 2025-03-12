<template>
  <div
    class="p-3 text-base flex flex-col gap-3 items-center justify-between min-h-48 border-black rounded border-2"
  >
    <template v-if="event.image_url">
      <div
        class="w-full min-h-32 h-32 border-black border-2 bg-cover bg-center bg-no-repeat"
        :style="{ backgroundImage: `url(${baseImageUrl}/${event.image_url})` }"
      ></div>
    </template>
    <template v-else>
      <div class="w-full min-h-32 h-32 border-black border-2 bg-slate-200"></div>
    </template>
    <div class="w-full inline-flex gap-2 justify-between">
      <div class="flex flex-col gap-1 justify-between">
        <div>{{ event.name }}</div>
        <div class="text-xs">Price: {{ formatPrice(event.current_price) }}</div>
        <div class="text-xs">Date: {{ formatDate(event.date) }}</div>
      </div>
      <div class="flex flex-col gap-1 justify-between">
        <button
          class="px-3 py-0.5 text-sm shadow-md shadow-black ml-auto border-2 border-black"
          @click.prevent="addToCart"
        >
          Add to cart
        </button>
        <div class="text-xs text-right">{{ eventTypeMessage }}</div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { Event } from '@/models/event'
import { formatDate } from '@/utils/date'
import { formatPrice } from '@/utils/events'
import { computed } from 'vue'

const props = defineProps<{
  event: Event
}>()

const emit = defineEmits(['addToCart'])

const addToCart = () => {
  emit('addToCart', props.event.id)
}

const baseImageUrl = import.meta.env.VITE_BASE_IMAGE_URL
const eventTypeMessage = computed(() => `Only ${props.event.event_type} Ticket Available`)
</script>

<style lang="scss" scoped></style>
