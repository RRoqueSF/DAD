<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const storeAuth = useAuthStore();

const isEditing = ref(false);
const updatedMessage = ref('');

const fetchProfile = async () => {
  try {
    const response = await axios.get('/users/me');
    storeAuth.value = response.data;
  } catch (error) {
    console.error('Error fetching profile:', error);
  }
};

const updateProfile = async () => {
  try {
    const response = await axios.put('/users/me', storeAuth.value);
    updatedMessage.value = response.data.message;
    isEditing.value = false;
  } catch (error) {
    console.error('Error updating profile:', error);
  }
};

onMounted(() => {
  fetchProfile();
});
</script>

<template>
  <div class="max-w-4xl mx-auto mt-8 p-4 border rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">{{storeAuth.userFirstLastName}} Profile</h1>
    <p v-if="storeAuth.userType === 'A'" class="text-xl font-semibold text-center text-gray-600">
  Admin
</p>
<br>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <!-- Profile Picture -->
      <div>
        <img
          :src="storeAuth.userPhotoUrl"
          alt="Profile Picture"
          class="w-32 h-32 rounded-full border shadow"
        />
      </div>

      <!-- User Details -->
      <div class="space-y-4">
        <div>
          <label class="block font-medium">Name</label>
          <Input
            v-model="storeAuth.userFirstLastName"
            :disabled="!isEditing"
            placeholder="Enter your name"
          />
        </div>
        <div>
          <label class="block font-medium">Nickname</label>
          <Input
            v-model="storeAuth.userNickname"
            :disabled="!isEditing"
            placeholder="Enter your nickname"
          />
        </div>
        <div>
          <label class="block font-medium">Custom Data</label>
          <Input
            v-model="storeAuth.userCustomData"
            :disabled="!isEditing"
            placeholder="Add custom information"
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

