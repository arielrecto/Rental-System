<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    links: {
        type: Array,
        default: () => []
    }
});

const isProfileMenuOpen = ref(false);
const activeDropdown = ref(null);

const toggleProfileMenu = () => {
    isProfileMenuOpen.value = !isProfileMenuOpen.value;
};

const toggleDropdown = (linkName) => {
    activeDropdown.value = activeDropdown.value === linkName ? null : linkName;
};
</script>

<template>
    <nav class="bg-white shadow-lg z-20">
        <!-- Upper Section - Profile -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Slot for additional options -->
                    <slot name="options"></slot>
                </div>

                <!-- Profile Section -->
                <div class="relative flex items-center">
                    <button
                        @click="toggleProfileMenu"
                        class="flex items-center space-x-3 focus:outline-none"
                    >
                        <img
                            :src="user.avatar || ''"
                            :alt="user.name"
                            class="h-8 w-8 rounded-full object-cover border-2 border-red-600"
                        >
                        <span class="text-gray-700 font-medium">{{ user.name }}</span>
                        <svg
                            class="w-4 h-4 text-gray-500"
                            :class="{ 'rotate-180': isProfileMenuOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div
                        v-show="isProfileMenuOpen"
                        class="absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                    >
                        <div class="py-1">
                            <Link
                                :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600"
                            >
                                Profile Settings
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lower Section - Navigation Links -->
        <div v-if="links.length" class="border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8 h-12">
                    <div
                        v-for="link in links"
                        :key="link.name"
                        class="relative"
                    >
                        <!-- Link with children -->
                        <button
                            v-if="link.children?.length"
                            @click="toggleDropdown(link.name)"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-red-600"
                        >
                            {{ link.name }}
                            <svg
                                class="ml-2 w-4 h-4"
                                :class="{ 'rotate-180': activeDropdown === link.name }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Link without children -->
                        <Link
                            v-else
                            :href="link.href"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-red-600"
                        >
                            {{ link.name }}
                        </Link>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="link.children?.length"
                            v-show="activeDropdown === link.name"
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                        >
                            <div class="py-1">
                                <Link
                                    v-for="child in link.children"
                                    :key="child.name"
                                    :href="child.href"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600"
                                >
                                    {{ child.name }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s;
}
</style>
