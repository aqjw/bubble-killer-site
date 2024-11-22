<script setup>
import { storeToRefs, useUserStore } from "@/Stores";
import { ref } from "vue";
import { router } from "@inertiajs/vue3"; // Для перенаправления на страницу входа

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    multiple: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const userStore = useUserStore();
const { isLogged } = storeToRefs(userStore);

const acceptedFormats = [".jpeg", ".jpg", ".png", ".zip"];
const isDragging = ref(false);

const addFiles = (fileList) => {
    const selectedFiles = Array.from(fileList);
    const validFiles = selectedFiles.filter((file) =>
        acceptedFormats.includes(file.name.slice(file.name.lastIndexOf(".")))
    );

    let newFiles = isLogged.value
        ? [...props.modelValue, ...validFiles] // Multiple files for logged users
        : validFiles.slice(0, 1); // Single file for guests

    emit("update:modelValue", newFiles);
};

const handleFileChange = (event) => {
    addFiles(event.target.files);
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    addFiles(event.dataTransfer.files);
};

const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

// Перенаправление на страницу входа
const redirectToLogin = () => {
    router.visit(route("login"));
};

// Обработка клика по ZIP
const handleZipClick = () => {
    if (!isLogged.value) {
        redirectToLogin();
    }
};
</script>

<template>
    <div>
        <!-- Пометочка о режиме загрузки -->
        <p v-if="!isLogged" class="text-sm text-gray-500 mb-4">
            <v-tooltip activator="parent" location="top">
                Only authorized users can upload Multiple files.
            </v-tooltip>
            <span class="text-red-600 font-semibold"> Single file only </span>
        </p>

        <p class="text-sm text-gray-500 flex gap-1 mb-2">
            Accepted formats:
            <span
                v-for="format in acceptedFormats"
                :key="format"
                class="px-1 py-0.5 rounded-md text-xs"
                :class="{
                    'bg-slate-200 text-slate-600':
                        isLogged || format !== '.zip',
                    'bg-red-200 text-red-600': !isLogged && format === '.zip',
                }"
            >
                <v-tooltip
                    v-if="!isLogged && format === '.zip'"
                    activator="parent"
                    location="top"
                >
                    Only authorized users can upload ZIP files.
                </v-tooltip>
                {{ format }}
            </span>
        </p>

        <!-- Форма загрузки -->
        <div
            class="relative p-4 border-2 border-dashed rounded-lg text-center cursor-pointer"
            :class="
                isDragging
                    ? 'border-blue-500 bg-blue-50'
                    : 'border-zinc-300 dark:border-zinc-500 bg-main'
            "
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
            @click="!isLogged && redirectToLogin"
        >
            <input
                id="fileInput"
                type="file"
                :multiple="isLogged"
                @change="handleFileChange"
                accept=".jpeg,.jpg,.png,.zip"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />

            <div class="flex flex-col items-center justify-center">
                <v-icon icon="mdi-plus" size="40" class="text-gray-500 mb-2" />
                <p class="text-sm text-gray-500">
                    Drag & Drop files here or click to browse
                </p>
            </div>
        </div>
    </div>
</template>
