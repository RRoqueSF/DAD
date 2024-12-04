<script setup>
import { onMounted } from 'vue';
import { useGameStore } from '@/stores/game';
import { useErrorStore } from '@/stores/error';
import { useAuthStore } from '@/stores/auth';
import GameList from './GameList.vue';

const storeAuth = useAuthStore();
const storeGame = useGameStore();
const storeError = useErrorStore();



onMounted(() => {
    storeError.resetMessages();
});
</script>

<template>
    <div class="pt-4">
        <RouterLink :to="{ name: 'selectSingleGame' }" class="mt-4 w-36 h-10 flex items-center justify-center text-sm font-bold rounded-md 
                                    border border-transparent bg-blue-600 text-white 
                                    hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
            New Game
        </RouterLink>        
        <h2 class="pt-8 pb-2 text-2xl">
            Games
            <span class="text-base">(Total = {{ storeGame.totalGames }})</span> 
        </h2>
        <div v-show="storeGame.totalGames > 0">
            <GameList :games="storeGame.games"></GameList>
        </div>
    </div>
</template>
