import { ref, computed, onMounted, watch } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';
import { useErrorStore } from '@/stores/error';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { useToast } from '@/components/ui/toast/use-toast';

export const useGameStore = defineStore('game', () => {
    const router = useRouter();
    const { toast } = useToast();
    const storeError = useErrorStore();
    const storeAuth = useAuthStore();

    const currentGame = ref(null);
    const games = ref([]);

    const setCurrentGame = (game) => {
        currentGame.value = game;
    };

    // Centralized error handler
    const handleError = (e, defaultMessage) => {
        const errorMessage = e.response?.data?.message || e.message || defaultMessage;
        storeError.setErrorMessages(
            errorMessage,
            e.response?.data?.errors || [],
            e.response?.status || 500,
            defaultMessage
        );
    };

    const fetchGameData = async (url, method = 'get', data = null) => {
        try {
            const response = await axios({ method, url, data });
            return response.data.data;
        } catch (e) {
            handleError(e, `Error fetching game data from ${url}`);
            return null;
        }
    };

    const fetchCurrentGame = async (gameId) => {
        currentGame.value = await fetchGameData(`games/${gameId}`);
    };

    const fetchGames = async () => {
        const userId = storeAuth.user?.id;
        if (!userId) return;
        games.value = await fetchGameData(`games/${userId}`);
    };

    const insertGame = async (game) => {
        const newGame = await fetchGameData('games', 'post', game);
        if (newGame) {
            games.value.push(newGame);
            toast({ description: `Game #${newGame.id} of type "${newGame.type}" was created!` });
            return newGame;
        }
        return false;
    };

    const updateGame = async (game) => {
        const index = games.value.findIndex((g) => g.id === game.id);
        if (index === -1) {
            storeError.setErrorMessages('Game not found!', [], 404, 'Error updating game!');
            return false;
        }
        const updatedGame = await fetchGameData(`games/${game.id}`, 'put', game);
        if (updatedGame) {
            games.value[index] = updatedGame;
            toast({ description: 'Game has been updated correctly!' });
            return updatedGame;
        }
        return false;
    };

    const totalGames = computed(() => games.value.length);

    const listGames = (includeNull = false, includeNoGameOption = false) => {
        let list = games.value.map((g) => ({
            id: g.id,
            filterDescription: g.type,
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
        currentGame,
        totalGames,
        listGamesIncludingNull,
        listGamesToFilter,
        fetchGames,
        fetchCurrentGame,
        insertGame,
        updateGame,
    };
});
