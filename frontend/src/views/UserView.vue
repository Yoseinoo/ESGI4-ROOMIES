<template>
  <div class="p-6 max-w-xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">👤 My Profile</h2>

    <div class="mb-6">
      <p><strong>Email:</strong> {{ user?.email }}</p>
    </div>

    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-2">Create a Room</h3>
      <input
        v-model="newRoomName"
        placeholder="Room name"
        class="border p-2 mr-2"
      />
      <button
        @click="createRoom"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Create
      </button>
    </div>
    
    <h3 class="text-xl font-semibold mb-2">My Rooms</h3>
    <ul v-if="rooms.length" class="space-y-3 mb-6">
      <li
        v-for="room in rooms"
        :key="room.id"
        class="flex justify-between items-center border p-3 rounded"
      >
        <span>{{ room.name }}</span>
        <button
          @click="deleteRoom(room.id)"
          class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
        >
          Delete
        </button>
      </li>
    </ul>
    <p v-else>No rooms created yet.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const rooms = ref([])
const newRoomName = ref('')
const user = ref(null);

//Récupère les rooms de l'utilisateur
const fetchRooms = async () => {
    const res = await axios.post('https://localhost:8000/my-rooms', {
        email: user.value.email
    })

    console.log(res.data);
    rooms.value = res.data;
}

//Créer une room et l'ajoute dans la liste
const createRoom = async () => {
    if (!newRoomName.value.trim()) return
    const res = await axios.post('https://localhost:8000/create-room', {
        name: newRoomName.value,
        email: user.value.email
    })

    rooms.value.push(res.data)
    newRoomName.value = ''
}

//Supprimer une room et la supprime de la liste
const deleteRoom = async (roomId) => {
    await axios.delete(`https://localhost:8000/rooms/${roomId}`, {
        data: { email: user.value.email }
    })

    console.log('Room deleted: ', roomId);
    rooms.value = rooms.value.filter(room => room.id !== roomId)
}

onMounted(() => {
    //Récupère l'utilisateur connecté
    const store = useUserStore();
    store.fetchUser()
    .then(data => {
        user.value = store.user;

        //Quand l'utilisateur est récupéré, récupère ses rooms
        fetchRooms();
    });
})
</script>
