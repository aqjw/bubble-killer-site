<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();

const form = useForm({
  password: null,
});

const passwordInput = ref(null);
const passVisible = ref(false);

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.delete(route('upi.profile.destroy'), {
    preserveScroll: true,
    onError: () => {
      if (form.errors.password) {
        form.reset('password');
        passwordInput.value.focus();
      } else {
        $toast.error('Что-то пошло не так.');
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
              ref="passwordInput"
              label="Current Password"
              variant="outlined"
              v-model="form.password"
              required
              autocomplete="current_password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible = !passVisible"
              v-bind="errorAttributes('password')"
            />
          </v-col>
        </v-row>
      </v-container>
    </div>

    <v-btn
      class="mt-2 text-none"
      variant="tonal"
      type="submit"
      color="red"
      :loading="form.processing"
    >
      Destroy
    </v-btn>
  </form>
</template>
