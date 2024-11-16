<script setup>
import { ref } from 'vue';

const dialog = ref(false);

const openDialog = () => {
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
};

defineExpose({
  open: openDialog,
});
</script>

<template>
  <v-dialog v-model="dialog" max-width="300" :opacity="0.6" @click:outside="closeDialog">
    <v-card
      :prepend-icon="'mdi-information-outline'"
      title="Login Requires"
      text="Please login or register to continue."
    >
      <template v-slot:actions>
        <v-spacer></v-spacer>
        <v-btn :to="route('register')" class="text-none" color="primary" variant="text">
          Register
        </v-btn>

        <v-btn :to="route('login')" class="text-none" color="primary" variant="tonal">
          Login
        </v-btn>
      </template>
    </v-card>
  </v-dialog>
</template>
