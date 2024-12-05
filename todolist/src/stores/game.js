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

    const createGame = async (boardSize) => {
        try {
            const response = await axios.post('/games', {
                custom: JSON.stringify({ board_size: boardSize }),
            });
            const newGame = response.data.game;
            currentGame.value = newGame; // Store the created game
            return newGame;
        } catch (error) {
            handleError(error, 'Failed to create the game.');
            return null;
        }
    };

    const updateGameStatus = async (gameId, status, details = {}) => {
        try {
            await axios.patch(`api/games/${gameId}`, {
                status,
                ...details,
            });
        } catch (error) {
            handleError(error, 'Failed to update the game status.');
        }
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
        updateGameStatus,
        fetchCurrentGame,
        createGame,
    };
});
