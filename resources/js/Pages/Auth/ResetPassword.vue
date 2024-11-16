<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
});

const passVisible = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Reset Password" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Reset Password</div>
          <div class="text-gray-600 dark:text-gray-400">
            Пожалуйста, введите новый пароль
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
              density="comfortable"
              color="primary"
              v-bind="errorAttributes('email')"
            />
          </div>

          <div class="mt-4">
            <v-text-field
              label="New password"
              variant="outlined"
              v-model="form.password"
              required
              autofocus
              autocomplete="new-password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible = !passVisible"
              v-bind="errorAttributes('password')"
            />
          </div>

          <div class="mt-4 flex items-center justify-end">
            <v-btn
              :loading="form.processing"
              color="primary"
              class="text-none"
              type="submit"
            >
              Reset Password
            </v-btn>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
