<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="auth-page">
        <div class="auth-brand">
          <div class="brand-content">
            <div class="brand-icon">
              <q-icon name="radar" size="56px" />
            </div>
            <h1 class="brand-title">Live Tracking</h1>
            <p class="brand-subtitle">Monitoreo de flotas en tiempo real</p>
            <div class="brand-features">
              <div class="brand-feature">
                <q-icon name="visibility" size="20px" />
                <span>Seguimiento GPS en tiempo real</span>
              </div>
              <div class="brand-feature">
                <q-icon name="route" size="20px" />
                <span>Rutas optimizadas con OpenRouteService</span>
              </div>
              <div class="brand-feature">
                <q-icon name="analytics" size="20px" />
                <span>Panel de control interactivo</span>
              </div>
            </div>
          </div>
          <div class="brand-pattern"></div>
        </div>

        <div class="auth-form-area">
          <div class="auth-form-wrapper">
            <slot />
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
</script>

<style scoped>
.auth-page {
  display: flex;
  min-height: 100vh;
  background: white;
}

.auth-brand {
  flex: 1;
  background: linear-gradient(135deg, #4A6CF7 0%, #6C5CE7 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  padding: 48px;
}

.brand-pattern {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
  pointer-events: none;
}

.brand-content {
  position: relative;
  z-index: 1;
  max-width: 400px;
}

.brand-icon {
  width: 88px;
  height: 88px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-bottom: 32px;
  backdrop-filter: blur(10px);
}

.brand-title {
  font-size: 36px;
  font-weight: 700;
  color: white;
  margin: 0 0 12px;
  letter-spacing: -0.5px;
}

.brand-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 40px;
  line-height: 1.5;
}

.brand-features {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.brand-feature {
  display: flex;
  align-items: center;
  gap: 12px;
  color: rgba(255, 255, 255, 0.9);
  font-size: 14px;
}

.brand-feature .q-icon {
  opacity: 0.9;
}

.auth-form-area {
  width: 480px;
  min-width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  background: var(--color-bg);
}

.auth-form-wrapper {
  width: 100%;
  max-width: 360px;
}

@media (max-width: 900px) {
  .auth-page {
    flex-direction: column;
  }

  .auth-brand {
    flex: none;
    padding: 40px 24px;
    min-height: auto;
  }

  .brand-content {
    text-align: center;
    max-width: 100%;
  }

  .brand-icon {
    margin: 0 auto 24px;
    width: 72px;
    height: 72px;
    border-radius: 20px;
  }

  .brand-title {
    font-size: 28px;
    margin-bottom: 8px;
  }

  .brand-subtitle {
    font-size: 14px;
    margin-bottom: 24px;
  }

  .brand-features {
    display: none;
  }

  .auth-form-area {
    width: 100%;
    min-width: 100%;
    flex: 1;
    padding: 32px 24px;
  }
}
</style>
