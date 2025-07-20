<template>
  <div class="tic-tac-toe">
    <h2>Room: {{ roomId }}</h2>

    <p v-if="winner && winner !== 'draw'">Winner: {{ winner }}</p>
    <p v-else-if="winner === 'draw'">Game is a draw!</p>
    <p v-else>Current Turn: {{ currentTurn }}</p>

    <table>
      <tbody>
        <tr v-for="(row, r) in board" :key="r">
          <td
            v-for="(cell, c) in row" 
            :key="c"
            class="cell"
            @click="play(r, c)"
          >
            {{ cell }}
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const roomId = route.params.id as string
const user = localStorage.getItem('user')

interface GameUpdate {
  board: string[][]
  currentTurn: string | null
  winner: string | null
}

const board = ref<string[][]>([
  ['', '', ''],
  ['', '', ''],
  ['', '', ''],
])
const currentTurn = ref<string | null>(null)
const winner = ref<string | null>(null)
const error = ref<string | null>(null)

let eventSource: EventSource | null = null

//Connexion à la room du jeu
const connectToMercure = () => {
  const url = new URL('http://localhost:3000/.well-known/mercure')
  url.searchParams.append('topic', `/rooms/${roomId}`)

  eventSource = new EventSource(url.toString());

  eventSource.onmessage = (event) => {
    const data: GameUpdate = JSON.parse(event.data)
    board.value = data.board
    currentTurn.value = data.currentTurn
    winner.value = data.winner
    console.log('new game state : ', data);
  }

  eventSource.onerror = () => {
    console.error('Mercure connection error')
  }
}

/**
 * Permet de signifier au back que la room a un utilisateur supplémentaire
 */
const joinRoom = async () => {
    const res = await axios.post(`https://localhost:8000/rooms/${roomId}/join`, {
        email: user
    });

    console.log(res.data);
}

/**
 * Permet de leave la room
 */
const leaveRoom = async () => {
    const res = await axios.post(`https://localhost:8000/rooms/${roomId}/leave`, {
        email: user
    });

    console.log(res.data);
}

async function play(row: number, col: number): Promise<void> {
  error.value = null

  if (winner.value) {
    error.value = 'Game is over.'
    return
  }

  if (currentTurn.value !== user) {
    error.value = 'Not your turn!'
    return
  }

  if (board.value[row][col] !== '') {
    error.value = 'Cell already taken.'
    return
  }

  try {
    const response = await fetch(`https://localhost:8000/rooms/${roomId}/play`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        row,
        col,
        user: user,
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      error.value = data.error || 'Failed to play move'
    }
  } catch {
    error.value = 'Network error'
  }
}

onMounted(() => {
    connectToMercure();
    joinRoom();
})

onUnmounted(() => {
    if (eventSource) {
        eventSource.close()
    }
    leaveRoom();
})
</script>

<style scoped>
table {
  border-collapse: collapse;
  margin-top: 1em;
}

td {
  width: 60px;
  height: 60px;
  text-align: center;
  vertical-align: middle;
  font-size: 2em;
  border: 1px solid #333;
  cursor: pointer;
  user-select: none;
}

.cell:hover {
  background-color: #f0f0f0;
}

.error {
  color: red;
  margin-top: 10px;
}
</style>
