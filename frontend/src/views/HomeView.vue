<template>
  <div id="app">
    <header class="app-header">
      <h1>🎮 Roomies - Chat Global</h1>
    </header>
    <button @click="handleLogout">Se déconnecter</button>
    <button @click="GoToProfile">Mon profil</button>
    <main>
      <Rooms />
      <Chat />
    </main>
  </div>
</template>

<script setup lang="ts">
import Chat from '../components/Chat.vue';
import Rooms from '@/components/Rooms.vue';
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'

const router = useRouter()
const store = useUserStore();

const handleLogout = async () => {
  await store.logout();
  if (!store.user) {
    router.push({ name: 'login' })
  }
}

const GoToProfile = () => {
  router.push({ name: 'profile' })
}
</script>

<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  margin: 0 auto;
  max-width: 800px;
  padding: 20px;

  display: flex;
  flex-direction: column;
  gap: 50px;
  align-items: center;
  justify-content: center;
}

.app-header {
  background-color: #35495e;
  color: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}
</style>