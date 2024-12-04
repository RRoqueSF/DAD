import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';
import { useErrorStore } from '@/stores/error';

export const useBoardStore = defineStore('board', () => {
  const boards = ref([]);
  const errorStore = useErrorStore();

  // Fetch boards from the backend API
  const fetchBoards = async () => {
    try {
      const response = await axios.get('/boards'); // Adjust this endpoint to match your API
      boards.value = response.data.data; // Assuming the boards are in `response.data.data`
      console.log('Boards:', boards.value);
    } catch (error) {
      errorStore.setErrorMessages(
        error.response?.data?.message || 'Error fetching boards!',
        error.response?.data?.errors || [],
        error.response?.status || 500,
        'Error fetching boards!'
      );
    }
  };

  return {
    boards,
    fetchBoards,
  };
});
