<script setup>
import { computed, onMounted } from 'vue';
import { useBoardStore } from '@/stores/board';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const boardStore = useBoardStore();
const authStore = useAuthStore();
const router = useRouter();

const userCoins = computed(() => authStore.userBrainCoinsBalance || 0);

const getBoardCost = (board) => {
  if (board.board_rows === 3 && board.board_cols === 4) return 0; // 3x4 board is free
  return 1; // All other boards cost 1 brain coin
};

const canPlay = (board) => {
  return getBoardCost(board) === 0 || userCoins.value >= getBoardCost(board);
};

const selectBoard = async (board) => {
  const cost = getBoardCost(board);
  if (cost > 0) {
    authStore.user.brainCoins -= cost; // Deduct brain coins for paid boards
  }

  await router.push({
    name: 'createGame',
    query: { rows: board.board_rows, cols: board.board_cols },
  });
};

onMounted(() => {
  boardStore.fetchBoards();
});
</script>

<template>
    <div class="select-single-game">
      <h2 class="title">Select Your Board Size</h2>
      <div class="boards">
        <button
          v-for="board in boardStore.boards"
          :key="board.id"
          :disabled="!canPlay(board)"
          @click="selectBoard(board)"
          class="board-button"
          :class="{ disabled: !canPlay(board) }"
        >
          <span class="board-size">{{ board.board_rows }}x{{ board.board_cols }} Board</span>
          <span v-if="getBoardCost(board) > 0" class="board-cost">
            - Costs {{ getBoardCost(board) }} brain coin<span v-if="getBoardCost(board) > 1">s</span>
          </span>
        </button>
      </div>
      <p v-if="userCoins === 0" class="no-coins-warning">
        You have no brain coins. You can only play on the smallest board.
      </p>
    </div>
  </template>

    <style>
.select-single-game {
    text-align: center;
    padding: 20px;
    max-width: 600px;
    margin: 0 auto;
    font-family: 'Arial', sans-serif;
    color: #333;
  }
  
  .title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #4caf50;
  }
  
  .boards {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
  }
  
  .board-button {
    padding: 15px 20px;
    border: none;
    border-radius: 8px;
    background-color: #4caf50;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    text-align: center;
    min-width: 120px;
  }
  
  .board-button:hover {
    background-color: #45a049;
    transform: scale(1.05);
  }
  
  .board-button.disabled {
    background-color: #ccc;
    color: #888;
    cursor: not-allowed;
  }
  
  .board-size {
    display: block;
    font-size: 20px;
    font-weight: bold;
  }
  
  .board-cost {
    font-size: 14px;
    color: #ffd700;
    margin-top: 5px;
    display: block;
  }
  
  .no-coins-warning {
    margin-top: 20px;
    font-size: 16px;
    color: #f44336;
    font-weight: bold;
  }
</style>