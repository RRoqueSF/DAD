<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import GameForm from './GameForm.vue';
import { useGameStore } from '@/stores/game';
import { useErrorStore } from '@/stores/error';

const storeGame = useGameStore();
const storeError = useErrorStore();

const router = useRouter();

const game = ref({
    id: 0,
    name: ''
});

const create = async (game) => {
    if (await storeGame.insertGame(game)) {        
        router.push({ name: 'games' });
    }
};

const cancel = () => {
    storeError.resetMessages();
    router.back();
};
</script>

<template>
    <GameForm :game="game" title="Create new game" @save="create" @cancel="cancel"></GameForm>
</template>
