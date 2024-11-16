<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const passVisible = ref(false);

const form = useForm({
  name: '',
  email: '',
  password: '',
});

const { errorAttributes } = useFormError(form);

const submit = () => {
  form.post(route('register'));
};
</script>

<template>
  <Head title="Register" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Register</div>
          <div class="text-gray-600 dark:text-gray-400">
            Пожалуйста, создайте себе учетную запись
          </div>
        </div>

        <form @submit.prevent="submit">
          <div>
            <v-text-field
              label="Name"
              variant="outlined"
              v-model="form.name"
              prepend-inner-icon="mdi-account-circle-outline"
              required
              autofocus
              autocomplete="username"
              density="comfortable"
              color="primary"
              v-bind="errorAttributes('name')"
            />
          </div>

          <div class="mt-4">
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
              v-bind="errorAttributes('password')"
            />
          </div>

          <div class="mt-8 d-flex items-center justify-between">
            <v-btn
              :to="route('login')"
              color="primary"
              class="text-none"
              variant="text"
              density="compact"
            >
              Already registered?
            </v-btn>

            <v-btn
              :loading="form.processing"
              color="primary"
              class="text-none"
              type="submit"
            >
              Register
            </v-btn>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
