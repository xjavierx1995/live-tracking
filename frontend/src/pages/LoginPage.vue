<template>
  <AuthLayout>
    <div class="auth-card">
      <div class="auth-header">
        <h2 class="auth-title">Iniciar sesion</h2>
        <p class="auth-subtitle">Ingresa tus credenciales para acceder</p>
      </div>

      <q-form @submit="handleLogin" class="auth-form">
        <div class="form-group">
          <label class="form-label">Email</label>
          <q-input
            v-model="form.email"
            placeholder="tu@email.com"
            type="email"
            :rules="[(val) => !!val || 'Email es requerido', (val) => isValidEmail(val) || 'Email invalido']"
            outlined
            dense
            no-error-icon
            class="form-input"
          >
            <template #prepend>
              <q-icon name="mail" size="18px" class="text-grey-5" />
            </template>
          </q-input>
        </div>

        <div class="form-group">
          <label class="form-label">Contrasena</label>
          <q-input
            v-model="form.password"
            placeholder="Tu contrasena"
            type="password"
            :rules="[(val) => !!val || 'Contrasena es requerida']"
            outlined
            dense
            no-error-icon
            class="form-input"
          >
            <template #prepend>
              <q-icon name="lock" size="18px" class="text-grey-5" />
            </template>
          </q-input>
        </div>

        <div v-if="authStore.error" class="auth-error">
          <q-icon name="error" size="16px" />
          {{ authStore.error }}
        </div>

        <q-btn
          type="submit"
          no-caps
          unelevated
          class="auth-submit"
          :loading="authStore.loading"
        >
          Iniciar sesion
        </q-btn>
      </q-form>

      <div class="auth-footer">
        <span class="auth-footer-text">No tienes cuenta?</span>
        <router-link to="/register" class="auth-link">Registrate</router-link>
      </div>
    </div>
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

<style scoped>
.auth-card {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.auth-header {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.auth-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--color-text-heading);
  margin: 0;
}

.auth-subtitle {
  font-size: 14px;
  color: var(--color-text-muted);
  margin: 0;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-heading);
}

.form-input :deep(.q-field__control) {
  height: 42px !important;
  min-height: 42px;
  border-radius: 10px !important;
  border-color: #D1D5DB !important;
  background: white !important;
}

.form-input :deep(.q-field__control::before) {
  border-color: #D1D5DB;
}

.form-input :deep(.q-field--focused .q-field__control::after) {
  border-color: var(--color-primary) !important;
  border-width: 2px;
}

.form-input :deep(.q-field__native) {
  font-size: 14px;
  color: var(--color-text-heading);
  padding-top: 2px;
}

.form-input :deep(.q-field__placeholder) {
  color: #9CA3AF;
  font-size: 14px;
}

.form-input :deep(.q-field__prepend) {
  color: #9CA3AF;
  padding-right: 4px;
}

.auth-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  background: var(--color-danger-light);
  color: var(--color-danger);
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 500;
}

.auth-submit {
  width: 100%;
  height: 44px !important;
  min-height: 44px;
  background: var(--color-primary) !important;
  color: white !important;
  border-radius: 10px !important;
  font-size: 14px;
  font-weight: 600 !important;
  letter-spacing: 0.2px;
  margin-top: 4px;
}

.auth-submit:hover {
  background: #3B5DE7 !important;
}

.auth-footer {
  text-align: center;
  padding-top: 4px;
}

.auth-footer-text {
  font-size: 14px;
  color: var(--color-text-muted);
  margin-right: 4px;
}

.auth-link {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-primary);
  text-decoration: none;
}

.auth-link:hover {
  text-decoration: underline;
}
</style>
