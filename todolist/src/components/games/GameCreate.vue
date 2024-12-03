<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useGameStore } from '@/stores/game';
import { useErrorStore } from '@/stores/error';
import axios from 'axios';

// Access the auth store, game store, and error store
const authStore = useAuthStore();
const gameStore = useGameStore();
const storeError = useErrorStore();

// Game settings
const boardSize = ref('3x4'); // Default board size

const startGame = async () => {
  try {
    const gameData = {
      type: 'S', // Single-player game type
      created_user_id: authStore.user.id, // The logged-in user's ID
      status: 'PE', // Pending status
      board_size: boardSize.value, // Pass board_size as a top-level field
      custom: JSON.stringify({
        board_size: boardSize.value,
      }),
    };

    console.log('Game data being sent:', gameData);

    const response = await axios.post('/games', gameData);

    console.log('Response data:', response.data);

    if (response.data && response.data.game) {
      gameStore.setCurrentGame(response.data.game); // Update currentGame using the store method
      alert('Game started successfully!');
    } else {
      throw new Error('Game not returned in the response!');
    }
  } catch (error) {
    console.error('Error starting the game:', error);
    const errorMessage =
      error.response?.data?.message || error.message || 'An unexpected error occurred!';
    const fieldErrors = error.response?.data?.errors || [];
    const status = error.response?.status || 500;
    storeError.setErrorMessages(errorMessage, fieldErrors, status, 'Error Starting Game');
  }
};





// Access user's brain coins from the auth store
const brainCoins = computed(() => authStore.userBrainCoinsBalance);

</script>

<template>
  <div v-if="gameStore.currentGame">
  <p>Game ID: {{ gameStore.currentGame.id }}</p>
  <p>Board Size: {{ gameStore.currentGame.board_size }}</p>
  <p>Game Started: {{ gameStore.currentGame.began_at }}</p>
</div>

    <div v-else>
      <select v-model="boardSize">
        <option value="3x4">3x4 (Free)</option>
        <option value="4x4" :disabled="brainCoins < 1">4x4 (1 Brain Coin)</option>
        <option value="6x6" :disabled="brainCoins < 1">6x6 (1 Brain Coin)</option>
      </select>
      <button @click="startGame">Start Game</button>

      <p v-if="brainCoins < 1">You do not have enough brain coins for larger boards.</p>
    </div>
</template>
