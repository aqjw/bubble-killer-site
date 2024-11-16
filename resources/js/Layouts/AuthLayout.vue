<script setup>
import Navbar from '@/Components/Layout/Navbar.vue';
</script>

<template>
  <v-app id="inspire" class="bg-transparent">
    <Navbar />

    <v-main>
      <v-container>
        <v-sheet rounded="lg" class="overflow-hidden !shadow-lg">
          <slot />
        </v-sheet>
      </v-container>
    </v-main>
  </v-app>
</template>
