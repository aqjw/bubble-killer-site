<script setup>
import CardTask from "@/Components/Card/CardTask.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import DateManager from "@/Plugins/DateManager";
import { Head, router } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    console.log("props.task.status", props.task.status);
    if (props.task.status != "cleaner_completed") {
        setTimeout(() => {
            router.visit(route("tasks.show", props.task.id));
        }, 10000);
    }
});
</script>

<template>
    <Head title="Upload" />

    <AppLayout>
        <div v-if="task.type === 'multiple'">
            <v-card
                class="bg-zinc-200 rounded-md mb-8"
                rounded="lg"
                :elevation="2"
            >
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-3xl font-bold">
                        {{ task.original_filename }}
                    </h1>

                    <div class="space-y-2">
                        <v-chip size="small">ID: {{ task.id }}</v-chip>
                        <div class="text-xs opacity-70 italic">
                            Загружено
                            {{
                                DateManager.toHuman(task.created_at, {
                                    parts: 1,
                                })
                            }}
                            назад
                        </div>
                        <div class="text-xs opacity-70 italic">
                            Всего файлов в архиве {{ task.subtasks.length }}
                        </div>
                    </div>
                </div>
            </v-card>

            <div
                v-if="!task.subtasks.length"
                class="text-sm font-bold text-gray-700 mb-4"
            >
                processing..
            </div>

            <div class="space-y-4">
                <CardTask
                    v-for="item in task.subtasks"
                    :key="item.id"
                    :task="item"
                    @click.stop.prevent="
                        $inertia.visit(route('tasks.show', item.id))
                    "
                />
            </div>
        </div>

        <div v-else>
            <v-btn
                v-if="task.parent_id"
                :to="route('tasks.show', task.parent_id)"
                variant="tonal"
                class="text-none mb-4"
                color="primary"
                density="comfortable"
            >
                Назад
            </v-btn>

            <CardTask :task="task" />
        </div>
    </AppLayout>
</template>
