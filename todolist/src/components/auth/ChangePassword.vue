<script setup>
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.js";
import { useErrorStore } from "@/stores/error"; // Import the error store
import { ref } from "vue";

const router = useRouter();
const authStore = useAuthStore();
const errorStore = useErrorStore(); // Initialize the error store

const passwords = ref({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const changePassword = async () => {
  try {
    await authStore.changePassword(passwords.value);
    router.back();
  } catch (error) {
    const { response } = error;
    if (response) {
      errorStore.setErrorMessages(
        response.data.message || "An error occurred!",
        response.data.errors || [],
        response.status,
        "Change Password Error"
      );
    } else {
      errorStore.setErrorMessages(
        "Unknown server error. Please try again later.",
        [],
        500,
        "Server Error"
      );
    }
  }
};
</script>
<template>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
      <form class="space-y-6" @submit.prevent="changePassword">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Change Password</h3>
        <hr class="mb-4">
  
        <!-- Current Password -->
        <div>
          <label for="inputCurrentPassword" class="block text-sm font-medium text-gray-700">Current Password</label>
          <input
            type="password"
            id="inputCurrentPassword"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            required
            v-model="passwords.current_password"
          >
          <p class="text-red-500 text-sm mt-1">{{ errorStore.fieldMessage("current_password") }}</p>
        </div>
  
        <!-- New Password -->
        <div>
          <label for="inputPassword" class="block text-sm font-medium text-gray-700">New Password</label>
          <input
            type="password"
            id="inputPassword"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            required
            v-model="passwords.password"
          >
          <p class="text-red-500 text-sm mt-1">{{ errorStore.fieldMessage("password") }}</p>
        </div>
  
        <!-- Password Confirmation -->
        <div>
          <label for="inputPasswordConfirm" class="block text-sm font-medium text-gray-700">New Password Confirmation</label>
          <input
            type="password"
            id="inputPasswordConfirm"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            required
            v-model="passwords.password_confirmation"
          >
          <p class="text-red-500 text-sm mt-1">{{ errorStore.fieldMessage("password_confirmation") }}</p>
        </div>
  
        <!-- Submit Button -->
        <div class="text-center">
          <button
            type="button"
            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400"
            @click="changePassword"
          >
            Change Password
          </button>
        </div>
      </form>
    </div>
  </template>
  