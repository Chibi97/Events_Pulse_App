import { defineStore } from 'pinia'
import http from '@/services/http'
import { ref, type Ref } from 'vue'
import type { Event } from '@/models/event'

export const useEventStore = defineStore('event', () => {
  const events: Ref<Event[]> = ref([])

  const getEvents = async (pageNumber = 1, pageSize = 30) => {
    const eventResponse = await http.getEvents(pageNumber, pageSize)
    if (eventResponse.length) {
      events.value = eventResponse
    }
  }

  return {
    events,
    getEvents,
  }
})
