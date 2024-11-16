<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import { useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';

const $toast = useToast();

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const passVisible1 = ref(false);
const passVisible2 = ref(false);
const passVisible3 = ref(false);

const form = useForm({
  current_password: null,
  password: null,
  password_confirmation: null,
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      $toast.success('Пароль успешно изменен');
      form.reset();
    },
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation');
        passwordInput.value.focus();
      }
      if (form.errors.current_password) {
        form.reset('current_password');
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<template>
  <form @submit.prevent="submit">
    <div class="-mx-4">
      <v-container>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              ref="currentPasswordInput"
              label="Current Password"
              variant="outlined"
              v-model="form.current_password"
              required
              autocomplete="current_password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible1 ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible1 ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible1 = !passVisible1"
              v-bind="errorAttributes('current_password')"
            />
          </v-col>

          <v-col cols="12" md="6"> </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              ref="passwordInput"
              label="New Password"
              variant="outlined"
              v-model="form.password"
              required
              autocomplete="new_password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible2 ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible2 ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible2 = !passVisible2"
              v-bind="errorAttributes('password')"
            />
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              label="New Password (confirm)"
              variant="outlined"
              v-model="form.password_confirmation"
              required
              autocomplete="new_password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible3 ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible3 ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible3 = !passVisible3"
              v-bind="errorAttributes('password_confirmation')"
            />
          </v-col>
        </v-row>
      </v-container>
    </div>

    <v-btn
      class="mt-2 text-none"
      variant="tonal"
      type="submit"
      color="primary"
      :loading="form.processing"
    >
      Change Password
    </v-btn>
  </form>
</template>
