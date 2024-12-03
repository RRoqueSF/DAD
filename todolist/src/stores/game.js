import { ref, computed, onMounted, watch } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';
import { useErrorStore } from '@/stores/error';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { useToast } from '@/components/ui/toast/use-toast';
import { ToastAction } from '@/components/ui/toast';
import { h } from 'vue';

export const useGameStore = defineStore('game', () => {
    const router = useRouter();
    const { toast } = useToast();
    const storeError = useErrorStore();
    const currentGame = ref(null); // Will hold the current game object


    const games = ref([]);
    const setCurrentGame = (game) => {
        currentGame.value = game;
    };

    const fetchCurrentGame = async (gameId) => {
        try {
            const response = await axios.get('games/' + gameId);
            currentGame.value = response.data.data; // Set the fetched game as the current game
            return response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response?.data?.message || 'Error fetching the game!',
                e.response?.data?.errors || [],
                e.response?.status || 500,
                'Error fetching the game!'
            );
        }
    };
    


    const totalGames = computed(() => {
        return games.value ? games.value.length : 0;
    });

    const listGames = (includeNull = false, includeNoGameOption = false) => {
        let list = games.value.map((g) => ({
            id: g.id,
            filterDescription: g.type, // Adjust the property as needed
        }));

        if (includeNoGameOption) {
            list.unshift({ id: -1, filterDescription: '-- No game --' });
        }
        if (includeNull) {
            list.unshift(null);
        }

        return list;
    };

    const listGamesIncludingNull = computed(() => listGames(true));
    const listGamesToFilter = computed(() => listGames(false, true));

    const storeAuth = useAuthStore();
    const fetchGames = async () => {
        try {
            const userId = storeAuth.user?.id;

            const response = await axios.get('games/' + userId);

            games.value = response.data.data;
        } catch (e) {
            const errorMessage =
                e.response?.data?.message || e.message || 'Error fetching user games!';
            storeError.setErrorMessages(
                errorMessage,
                [],
                e.response?.status || 500,
                'Error fetching games!'
            );
        }
    };

    const getIndexOfGame = (gameId) => {
        return games.value.findIndex((g) => g.id === gameId);
    };

    const fetchGame = async (gameId) => {
        storeError.resetMessages();
        const response = await axios.get('games/' + gameId);
        const index = getIndexOfGame(gameId);
        if (index > -1) {
            games.value[index] = Object.assign({}, response.data.data);
        }
        return response.data.data;
    };

    const showToast = (description, action = null) => {
        toast({ description, action });
    };

    const insertGame = async (game) => {
        storeError.resetMessages();
        try {
            const response = await axios.post('games', game);
            games.value.push(response.data.data);
            showToast(`Game #${response.data.data.id} of type "${response.data.data.type}" was created!`);
            return response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Error creating game!'
            );
            return false;
        }
    };

    const updateGame = async (game) => {
        storeError.resetMessages();
        const index = getIndexOfGame(game.id);
        if (index === -1) {
            storeError.setErrorMessages('Game not found!', [], 404, 'Error updating game!');
            return false;
        }
        try {
            const response = await axios.put('games/' + game.id, game);
            games.value[index] = Object.assign({}, response.data.data);
            toast({ description: 'Game has been updated correctly!' });
            return response.data.data;
        } catch (e) {
            storeError.setErrorMessages(
                e.response.data.message,
                e.response.data.errors,
                e.response.status,
                'Error updating game!'
            );
            return false;
        }
    };


    onMounted(() => {
        watch(
            () => storeAuth.user,
            (newUser) => {
                if (newUser) {
                    fetchGames();
                }
            },
            { immediate: true }
        );
    });

    return {
        games,
        totalGames,
        listGamesIncludingNull,
        listGamesToFilter,
        fetchGames,
        fetchGame,
        insertGame,
        updateGame,
    };
});
