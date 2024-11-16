<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const passVisible = ref(false);

const form = useForm({
  password: '',
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset(),
  });
};
</script>

<template>
  <Head title="Confirm Password" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Confirm Password</div>
          <div class="text-gray-600 dark:text-gray-400">
            This is a secure area of the application. Please confirm your password before
            continuing.
          </div>
        </div>

        <form @submit.prevent="submit">
          <div>
            <v-text-field
              label="Password"
              variant="outlined"
              v-model="form.password"
              required
              autofocus
              autocomplete="current-password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible = !passVisible"
              v-bind="errorAttributes('password')"
            />
          </div>

          <div class="mt-4 flex justify-end">
            <v-btn
              :loading="form.processing"
              color="primary"
              class="text-none"
              type="submit"
            >
              Confirm
            </v-btn>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
