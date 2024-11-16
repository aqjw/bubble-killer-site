<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import PartDestroyAccount from "./Parts/PartDestroyAccount.vue";
import PartUpdateInformation from "./Parts/PartUpdateInformation.vue";
import PartUpdatePassword from "./Parts/PartUpdatePassword.vue";

const props = defineProps({
    user: Object,
});

const tab = ref("information");
</script>

<template>
    <Head title="Profile Edit" />

    <AppLayout>
        <div class="w-full bg-second rounded-lg shadow-lg overflow-hidden">
            <v-tabs v-model="tab" density="compact" slider-color="primary">
                <v-tab
                    value="information"
                    prepend-icon="mdi-information-box-outline"
                    base-color="default"
                    class="text-none"
                >
                    User Information
                </v-tab>
                <v-tab
                    value="password"
                    prepend-icon="mdi-lock-outline"
                    base-color="default"
                    class="text-none"
                >
                    Change Password
                </v-tab>
                <v-tab
                    value="delete"
                    prepend-icon="mdi-close-octagon-outline"
                    base-color="red"
                    slider-color="red"
                    class="text-none"
                >
                    Destroy Account
                </v-tab>
            </v-tabs>

            <v-card-text class="!p-0 mt-2">
                <v-tabs-window v-model="tab">
                    <v-tabs-window-item value="information" class="p-4">
                        <PartUpdateInformation :user="user" />
                    </v-tabs-window-item>

                    <v-tabs-window-item value="password" class="p-4">
                        <PartUpdatePassword />
                    </v-tabs-window-item>

                    <v-tabs-window-item value="delete" class="p-4">
                        <PartDestroyAccount />
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-card-text>
        </div>
    </AppLayout>
</template>
