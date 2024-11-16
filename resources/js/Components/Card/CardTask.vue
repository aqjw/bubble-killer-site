<script setup>
import DialogGallery from "@/Components/Dialogs/DialogGallery.vue";
import DateManager from "@/Plugins/DateManager";
import { ref } from "vue";

const dialogGallery = ref(null);

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <v-card class="bg-zinc-200 rounded-md" rounded="lg" :elevation="2">
        <div class="flex gap-4 p-4">
            <div class="w-1/3">
                <v-chip class="mb-3" size="small">ID: {{ task.id }}</v-chip>

                <v-table density="compact">
                    <tbody>
                        <tr>
                            <td class="opacity-70">Файл</td>
                            <td class="truncate max-w-12">
                                {{ task.original_filename }}
                                <v-chip
                                    v-if="task.type === 'multiple'"
                                    density="compact"
                                    color="primary"
                                    size="small"
                                    class="ml-2"
                                >
                                    {{ task.subtasks.length }}
                                </v-chip>
                            </td>
                        </tr>
                        <tr>
                            <td class="opacity-70">Загружено</td>
                            <td>
                                {{
                                    DateManager.toHuman(task.created_at, {
                                        parts: 1,
                                    })
                                }}
                                назад
                            </td>
                        </tr>
                        <tr>
                            <td class="opacity-70">Статус</td>
                            <td>{{ task.status }}</td>
                        </tr>
                        <tr>
                            <td class="opacity-70">Model</td>
                            <td>{{ task.cleaning_model }}</td>
                        </tr>
                        <tr>
                            <td class="opacity-70">Mask Time</td>
                            <td>
                                <span v-if="task.mask_time_seconds">
                                    {{ task.mask_time_seconds }} сек
                                </span>
                                <span v-else>–</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="opacity-70">Clean Time</td>
                            <td>
                                <span v-if="task.clean_time_seconds">
                                    {{ task.clean_time_seconds }} сек
                                </span>
                                <span v-else>–</span>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </div>

            <div class="w-2/3 flex justify-end">
                <div v-if="task.type === 'multiple'">
                    <v-list lines="one" max-height="15rem">
                        <v-list-item
                            v-for="(item, index) in task.subtasks"
                            :key="index"
                            :title="item.original_filename"
                            :prepend-avatar="item.files[0]"
                            class="roundless"
                            @click.stop.prevent="
                                $inertia.visit(route('task', item.id))
                            "
                        >
                            <v-chip class="mb-3" size="small" density="compact">
                                ID: {{ item.id }}
                            </v-chip>
                        </v-list-item>
                    </v-list>
                </div>

                <div
                    v-else
                    class="flex justify-center divide-x-2 divide-gray-400 border-2 border-gray-400 rounded-lg shadow-lg overflow-hidden"
                >
                    <template v-for="(file, index) in task.files" :key="index">
                        <img
                            v-if="file"
                            :src="file"
                            class="max-w-64 max-h-64 object-cover cursor-pointer"
                            @click="() => dialogGallery.open(index)"
                        />
                    </template>

                    <DialogGallery ref="dialogGallery" :images="task.files" />
                </div>
            </div>
        </div>
    </v-card>
</template>
