<template>
  <AuthLayout>
    <q-card-section>
      <q-form @submit="handleLogin" class="q-gutter-md">
        <q-input
          v-model="form.email"
          label="Email"
          type="email"
          :rules="[(val) => !!val || 'Email es requerido', (val) => isValidEmail(val) || 'Email inválido']"
          outlined
          dense
        />
        <q-input
          v-model="form.password"
          label="Contraseña"
          type="password"
          :rules="[(val) => !!val || 'Contraseña es requerida']"
          outlined
          dense
        />
        <q-banner v-if="authStore.error" class="bg-negative text-white">
          {{ authStore.error }}
        </q-banner>
        <div class="q-mt-md">
          <q-btn
            type="submit"
            label="Iniciar sesión"
            color="primary"
            class="full-width"
            :loading="authStore.loading"
          />
        </div>
      </q-form>
    </q-card-section>
    <q-card-section class="text-center">
      <router-link to="/register" class="text-primary">¿No tienes cuenta? Regístrate</router-link>
    </q-card-section>
  </AuthLayout>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

async function handleLogin() {
  const success = await authStore.login(form)
  if (success) {
    router.push('/dashboard')
  }
}
</script>