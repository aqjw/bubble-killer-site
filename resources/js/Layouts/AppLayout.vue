<script setup>
import Navbar from "@/Components/Layout/Navbar.vue";
import Sidebar from "@/Components/Layout/Sidebar.vue";
import Footer from "@/Components/Layout/Footer.vue";
import { storeToRefs, useUserStore } from "@/Stores";

const userStore = useUserStore();
const { isLogged } = storeToRefs(userStore);
</script>

<template>
    <v-app id="inspire" class="bg-transparent">
        <Navbar />

        <v-main>
            <v-container>
                <v-row>
                    <v-col v-if="isLogged" cols="2">
                        <div class="sticky top-[4.5rem]">
                            <Sidebar />
                        </div>
                    </v-col>

                    <v-col>
                        <v-sheet
                            min-height="70vh"
                            rounded="lg"
                            class="bg-transparent"
                        >
                            <slot />
                        </v-sheet>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <Footer />
    </v-app>
</template>
