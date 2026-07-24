<template>
  <AuthLayout>
    <div class="auth-card">
      <div class="auth-header">
        <h2 class="auth-title">Crear cuenta</h2>
        <p class="auth-subtitle">Registrate para comenzar a monitorear</p>
      </div>

      <q-form @submit="handleRegister" class="auth-form">
        <div class="form-group">
          <label class="form-label">Nombre</label>
          <q-input
            v-model="form.name"
            placeholder="Tu nombre"
            :rules="[(val) => !!val || 'Nombre es requerido']"
            outlined
            dense
            no-error-icon
            class="form-input"
          >
            <template #prepend>
              <q-icon name="person" size="18px" class="text-grey-5" />
            </template>
          </q-input>
        </div>

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

        <div class="form-row">
          <div class="form-group form-group--half">
            <label class="form-label">Contrasena</label>
            <q-input
              v-model="form.password"
              placeholder="Minimo 8 caracteres"
              type="password"
              :rules="[(val) => !!val || 'Contrasena es requerida', (val) => val.length >= 8 || 'Minimo 8 caracteres']"
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

          <div class="form-group form-group--half">
            <label class="form-label">Confirmar</label>
            <q-input
              v-model="form.passwordConfirmation"
              placeholder="Repite tu contrasena"
              type="password"
              :rules="[(val) => !!val || 'Confirma tu contrasena', (val) => val === form.password || 'No coinciden']"
              outlined
              dense
              no-error-icon
              class="form-input"
            >
              <template #prepend>
                <q-icon name="lock_outline" size="18px" class="text-grey-5" />
              </template>
            </q-input>
          </div>
        </div>

        <div v-if="authStore.error" class="auth-error">
          <q-icon name="error" size="16px" />
          {{ authStore.error }}
        </div>

        <q-btn
          type="submit"
          no-caps
          unelevated
          color="primary"
          class="auth-submit"
          :loading="authStore.loading"
        >
          Crear cuenta
        </q-btn>
      </q-form>

      <div class="auth-footer">
        <span class="auth-footer-text">Ya tienes cuenta?</span>
        <router-link to="/login" class="auth-link">Inicia sesion</router-link>
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

<style scoped>
.auth-card {
  display: flex;
  flex-direction: column;
  gap: 24px;
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
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-row {
  display: flex;
  gap: 12px;
}

.form-group--half {
  flex: 1;
  min-width: 0;
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

@media (max-width: 400px) {
  .form-row {
    flex-direction: column;
    gap: 16px;
  }
}
</style>
