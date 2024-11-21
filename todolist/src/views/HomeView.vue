<script setup>
import { useRouter, RouterLink, RouterView } from 'vue-router'

</script>
<template>
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Welcome!</h1>
          <p class="fs-4">Create your own Game!</p>
          <!--<router-link class="btn btn-success btn-lg mt-5" :to="{name: 'NewVcard'}">Get Started</router-link>-->
        </div>
      </div>
    </div>
  </header>
</template>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>