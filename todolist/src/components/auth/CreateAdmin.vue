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

// CreationDatation form data
const CreationData = ref({
  name: '',
  nickname: '',
  email: '',
  password: '',
});

// Error and loading states
const errors = ref({});
const loading = ref(false);



// Handle CreationDatation submission
const register = async () => {
  loading.value = true;
  try {
    // Clear previous errors
    errors.value = {};

    // Debugging: log the data being sent
    console.log('creation data:', CreationData.value);

    // Send data to the backend
    const response = await axios.post('/admins', CreationData.value);

    // Debugging: log the response
    console.log('Response:', response);

    // Handle successful CreationDatation
    alert(response.data.message || 'Admin creation successful!');
  } catch (error) {
    // Display validation errors
    if (error.response && error.response.data.errors) {
      errors.value = error.response.data.errors;
    } else {
      console.error('Admin creation failed:', error);
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
      <CardTitle>Admin Creation</CardTitle>
      <CardDescription>Create a new Admin account by filling out the form below.</CardDescription>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="register">
        <div class="grid items-center w-full gap-4">
          <!-- Name Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="name">Name</Label>
            <Input id="name" type="text" v-model="CreationData.name" placeholder="Enter your full name" />
            <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name }}</p>
          </div>
          
            <!-- Nickname Field --> 
            <div class="flex flex-col space-y-1.5">
                <Label for="nickname">Nickname</Label>
                <Input id="nickname" type="text" v-model="CreationData.nickname" placeholder="Enter your nickname" />
                <p v-if="errors.nickname" class="text-red-600 text-sm">{{ errors.nickname }}</p>
            </div>
        
          <!-- Email Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="email">Email</Label>
            <Input id="email" type="email" v-model="CreationData.email" placeholder="Enter your email" />
            <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email }}</p>
          </div>

          <!-- Password Field -->
          <div class="flex flex-col space-y-1.5">
            <Label for="password">Password</Label>
            <Input id="password" type="password" v-model="CreationData.password" placeholder="Enter your password" />
            <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password }}</p>
          </div>
        </div>
        <Button type="submit" :disabled="loading.value">Create</Button>
      </form>
    </CardContent>
    <CardFooter class="flex justify-between px-6 pb-6">
      <Button variant="outline" @click="cancel">Cancel</Button>
    </CardFooter>
  </Card>
</template>
