<script setup>
import { onMounted, useTemplateRef, provide, ref } from 'vue';
import { RouterView } from 'vue-router';
import { useGameStore } from '@/stores/game';
import Toaster from '@/components/ui/toast/Toaster.vue';
import GlobalAlertDialog from '@/components/common/GlobalAlertDialog.vue';
import { useAuthStore } from '@/stores/auth';
import coinUrl from '@/assets/coin.png'


const storeGame = useGameStore();
const storeAuth = useAuthStore();
const alertDialog = useTemplateRef('alert-dialog');
provide('alertDialog', alertDialog);
const showDropdown = ref(false);
const logoutConfirmed = () => {
  storeAuth.logout();
};
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

console.log(storeAuth.userBrainCoinsBalance);

const logout = () => {
  alertDialog.value.open(
    logoutConfirmed,
    'Logout confirmation?',
    'Cancel',
    `Yes, I want to log out`,
    `Are you sure you want to log out? You can still access your account later with your credentials.`
  );
};
</script>

<template>
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog" />
  <div class="flex flex-col min-h-screen">
  <header class="bg-gray-800 text-white py-4 shadow-md">
  <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
    <!-- Logo or App Name -->
    <h1 class="text-xl font-bold">My Game App</h1>

    <!-- Navigation Links -->
    <nav class="flex space-x-6">
      <RouterLink
        to="/"
        class="hover:text-gray-300 font-medium"
        activeClass="underline underline-offset-4 decoration-gray-300"
      >
        Home
      </RouterLink>
      <RouterLink
        to="/chats"
        class="hover:text-gray-300 font-medium"
        activeClass="underline underline-offset-4 decoration-gray-300"
      >
        Chat
      </RouterLink>
      <RouterLink
        :to="{ name: 'games' }"
        class="hover:text-gray-300 font-medium"
        activeClass="underline underline-offset-4 decoration-gray-300"
      >
        My Games
      </RouterLink>

      <span class="flex font-medium align-items-right" >balance: {{ storeAuth.userBrainCoinsBalance}} &nbsp
      <img
          class="w-7 h-7 rounded-full"
          :src=coinUrl
          alt="Coin Icon"
        />
      </span>
    </nav>

      <!-- Avatar with Dropdown -->
       <!-- User Section -->
    <div class="relative">
      <button
        @click="toggleDropdown"
        class="flex items-center space-x-2 focus:outline-none"
      >
        <!-- Avatar -->
        <img
          v-if="storeAuth.user"
          class="w-10 h-10 rounded-full border-2 border-white"
          :src="storeAuth.userPhotoUrl"
          
          alt="User Avatar"
        />
        <!-- Nickname -->
        <span class="font-medium">{{ storeAuth.userFirstLastName }}</span>
      </button>

      <!-- Dropdown Menu -->
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 bg-white text-black rounded-lg shadow-lg w-40 z-50"
      >
        <ul>
          <li>
      <RouterLink
          :to="{ name: 'profile', params: { id: storeAuth.user.id } }"
        class="block px-4 py-2 hover:bg-gray-100 cursor-pointer"
        @click="showDropdown = false"
      >
        Profile
      </RouterLink>
    </li>
    <li>
    <RouterLink
          :to="{ name: 'changePassword', params: { id: storeAuth.user.id } }"
        class="block px-4 py-2 hover:bg-gray-100 cursor-pointer"
        @click="showDropdown = false"
      >
        Change Password
      </RouterLink>
    </li>
          <li
            @click="logout"
            class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
          >
            Logout
            </li>
            <li>
            <router-link 

  v-if="storeAuth.userType !== 'A'" 
  
 :to="{ name: 'accountDelete' }"
  class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
   @click="showDropdown = false"
>
  Delete Account
</router-link>
</li>

          </ul>
        </div>
        
      </div>
    </div>

</header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 py-8">
    <RouterView />
  </main>

  <!-- Footer -->
  <footer class="fixed bottom-0 left-0 z-20 w-full bg-gray-900 text-white py-6">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-sm">
          &copy; {{ new Date().getFullYear() }} My Game App. All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>
