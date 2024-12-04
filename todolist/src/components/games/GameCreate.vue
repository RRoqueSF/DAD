<script setup>
import { ref, computed, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { initializeGame, flipTile, stopTimer, startTimer, timer } from '@/stores/gameUtils.js';

const route = useRoute();
const router = useRouter();

// Accessing query parameters
const cols = parseInt(route.query.cols);
const rows = parseInt(route.query.rows);

const tiles = ref([]);
const totalTiles = computed(() => cols * rows);
console.log(totalTiles.value);

const firstTile = ref(null);
const secondTile = ref(null);
const isProcessing = ref(false);
const gameWon = ref(false);
const showPopup = ref(false);
const isGameStarted = ref(false);

// Initialize the game and assign tiles
tiles.value = initializeGame(totalTiles.value);

const gridColumns = computed(() => Math.max(cols, rows));
const gridRows = computed(() => Math.min(cols, rows));

const handleFlipTile = (tile) => {
  if (isProcessing.value || gameWon.value) return;

  if (!isGameStarted.value) {
    isGameStarted.value = true; 
    startTimer(); // Start the timer
  }

  flipTile(
    tile,
    firstTile,
    secondTile,
    (processing) => (isProcessing.value = processing),
    () => {
      if (tiles.value.every((t) => t.isMatched)) {
        gameWon.value = true;
        stopTimer();

        setTimeout(() => {
          showPopup.value = true;
        }, 500);
      }
      isProcessing.value = false;
    },
    () => (isProcessing.value = false)
  );
};

const resetGame = () => {
  tiles.value = initializeGame(totalTiles.value);
  gameWon.value = false;
  showPopup.value = false;
  isGameStarted.value = false; 
  stopTimer();
  timer.value = 0;
};

onBeforeUnmount(() => {
  resetGame();
});

router.beforeEach((to, from) => {
  if (from.name === 'selectSingleGame') {
    resetGame();
  }
});

</script>


<template>
  <div class="container mx-auto p-6">
    <!-- Title Section -->
    <div
      :class="[
        'title-container text-center transition-all duration-700',
        { 'slide-up-out': isGameStarted }
      ]"
    >
      <h1 class="text-3xl font-bold mb-4">Memory Game</h1>
      <p class="text-gray-600 mb-6">Flip one tile to start the game!</p>
    </div>

    <!-- Game Section -->
    <div
      :class="[
        'game-container mx-auto transition-all duration-700',
        { 'move-up': isGameStarted }
      ]"
    >
      <!-- Timer Display -->
      <div class="text-center mb-4">
        <p class="text-lg font-semibold">Time: {{ timer }} seconds</p>
      </div>

      <!-- Game Board -->
      <div
        class="game-board mx-auto grid gap-4"
        :style="{ 
          gridTemplateColumns: `repeat(${gridColumns}, 1fr)`, 
          gridTemplateRows: `repeat(${gridRows}, 1fr)`
        }"
      >
        <!-- Game Tiles -->
        <div
          v-for="tile in tiles"
          :key="tile.id"
          class="tile"
          @click="handleFlipTile(tile)"
        >
          <div class="tile-inner" :class="{ 'is-flipped': tile.isFlipped }">
            <!-- Tile Front (Revealed Face) -->
            <div class="tile-front">
              <img v-if="tile.isFlipped" :src="tile.face" alt="Tile face" />
            </div>
            <!-- Tile Back (Default Unturned Face) -->
            <div class="tile-back">
              <img v-if="!tile.isFlipped" src="@/assets/cards/semFace.png" alt="Default face" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Victory Popup -->
    <div v-if="showPopup" class="popup fade-in">
      <div class="popup-content">
        <h2 class="text-3xl font-bold mb-4">Congratulations!</h2>
        <p class="text-xl">You completed the game in {{ timer }} seconds.</p>
        <button 
          @click="resetGame" 
          class="px-6 py-3 bg-blue-500 text-white rounded hover:bg-blue-600 mt-4"
        >
          Play Again
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 1000px; /* Restricting overall container width */
  margin: 0 auto;
  padding: 2rem;
}

.game-board {
  max-width: 600px; /* Limits the game board size */
  display: grid;
  gap: 1rem; /* Adjusted spacing between tiles */
  margin: 1rem auto;
}

.tile {
  perspective: 800px;
  width: 80px; /* Fixed size for tiles */
  height: 80px; /* Ensure they are square */
  margin-bottom: 30px;
  cursor: pointer;
}

.tile-inner {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform 0.5s;
  border-radius: 10px;
}

.tile-inner.is-flipped {
  transform: rotateY(180deg);
}

.tile-front,
.tile-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  border-radius: 10px;
}

.tile-front {
  transform: rotateY(180deg);
  background-color: #e0e0e0;
  border: 1px solid #ccc;
}

.tile-back {
  background-color: #ffffff;
  border: 1px solid #444;
}

.popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255, 255, 255, 0.95);
  padding: 2rem;
  max-width: 300px; /* Shrink size for better focus */
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.popup-content {
  text-align: center;
}

.title-container {
  margin-bottom: 2rem;
}

.game-container {
  margin-top: 2rem;
}

@media (max-width: 768px) {
  .game-board {
    max-width: 100%; /* Ensures board scales to smaller screens */
    gap: 0.5rem;
  }

  .tile {
    width: 60px; /* Smaller tiles for narrow screens */
    height: 60px;
  }
}
</style>
