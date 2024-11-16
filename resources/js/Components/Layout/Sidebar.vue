<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const onAction = (e, item) => {
    e.preventDefault();
    router.visit(item.href);
};

const items = ref([
    {
        label: "Загрузить",
        href: route("home"),
        icon: "mdi-upload-circle-outline",
        isActive: () => route().current("home"),
        action: (e, item) => onAction(e, item),
    },
    {
        label: "История",
        href: route("tasks.index"),
        icon: "mdi-history",
        isActive: () => route().current("tasks.index"),
        action: (e, item) => onAction(e, item),
    },
]);
</script>

<template>
    <v-list rounded="lg" class="!shadow-md spacerless">
        <template v-for="(item, index) in items" :key="index">
            <v-list-item
                :prepend-icon="item.icon"
                :title="item.label"
                :href="item.href"
                :active="item.isActive()"
                color="primary"
                @click="(e) => item.action(e, item)"
            />
        </template>
    </v-list>
</template>
