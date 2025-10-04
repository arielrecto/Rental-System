<script setup>
import { Link } from '@inertiajs/vue3';
</script>





<template>

    <Link :href="route('logout')" method="post" >Logout</Link>
    <h1>Internal Dashboard</h1>
</template>
