<template>
  <MainView v-if="events.length" :events="events" @addToCart="cartStore.addToCart" />
  <Loader v-else />
</template>

<script lang="ts" setup>
import { onMounted } from 'vue'
import Loader from '@/components/shared/Loader.vue'
import MainView from '@/views/MainView.vue'
import { useEventStore } from '@/store/event'
import { useCartStore } from '@/store/cart'
import { storeToRefs } from 'pinia'

const cartStore = useCartStore()
const eventStore = useEventStore()

const { events } = storeToRefs(eventStore)

onMounted(async () => {
  if (!events.value.length) {
    await eventStore.getEvents()
  }
})
</script>

<style lang="scss" scoped></style>
