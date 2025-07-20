<template>
  <div class="chat">
    <div v-for="(msg, index) in messages" :key="index" class="message">
      <small>{{ new Date(msg.timestamp * 1000).toLocaleTimeString() }}</small> {{ msg.message }} 
    </div>
    <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Tape ton message" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const messages = ref([]);
const newMessage = ref('');
let eventSource = null;
const api_url = "https://localhost:8000";

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;

  try {
    await fetch(api_url + '/chat/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: newMessage.value }),
    });
    newMessage.value = '';
  } catch (error) {
    console.error('Erreur envoi message:', error);
  }
};

onMounted(() => {
  const url = new URL('http://localhost:3000/.well-known/mercure');
  url.searchParams.append('topic', 'chat/general');

  eventSource = new EventSource(url.toString());

  eventSource.onmessage = event => {
    const data = JSON.parse(event.data);
    messages.value.push(data);
  };

  eventSource.onerror = err => {
    console.error('Erreur Mercure:', err);
    eventSource.close();
  };
});

onBeforeUnmount(() => {
  if (eventSource) {
    eventSource.close();
  }
});
</script>

<style scoped>
.chat {
  min-height: 300px;
  max-height: 300px;
  overflow-y: scroll;

  display: flex;
  flex-direction: column;
  justify-content: flex-end;

  border: solid 1px white;
}

.message {
  margin-bottom: 8px;
  color: white;
}
</style>