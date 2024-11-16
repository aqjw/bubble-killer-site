<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import useFormError from '@/Composables/useFormError.js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SvgLogin from '@/Components/Svg/SvgLogin.vue';

const form = useForm({});

const submit = () => {
  form.post(route('verification.send'));
};
</script>

<template>
  <Head title="Email Verification" />

  <AuthLayout>
    <div class="d-flex items-center">
      <div class="w-1/2 bg-gray-50 dark:bg-neutral-800">
        <SvgLogin class="w-full" />
      </div>

      <div class="w-1/2 p-8 px-20 rounded-2xl">
        <div class="text-center mb-8">
          <div class="text-2xl font-semibold mb-1">Email Verification</div>
          <div class="text-gray-600 dark:text-gray-400">
            Before getting started, could you verify your email
            address by clicking on the link we just emailed to you? If you didn't receive
            the email, we will gladly send you another.
          </div>
        </div>

        <form @submit.prevent="submit">
          <div class="d-flex align-center justify-end gap-2">
            <v-btn
              :to="{
                href: route('logout'),
                method: 'post',
              }"
              variant="text"
              color="red-lighten-1"
              class="text-none"
            >
              Log out
            </v-btn>

            <v-btn
              :loading="form.processing"
              color="primary"
              class="text-none"
              type="submit"
            >
              Resend
            </v-btn>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>
