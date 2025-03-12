<template>
  <div class="h-full">
    <template v-if="noEventsAvailable">
      <div class="h-full flex justify-center items-center text-4xl">No events available</div>
    </template>
    <template v-else>
      <div class="h-full grid grid-cols-3 gap-4">
        <div v-for="(event, idx) in events" :key="idx">
          <EventCard :event="event" @add-to-cart="addToCart" />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import EventCard from '@/components/EventCard.vue'
import type { Event } from '@/models/event'

const props = defineProps<{
  events: Event[]
}>()

const emit = defineEmits(['addToCart'])

const addToCart = (id: string) => {
  emit('addToCart', id)
}

const noEventsAvailable = computed((): boolean => {
  return !props.events?.length
})
</script>

<style lang="scss" scoped></style>
