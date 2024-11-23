<script setup>
import FileUploader from "@/Components/FileUploader.vue";
import SvgSelectFiles from "@/Components/Svg/SvgSelectFiles.vue";
import useFormError from "@/Composables/useFormError.js";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    files: [],
    model: "lama",
});
const { errorAttributes } = useFormError(form);

const submit = () => {
    form.post(route("upload"));
};

const removeFile = (index) => {
    form.files.splice(index, 1);
};
const createObjectURL = (path) => {
    return URL.createObjectURL(path);
};
</script>

<template>
    <Head title="Upload" />

    <AppLayout>
        <div>
            <form enctype="multipart/form-data" @submit.prevent="submit">
                <div class="flex justify-between gap-4 relative">
                    <div class="flex-1">
                        <v-list v-if="form.files.length" lines="one">
                            <v-list-item
                                v-for="(file, index) in form.files"
                                :key="file.name"
                                :title="file.name"
                                class="roundless"
                            >
                                <template v-slot:prepend>
                                    <v-avatar class="!bg-gray-400">
                                        <img
                                            v-if="
                                                file.type.startsWith('image/')
                                            "
                                            :src="createObjectURL(file)"
                                            alt="Preview"
                                            class="object-cover"
                                        />
                                        <v-icon v-else color="white">
                                            mdi-folder
                                        </v-icon>
                                    </v-avatar>
                                </template>

                                <template v-slot:append>
                                    <v-btn
                                        color="red-lighten-1"
                                        icon="mdi-close"
                                        variant="text"
                                        @click="removeFile(index)"
                                    />
                                </template>
                            </v-list-item>
                        </v-list>

                        <SvgSelectFiles v-else />
                    </div>

                    <div>
                        <v-card
                            class="bg-zinc-200 rounded-md h-fit !sticky top-[4.5rem]"
                            rounded="lg"
                            :elevation="4"
                            :loading="form.processing"
                        >
                            <div
                                class="w-80 flex flex-col gap-4 justify-end p-6"
                            >
                                <div>
                                    <v-input v-bind="errorAttributes('files')">
                                        <FileUploader
                                            v-model="form.files"
                                            :multiple="true"
                                        />
                                    </v-input>

                                    <!-- <div
                                        v-if="form.errors.files"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.files }}
                                    </div> -->
                                </div>

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

                                <v-btn
                                    :loading="form.processing"
                                    color="primary"
                                    class="text-none"
                                    type="submit"
                                >
                                    Загрузить
                                </v-btn>
                            </div>
                        </v-card>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
