<script setup lang="ts">
import Header from '@/components/Header.vue'
import { RouterView } from 'vue-router'
import LoadingOverlay from '@/components/shared/LoadingOverlay.vue'
</script>

<template>
  <div class="h-full p-1 bg-white">
    <Header />
    <div class="content-container my-1 border-solid border-2 border-black rounded">
      <div class="content content--adjusted-height m-6 bg-white">
        <LoadingOverlay />
        <RouterView />
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@use '@/assets/scss/style.scss' as *;
</style>
