<script setup>
import { onMounted, useTemplateRef, provide } from 'vue'
import { RouterView } from 'vue-router'
import { useGameStore } from '@/stores/game'
import Toaster from '@/components/ui/toast/Toaster.vue'
import GlobalAlertDialog from '@/components/common/GlobalAlertDialog.vue'
import Chat from '@/components/chats/Chat.vue'
import { useAuthStore } from '@/stores/auth'
const storeGame = useGameStore()

const storeAuth = useAuthStore()


const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

const logoutConfirmed = () => {
storeAuth.logout()
}

const logout = () => {
alertDialog.value.open(logoutConfirmed,
'Logout confirmation?', 'Cancel', `Yes, I want to log out`,
`Are you sure you want to log out? You can still access your account later with
your credentials.`)
}
</script>

<template>
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>
  <div class="p-8 mx-auto max-w-3xl">
    <div class="flex justify-between">
<h1 class="text-4xl pb-8">Games
{{ storeAuth.userFirstLastName ? 'of ' + storeAuth.userFirstLastName: ''}}</h1>
<img v-if="storeAuth.user" class="w-14 h-14 rounded-full"
:src="storeAuth.userPhotoUrl" alt="Rounded avatar">
</div>
    <h1 class="text-5xl pb-8">Games</h1>
    <nav class="flex space-x-1 border-b-2 border-gray-800 text-base">
    
      <RouterLink :to="{ name: 'games'}" class="w-24 h-10 leading-10 text-center rounded-t-xl 
          border-none  text-white select-none bg-gray-400 cursor-pointer hover:bg-gray-500"
          activeClass="bg-gray-800 hover:bg-gray-800"> 
                  My Games
      </RouterLink> 
      <RouterLink :to="{ name: 'chat'}" class="w-24 h-10 leading-10 text-center rounded-t-xl 
          border-none  text-white select-none bg-gray-400 cursor-pointer hover:bg-gray-500"
          activeClass="bg-gray-800 hover:bg-gray-800"> 
                  Chat
      </RouterLink> 
      <span class="grow"></span>
      <RouterLink v-show="!storeAuth.user" :to="{ name: 'login'}" class="w-24 h-10 leading-10 text-center rounded-t-xl 
          border-none  text-white select-none bg-gray-400 cursor-pointer hover:bg-gray-500"
          activeClass="bg-gray-800 hover:bg-gray-800"> 
                  Login
      </RouterLink> 
      <button v-show="storeAuth.user" @click="logout" class="w-24 h-10 leading-10 text-center rounded-t-xl 
          border-none  text-white select-none bg-gray-400 cursor-pointer hover:bg-gray-500"> 
                  Logout
      </button> 
    </nav>
    <RouterView></RouterView>   
  </div>
</template>
