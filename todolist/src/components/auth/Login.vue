<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useErrorStore } from '@/stores/error';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const storeAuth = useAuthStore();
const storeError = useErrorStore();

const credentials = ref({
  email: '',
  password: '',
});

const cancel = () => {
  router.back();
};

const login = () => {
  storeAuth.login(credentials.value);
};

// Redirect if the user is already authenticated
onMounted(() => {
  if (storeAuth.user) {
    router.push({ name: 'home' });
  }
});

// Navigate to the registration page
const goToRegister = () => {
  router.push({ name: 'register' });
};
</script>

<template>
  <Card class="w-[450px] mx-auto my-8 p-4 px-8">
    <CardHeader>
      <CardTitle>Login</CardTitle>
      <CardDescription>Enter your credentials to access your account.</CardDescription>
    </CardHeader>
    <CardContent>
      <form>
        <div class="grid items-center w-full gap-4">
          <div class="flex flex-col space-y-1.5">
            <Label for="email">Email</Label>
            <Input id="email" type="email" placeholder="User Email" v-model="credentials.email" />
            <ErrorMessage :errorMessage="storeError.fieldMessage('email')" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label for="password">Password</Label>
            <Input id="password" type="password" v-model="credentials.password" />
            <ErrorMessage :errorMessage="storeError.fieldMessage('password')" />
          </div>
        </div>
      </form>
    </CardContent>
    <CardFooter class="flex justify-between px-6 pb-6">
      <Button variant="outline" @click="cancel">
        Cancel
      </Button>
      <Button variant="outline" @click="goToRegister">
        Register
      </Button>
      <Button @click="login">
        Login
      </Button>
    </CardFooter>
  </Card>
</template>
