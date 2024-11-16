<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const passVisible = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.post(route('login'));
};
</script>

<template>
  <Head title="Log in" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Login</div>
          <div class="text-gray-600 dark:text-gray-400">Пожалуйста, войдите в свою учетную запись</div>
        </div>

        <form @submit.prevent="submit">
          <div>
            <v-text-field
              label="Email"
              variant="outlined"
              v-model="form.email"
              prepend-inner-icon="mdi-at"
              required
              autofocus
              autocomplete="email"
              density="comfortable"
              color="primary"
              v-bind="errorAttributes('email')"
            />
          </div>

          <div class="mt-4">
            <v-text-field
              label="Password"
              variant="outlined"
              v-model="form.password"
              required
              autocomplete="password"
              density="comfortable"
              color="primary"
              :append-inner-icon="passVisible ? 'mdi-eye-off' : 'mdi-eye'"
              :type="passVisible ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-outline"
              @click:append-inner="passVisible = !passVisible"
              hide-details
            />
            <div class="mt-1 flex items-center justify-end">
              <v-btn
                :to="route('password.request')"
                color="primary"
                class="text-none"
                variant="text"
                density="compact"
              >
                Forgot your password?
              </v-btn>
            </div>
          </div>

          <div class="mt-8 d-flex items-center justify-between">
            <v-checkbox
              v-model.checked="form.remember"
              color="primary"
              label="Remember me"
              density="compact"
              hide-details
            />

            <div class="d-flex align-center gap-2">
              <v-btn
                :to="route('register')"
                color="primary"
                class="text-none"
                variant="text"
              >
                Register
              </v-btn>
              <v-btn
                :loading="form.processing"
                color="primary"
                class="text-none"
                type="submit"
              >
                Login
              </v-btn>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
