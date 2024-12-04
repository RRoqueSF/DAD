<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useErrorStore } from '@/stores/error'; // Import error store
import axios from 'axios';
import { useRouter } from 'vue-router';

const storeAuth = useAuthStore();
const errorStore = useErrorStore(); // Initialize error store
const router = useRouter();

const showDeleteModal = ref(false);
const confirmationInput = ref('');

const cancelDelete = () => {
  showDeleteModal.value = false;
  confirmationInput.value = '';
};

const confirmDelete = async () => {
  try {
    const response = await axios.delete(`/users/${storeAuth.user.id}`, {
      data: {
        confirmation: confirmationInput.value,
      },
    });

    // Handle successful deletion (perhaps logout the user, show success toast, etc.)
    storeAuth.logout();
    showDeleteModal.value = false;
    confirmationInput.value = '';
    
    // Redirect to the login page after successful deletion
    router.push({ name: 'login' }); 
    alert('Your account has been deleted successfully.');
  } catch (error) {
    // Handle error with the error store
    const statusCode = error.response?.status || 500;
    const mainMessage = error.response?.data?.message || 'An unknown error occurred';
    const fieldMessages = error.response?.data?.errors || [];

    // Set error messages in the error store
    errorStore.setErrorMessages(mainMessage, fieldMessages, statusCode, 'Account Deletion Failed');
  }
};
</script>
<template>
  <div>
    <!-- Delete Account Modal -->
    <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Confirm Account Deletion</h3>
        <p class="mb-4">Please enter your nickname or password to confirm account deletion.</p>
        <input
          v-model="confirmationInput"
          type="text"
          placeholder="Enter your nickname or password"
          class="w-full border border-gray-300 p-2 mb-4"
        />
        <div class="flex justify-end space-x-4">
          <button @click="cancelDelete" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
          <button @click="confirmDelete" class="bg-red-500 text-white px-4 py-2 rounded">Delete Account</button>
        </div>
      </div>
    </div>
  </div>
</template>

  