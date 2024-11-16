<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const form = useForm({
  email: '',
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.post(route('password.email'), {
    onSuccess: () => form.reset('email'),
  });
};
</script>

<template>
  <Head title="Forgot Password" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Forgot your password?</div>
          <div class="text-gray-600 dark:text-gray-400 text-sm">
            No problem. Just let us know your email address and we will email you a
            password reset link that will allow you to choose a new one.
          </div>
        </div>

        <form @submit.prevent="submit">
          <div>
            <v-text-field
              label="Email"
              variant="outlined"
              v-model="form.email"
              prepend-inner-icon="mdi-at"
              required
              autocomplete="email"
              color="primary"
              density="comfortable"
              v-bind="errorAttributes('email')"
            />
          </div>

          <div class="mt-4 flex items-center justify-end">
            <v-btn
              :loading="form.processing"
              color="primary"
              class="text-none"
              type="submit"
            >
              Send
            </v-btn>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
