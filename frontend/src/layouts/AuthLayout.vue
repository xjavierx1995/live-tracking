<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="q-pa-md shadow-2" style="width: 400px; max-width: 90vw">
          <q-card-section class="text-center">
            <div class="text-h5 text-primary">Live Tracking</div>
          </q-card-section>
          <slot />
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
</script>