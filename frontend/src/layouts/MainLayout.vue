<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <q-toolbar-title> Live Tracking </q-toolbar-title>
        <q-space />
        <div class="q-gutter-sm row items-center">
          <span class="text-subtitle1">{{ authStore.user?.name }}</span>
          <q-btn flat round dense icon="logout" @click="handleLogout">
            <q-tooltip>Cerrar sesión</q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  if (!authStore.user) {
    authStore.fetchUser()
  }
})
</script>