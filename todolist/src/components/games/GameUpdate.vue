<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import GameForm from './GameForm.vue';
import { useGameStore } from '@/stores/game';
import { useErrorStore } from '@/stores/error';

const storeGame = useGameStore();
const storeError = useErrorStore();

const router = useRouter();

const game = ref(null);

const props = defineProps({
    id: {
        type: Number,
        required: true
    }
});

// When the "id" prop changes, the "game" will be loaded from the API
watch(
    () => props.id,
    async (newIDValue) => {
        game.value = await storeGame.fetchGame(newIDValue);
    },
    { immediate: true }
);

const save = async (game) => {
    if (await storeGame.updateGame(game)) {        
        router.push({ name: 'games' });
    }
};

const cancel = () => {
    storeError.resetMessages();
    router.back();
};
</script>

<template>
    <GameForm v-if="game" :game="game" :title="`Update game # ${game.id}`" @save="save" @cancel="cancel"></GameForm>
</template>
