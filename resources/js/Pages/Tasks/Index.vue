<script setup>
import CardTask from "@/Components/Card/CardTask.vue";
import InfiniteScroll from "@/Components/InfiniteScroll.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { storeToRefs, useTaskStore } from "@/Stores";
import { Head } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref } from "vue";
import { useToast } from "vue-toast-notification";

const props = defineProps({
    //
});

const $toast = useToast();

const taskStore = useTaskStore();
const { items, has_more, total } = storeToRefs(taskStore);

//
const infiniteScroll = ref(null);
const loading = ref(false);

onMounted(() => {
    nextTick(() => {
        infiniteScroll.value?.load();
    });
});

const onLoad = (finish) => {
    loading.value = true;
    taskStore.$loadTasks({
        success: () => {},
        error: () => {},
        finish: () => {
            loading.value = false;
            finish();
        },
    });
};

onBeforeUnmount(() => {
    taskStore.$resetAll();
});
</script>

<template>
    <Head title="History" />

    <AppLayout>
        <InfiniteScroll
            ref="infiniteScroll"
            :items="items"
            :has-more="has_more"
            :on-load="onLoad"
        >
            <div class="space-y-4 mb-4">
                <CardTask
                    v-for="(task, index) in items"
                    :key="index"
                    :task="task"
                    @click.stop.prevent="
                        $inertia.visit(route('tasks.show', task.id))
                    "
                />
            </div>
        </InfiniteScroll>
    </AppLayout>
</template>
