<template>
  <div
    class="px-6 py-2 w-full inline-flex justify-between items-center border-solid border-2 border-black rounded"
  >
    <div class="inline-flex items-center gap-4">
      <RouterLink to="/">
        <div class="flex items-center gap-2">
          <TicketIcon class="size-12" />
          <span class="break-words">Events Pulse</span>
        </div>
      </RouterLink>
    </div>
    <div class="inline-flex items-end gap-4">
      <div>
        <RouterLink to="/">Main</RouterLink>
      </div>
      |
      <div>
        <RouterLink to="/cart">Cart</RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { TicketIcon } from '@heroicons/vue/24/solid'
import { RouterLink } from 'vue-router'
</script>

<style scoped></style>
