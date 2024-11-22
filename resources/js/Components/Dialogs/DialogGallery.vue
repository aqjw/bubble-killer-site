<script setup>
import { ref } from "vue";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide, Navigation } from "vue3-carousel";

defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const dialog = ref(false);
const index = ref(0);

const open = (_index) => {
    index.value = _index;
    dialog.value = true;
};

defineExpose({ open });

// TODO
</script>

<template>
    <v-dialog v-model="dialog" width="60%" :opacity="0.6">
        <carousel :model-value="index">
            <slide v-for="(image, _index) in images" :key="_index">
                <img
                    :src="image"
                    cover
                    class="rounded-md max-w-[80vw] mx-auto max-h-[80vh] object-cover"
                />
            </slide>

            <template #addons>
                <navigation v-if="images.length > 1" />
            </template>
        </carousel>
    </v-dialog>
</template>
