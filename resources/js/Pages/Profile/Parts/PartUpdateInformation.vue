<script setup>
import useFormError from "@/Composables/useFormError.js";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";

const props = defineProps({
    user: Object,
});

const $toast = useToast();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
});

const { errorAttributes } = useFormError(form);

const submit = () => {
    form.post(route("upi.profile.update_information"), {
        preserveScroll: true,
        onSuccess: ({ props }) => {
            $toast.success("Успешно сохранено");
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
                            label="Name"
                            variant="outlined"
                            v-model="form.name"
                            prepend-inner-icon="mdi-account-circle-outline"
                            required
                            autocomplete="name"
                            density="comfortable"
                            color="primary"
                            v-bind="errorAttributes('name')"
                        />
                    </v-col>

                    <v-col cols="12" md="6">
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
            Save Changes
        </v-btn>
    </form>
</template>
