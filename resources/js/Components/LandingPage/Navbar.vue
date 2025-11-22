<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

const isOpen = ref(false);

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const { auth } = usePage().props;

console.log(auth);
const user = auth.user;
</script>

<template>
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-red-600 text-2xl font-bold"
                            >Anaia's</span
                        >
                    </div>

                </div>


                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a
                        href="#home"
                        class="text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                        >Home</a
                    >
                    <a
                        href="#motorcycles"
                        class="text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                        >Motorcycles</a
                    >
                    <a
                        href="#services"
                        class="text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                        >Services</a
                    >
                    <a
                        href="#about"
                        class="text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                        >About</a
                    >
                    <Link
                        v-if="!user"
                        :href="route('login')"
                        class="text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                        >Login</Link
                    >
                    <Link
                        v-if="!user"
                        :href="route('register')"
                        class="border-2 border-red-600 text-red-600 px-4 py-2 rounded-md hover:bg-red-600 hover:text-white transition duration-300"
                        >Register</Link
                    >
                    <Link
                        v-if="user"
                        :href="route('home')"
                        class="border-2 border-red-600 text-red-600 px-4 py-2 rounded-md hover:bg-red-600 hover:text-white transition duration-300"
                        >Dashboard</Link
                    >

                    <a
                        href="#contact"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300"
                        >Contact Us</a
                    >
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button
                        @click="toggleMenu"
                        class="text-gray-700 hover:text-red-600"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                v-if="!isOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->;
        <div v-show="isOpen" class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-2 sm:px-3">
                <a
                    href="#home"
                    class="block text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                    >Home</a
                >
                <a
                    href="#motorcycles"
                    class="block text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                    >Motorcycles</a
                >
                <a
                    href="#services"
                    class="block text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                    >Services</a
                >
                <a
                    href="#about"
                    class="block text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                    >About</a
                >
                <Link
                    v-if="!user"
                    :href="route('login')"
                    class="block text-gray-700 hover:text-red-600 px-3 py-2 font-medium"
                    >Login</Link
                >
                <Link
                    v-if="!user"
                    :href="route('register')"
                    class="block border-2 border-red-600 text-red-600 px-4 py-2 rounded-md hover:bg-red-600 hover:text-white transition duration-300 mx-3 text-center"
                    >Register</Link
                >
                <Link
                    v-if="user"
                    :href="route('home')"
                    class="block border-2 border-red-600 text-red-600 px-4 py-2 rounded-md hover:bg-red-600 hover:text-white transition duration-300 mx-3 text-center"
                    >Dashboard</Link
                >
                <a
                    href="#contact"
                    class="block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300 mx-3 text-center"
                    >Contact Us</a
                >
            </div>
        </div>
    </nav>
</template>
