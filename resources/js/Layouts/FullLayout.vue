<script setup>
import Footer from "@/Components/Layout/Footer.vue";
import Navbar from "@/Components/Layout/Navbar.vue";
</script>

<template>
    <v-app id="inspire" class="bg-transparent">
        <Navbar />

        <v-main>
            <v-container>
                <v-sheet min-height="70vh" rounded="lg" class="bg-transparent">
                    <slot />
                </v-sheet>
            </v-container>
        </v-main>

        <Footer />
    </v-app>
</template>
