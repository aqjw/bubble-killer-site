<script setup>
import MenuProfile from "@/Components/Menu/MenuProfile.vue";
import useSession from "@/Composables/useSession";
import { storeToRefs, useUserStore } from "@/Stores";
import { Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useTheme } from "vuetify";

const theme = useTheme();
const themeMode = ref(useSession.get("theme", "light"));
const isDark = computed(() => themeMode.value === "dark");

const userStore = useUserStore();
const { isLogged, user } = storeToRefs(userStore);

function toggleTheme() {
    themeMode.value = isDark.value ? "light" : "dark";
    theme.global.name.value = themeMode.value;
    useSession.set("theme", themeMode.value);

    if (isDark.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
}
</script>

<template>
    <v-app-bar flat density="comfortable" class="!shadow-">
        <v-container>
            <v-row>
                <v-col cols="2" class="d-flex align-center relative">
                    <Link
                        :href="route('home')"
                        class="text-2xl font-semibold mr-10 text-zinc-800 dark:text-zinc-200 no-underline"
                    >
                        {{ $appName }}

                        <div class="absolute top-0 right-0">
                            <v-chip
                                density="comfortable"
                                size="small"
                                color="purple"
                                variant="elevated"
                            >
                                Beta
                            </v-chip>
                        </div>
                    </Link>
                </v-col>

                <v-col class="mx-auto d-flex align-center justify-center">
                    <v-spacer></v-spacer>

                    <div class="d-flex align-center gap-3">
                        <v-btn
                            density="comfortable"
                            :icon="
                                isDark
                                    ? 'mdi-weather-night'
                                    : 'mdi-white-balance-sunny'
                            "
                            variant="plain"
                            @click="toggleTheme"
                        />

                        <template v-if="isLogged">
                            <MenuProfile :user="user" />
                        </template>

                        <v-btn
                            v-if="!isLogged"
                            :to="route('login')"
                            variant="tonal"
                            class="text-none"
                            color="primary"
                            density="comfortable"
                        >
                            Login
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-app-bar>
</template>
