™
<script setup>
import { ref, toRefs } from "vue";

const props = defineProps({
    items: Array,
    onLoad: Function,
    hasMore: Boolean,
});

const { items, onLoad, hasMore } = toRefs(props);

const loading = ref(false);

const load = () => {
    loading.value = true;

    props.onLoad(() => {
        loading.value = false;
    });
};

const reload = () => {
    load();
};

defineExpose({ load, reload });
</script>

<template>
    <div>
        <slot />

        <div class="flex items-center justify-center">
            <slot v-if="loading" name="loading">
                <v-progress-circular
                    indeterminate
                    :size="30"
                    :width="2"
                    color="primary"
                />
            </slot>

            <slot v-if="!loading && hasMore" name="load-more">
                <v-btn
                    variant="tonal"
                    class="text-none"
                    color="primary"
                    density="comfortable"
                    @click="load"
                >
                    Load More
                </v-btn>
            </slot>

            <slot v-if="!hasMore && !items.length" name="empty">
                <div>Ничего не найдено</div>
            </slot>
        </div>
    </div>
</template>
