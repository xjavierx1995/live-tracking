<template>
  <q-layout view="hHh lpR fFf">
    <q-header class="app-header">
      <q-toolbar>
        <div class="header-brand">
          <q-icon name="radar" size="28px" class="q-mr-sm" />
          <span class="header-title">Live Tracking</span>
        </div>
        <q-space />
        <div class="header-user">
          <q-avatar size="32px" class="q-mr-sm">
            <div class="avatar-initials">
              {{ userInitial }}
            </div>
          </q-avatar>
          <span class="user-name">{{ authStore.user?.name }}</span>
          <q-btn flat round dense icon="logout" size="sm" class="q-ml-sm logout-btn" @click="handleLogout">
            <q-tooltip>Cerrar sesion</q-tooltip>
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
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const userInitial = computed(() => authStore.user?.name?.charAt(0)?.toUpperCase() || 'U')

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

<style scoped>
.app-header {
  background: var(--color-surface) !important;
  border-bottom: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
  color: var(--color-text-heading);
}

.header-brand {
  display: flex;
  align-items: center;
  color: var(--color-primary);
}

.header-title {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.3px;
}

.header-user {
  display: flex;
  align-items: center;
}

.avatar-initials {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: 600;
  font-size: 14px;
}

.user-name {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text);
}

.logout-btn {
  color: var(--color-text-muted);
}
.logout-btn:hover {
  color: var(--color-danger);
}
</style>
