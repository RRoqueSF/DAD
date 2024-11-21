<script setup>
import Game from './Game.vue';

const props = defineProps({
    games: Array,
    readonly: Boolean
});
console.log(props.games);
</script>

<template>
    <div class="divide-y divide-solid divide-gray-300 border border-gray-300">
        <Game v-for="game in games" :key="game" 
                :game="game" 
                :readonly="readonly"></Game>
    </div>            
</template>
