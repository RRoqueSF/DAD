<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import axios from 'axios';

// Initialize Vue Router
const router = useRouter();

// Registration form data
const registrationData = ref({
  name: '',
  nickname: '',
  email: '',
  password: '',
  password_confirmation: '',
  base64ImagePhoto: '', // Base64 image for photo upload
});

// Error and loading states
const errors = ref({});
const loading = ref(false);

// File and base64 processing
const selectedFile = ref(null);

// Convert file to base64
const toBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
    reader.readAsDataURL(file);
  });
};

// Handle file selection
const handlePhotoChange = async (event) => {
  selectedFile.value = event.target.files[0];
  if (selectedFile.value) {
    try {
      registrationData.value.base64ImagePhoto = await toBase64(selectedFile.value);
    } catch (error) {
      console.error('Error converting file to base64:', error);
    }
  }
};

// Handle registration submission
const register = async () => {
  loading.value = true;
  try {
    // Clear previous errors
    errors.value = {};

    // Debugging: log the data being sent
    console.log('Registration data:', registrationData.value);

    // Send data to the backend
    const response = await axios.post('/users', registrationData.value);

    // Debugging: log the response
    console.log('Response:', response);

    // Handle successful registration
    alert(response.data.message || 'Registration successful!');
    router.push({ name: 'login' });
  } catch (error) {
    // Display validation errors
    if (error.response && error.response.data.errors) {
      errors.value = error.response.data.errors;
    } else {
      console.error('Registration failed:', error);
    }
  } finally {
    loading.value = false; // Reset loading state
  }
};

// Cancel button handler
const cancel = () => {
  router.back();
};
</script>

<template>
  <Card class="w-[450px] mx-auto my-8 p-4 px-8">
    <CardHeader>
      <CardTitle>Register</CardTitle>
      <CardDescription>Create a new account by filling out the form below.</CardDescription>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="register">
        <div class="grid items-center w-full gap-4">
          <!-- Name Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="name">Name</Label>
            <Input id="name" type="text" v-model="registrationData.name" placeholder="Enter your full name" />
            <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name }}</p>
          </div>

          <!-- Nickname Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="nickname">Nickname</Label>
            <Input id="nickname" type="text" v-model="registrationData.nickname" placeholder="Enter your nickname" />
            <p v-if="errors.nickname" class="text-red-600 text-sm">{{ errors.nickname }}</p>
          </div>

          <!-- Email Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="email">Email</Label>
            <Input id="email" type="email" v-model="registrationData.email" placeholder="Enter your email" />
            <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email }}</p>
          </div>

          <!-- Password Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="password">Password</Label>
            <Input id="password" type="password" v-model="registrationData.password" placeholder="Enter your password" />
            <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password }}</p>
          </div>

          <!-- Password Confirmation -->
          <div class="flex flex-col space-y-1.5">
            <Label for="password_confirmation">Confirm Password</Label>
            <Input id="password_confirmation" type="password" v-model="registrationData.password_confirmation" placeholder="Confirm your password" />
            <p v-if="errors.password_confirmation" class="text-red-600 text-sm">{{ errors.password_confirmation }}</p>
          </div>

          <!-- Photo Upload -->
          <div class="flex flex-col space-y-1.5">
            <Label for="photo">Profile Photo (Optional)</Label>
            <input
              id="photo"
              type="file"
              @change="handlePhotoChange"
              class="file-input"
            />
            <p v-if="errors.base64ImagePhoto" class="text-red-600 text-sm">{{ errors.base64ImagePhoto }}</p>
          </div>
        </div>
        <Button type="submit" :disabled="loading.value">Register</Button>
      </form>
    </CardContent>
    <CardFooter class="flex justify-between px-6 pb-6">
      <Button variant="outline" @click="cancel">Cancel</Button>
    </CardFooter>
  </Card>
</template>
