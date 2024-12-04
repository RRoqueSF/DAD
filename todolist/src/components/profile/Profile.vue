<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const storeAuth = useAuthStore();

const isEditing = ref(false);
const updatedMessage = ref('');
const localUser = ref({});


// Fetch the profile data from the API
const fetchProfile = async () => {
  try {
    const response = await axios.get(`/users/${storeAuth.user.id}`);
    storeAuth.user.value = response.data;
    localUser.value = { ...response.data };
  } catch (error) {
    console.error('Error fetching profile:', error);
  }
};

// Update the user profile
const updateProfile = async () => {
  try {
    console.log('Dados enviados:', localUser.value); // Log the local user data before sending to the server

    const response = await axios.put(`/users/${storeAuth.user.id}`, localUser.value); // Send the local copy to the server
    updatedMessage.value = response.data.message;
    isEditing.value = false;
    storeAuth.user.value = { ...localUser.value }; // Update global store with the new data
  } catch (error) {
    console.error('Error updating profile:', error);
  }
};

// Fetch user data when the component is mounted
onMounted(() => {
  fetchProfile();
});

// Handle photo file selection
const handlePhotoChange = (event) => {
  selectedFile.value = event.target.files[0];
};
</script>

<template>
  <div class="max-w-4xl mx-auto mt-8 p-4 border rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">{{ storeAuth.userFirstLastName }} Profile</h1>
    <p v-if="storeAuth.userType === 'A'" class="text-xl font-semibold text-center text-gray-600">
      Admin
    </p>
    <br>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <!-- Profile Picture -->
      <div class="flex flex-col items-center">
        <img
          :src="storeAuth.userPhotoUrl"
          alt="Profile Picture"
          class="w-32 h-32 rounded-full border shadow mb-4"
        />
        <input
          v-if="isEditing"
          type="file"
          @change="handlePhotoChange"
          class="file-input"
        />
      </div>

      <!-- User Details -->
      <div class="space-y-4">
        <div>
          <label class="block font-medium">Name</label>
          <Input
            v-model="localUser.name"
            :disabled="!isEditing"
            :placeholder="storeAuth.user.name"
          />
        </div>
        <div>
          <label class="block font-medium">Nickname</label>
          <Input
            v-model="localUser.nickname"
            :disabled="!isEditing"
            :placeholder="storeAuth.userNickname"
          />
        </div>
        <div>
          <label class="block font-medium">Email</label>
          <Input
            v-model="localUser.email"
            :disabled="!isEditing"
            :placeholder="storeAuth.userEmail"
          />
        </div>
        <div>
          <label class="block font-medium">Custom Data</label>
          <Input
            v-model="localUser.custom"
            :disabled="!isEditing"
            :placeholder="storeAuth.customData"
          />
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end mt-6 space-x-4">
      <Button
        variant="outline"
        v-if="!isEditing"
        @click="isEditing = true"
      >
        Edit Profile
      </Button>
      <Button
        v-if="isEditing"
        @click="updateProfile"
      >
        Save Changes
      </Button>
    </div>

    <p v-if="updatedMessage" class="mt-4 text-green-600">{{ updatedMessage }}</p>
  </div>
</template>