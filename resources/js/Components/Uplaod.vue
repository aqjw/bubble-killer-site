<script setup>
import useFormError from "@/Composables/useFormError.js";
import { storeToRefs, useUserStore } from "@/Stores";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const userStore = useUserStore();
const { isLogged } = storeToRefs(userStore);
//

const form = useForm({
    file: null,
    model: "lama",
});

const { errorAttributes } = useFormError(form);

const acceptedFormats = [".jpeg", ".jpg", ".png", ".zip"];

const submit = () => {
    form.post(route("upload"));
};
</script>

<template>
    <div>
        <v-card
            class="bg-zinc-200 rounded-md mb-8"
            rounded="lg"
            :elevation="4"
            :loading="form.progress"
        >
            <div class="max-w-lg p-6">
                <form
                    class="space-y-6"
                    enctype="multipart/form-data"
                    @submit.prevent="submit"
                >
                    <!-- File Upload -->
                    <div>
                        <p class="text-sm text-gray-500 flex gap-1 mb-2">
                            Accepted formats:
                            <span
                                v-for="format in acceptedFormats"
                                :key="format"
                                class="px-1 py-0.5 rounded-md text-xs"
                                :class="{
                                    'bg-slate-200 text-slate-600':
                                        isLogged || format !== '.zip',
                                    'bg-red-200 text-red-600':
                                        !isLogged && format === '.zip',
                                }"
                            >
                                {{ format }}
                            </span>
                        </p>

                        <v-file-input
                            label="File"
                            variant="outlined"
                            v-model="form.file"
                            prepend-inner-icon="mdi-paperclip"
                            :prepend-icon="''"
                            required
                            density="comfortable"
                            color="primary"
                            v-bind="errorAttributes('file')"
                        />
                    </div>

                    <!-- Model Selection (Styled to match file upload) -->
                    <div>
                        <v-select
                            label="Model"
                            variant="outlined"
                            v-model="form.model"
                            :items="models"
                            prepend-inner-icon="mdi-brain"
                            required
                            density="comfortable"
                            color="primary"
                            v-bind="errorAttributes('model')"
                        />
                    </div>

                    <!-- Submit Button with Loading Indicator -->
                    <div class="flex items-center justify-end mt-6">
                        <v-btn
                            :loading="form.processing"
                            color="primary"
                            class="text-none"
                            type="submit"
                        >
                            Загрузить
                        </v-btn>
                    </div>
                </form>
            </div>
        </v-card>
    </div>
</template>
