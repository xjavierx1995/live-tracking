<template>
  <AuthLayout>
    <q-card-section>
      <q-form @submit="handleRegister" class="q-gutter-md">
        <q-input
          v-model="form.name"
          label="Nombre"
          :rules="[(val) => !!val || 'Nombre es requerido']"
          outlined
          dense
        />
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
          :rules="[(val) => !!val || 'Contraseña es requerida', (val) => val.length >= 8 || 'Mínimo 8 caracteres']"
          outlined
          dense
        />
        <q-input
          v-model="form.passwordConfirmation"
          label="Confirmar contraseña"
          type="password"
          :rules="[(val) => !!val || 'Confirma tu contraseña', (val) => val === form.password || 'Las contraseñas no coinciden']"
          outlined
          dense
        />
        <q-banner v-if="authStore.error" class="bg-negative text-white">
          {{ authStore.error }}
        </q-banner>
        <div class="q-mt-md">
          <q-btn
            type="submit"
            label="Registrarse"
            color="primary"
            class="full-width"
            :loading="authStore.loading"
          />
        </div>
      </q-form>
    </q-card-section>
    <q-card-section class="text-center">
      <router-link to="/login" class="text-primary">¿Ya tienes cuenta? Inicia sesión</router-link>
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
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
})

function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

async function handleRegister() {
  const success = await authStore.register({
    name: form.name,
    email: form.email,
    password: form.password,
    password_confirmation: form.passwordConfirmation,
  })
  if (success) {
    router.push('/dashboard')
  }
}
</script>