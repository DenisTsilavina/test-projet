<template>
    <div class="min-h-screen">
        <NavBar />

        <main>
            <router-view></router-view>
        </main>
    </div>
</template>

<script setup>
import NavBar from './components/NavBar.vue'
</script>
