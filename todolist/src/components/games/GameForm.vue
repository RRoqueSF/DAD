<script setup>
import { useErrorStore } from '@/stores/error';

const storeError = useErrorStore();

const props = defineProps({
    game: {
        type: Object,
        required: true
    },
    title: {
        type: String,
        default: 'Game'
    }
});

const emit = defineEmits(['save', 'cancel']);

const clickSave = (game) => {
    emit('save', game);
};

const clickCancel = () => {
    emit('cancel');
};
</script>

<template>
    <div class="py-4 px-2">
        <div class="flex flex-col space-y-3 px-3">
            <h2 class="text-2xl">
                {{ title }}
            </h2>

            <div class="flex flex-col">
                <div class="flex space-x-1 align-middle">
                    <label for="input_name_id" class="w-24 font-medium text-sm leading-10">Name</label>
                    <input type="text" id="input_name_id" class="px-4 grow h-10 border-gray-300 border rounded-lg text-base"
                            v-model="game.name">     
                </div>                
                <ErrorMessage class="ps-[6.5rem]" :errorMessage="storeError.fieldMessage('name')"></ErrorMessage>
            </div>
            <div class="pt-4 flex space-x-4 justify-end">
                <button type="button" class="w-24 h-10 text-sm font-bold rounded-md 
                                            border border-transparent bg-gray-400 text-white 
                                            hover:bg-gray-500 focus:outline-none focus:bg-gray-500"
                        @click.prevent="clickCancel">
                    Cancel
                </button>                
                <button type="button" class="w-24 h-10 text-sm font-bold rounded-md 
                                            border border-transparent bg-green-700 text-white 
                                            hover:bg-green-800 focus:outline-none focus:bg-green-800"
                        @click.prevent="clickSave(game)">
                    Save
                </button>  
            </div>
        </div>
    </div>
</template>
