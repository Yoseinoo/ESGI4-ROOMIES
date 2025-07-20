<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Rooms disponibles</h2>

    <div v-if="rooms.length === 0">
      <p>Pas de rooms disponibles</p>
    </div>

    <div v-else class="space-y-2">
        <div
            v-for="room in rooms"
            :key="room.id"
            class="border p-4 rounded hover:bg-gray-50 transition"
        >
            <div class="flex justify-between items-center">
            <div>
                <p class="text-lg font-semibold">🕹 {{ room.name }}</p>
                <p class="text-sm text-gray-600">Owner: {{ room.owner }}</p>
            </div>
            <RouterLink
                :to="`/room/${room.id}`"
                class="text-blue-600 underline hover:text-blue-800"
            >
                Rejoindre la room
            </RouterLink>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const rooms = ref([])

const fetchRooms = async () => {
  try {
    const response = await axios.get('https://localhost:8000/rooms');

    console.log(response.data);
    rooms.value = response.data
  } catch (error) {
    console.error('Failed to fetch rooms:', error)
  }
}

onMounted(() => {
  fetchRooms()
})
</script>
