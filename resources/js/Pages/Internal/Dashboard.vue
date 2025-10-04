<script setup>
// 1. Import all the necessary SVG icons
import {
    HomeIcon,
    TruckIcon,
    ShoppingBagIcon,
    UsersIcon,
    DocumentChartBarIcon,
    WrenchScrewdriverIcon,
} from "@heroicons/vue/24/outline";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import { Link } from "@inertiajs/vue3";

// 2. Define your navigation data directly in the page
const navigation = [
    {
        name: "Dashboard",
        icon: HomeIcon,
        href: route('internal.dashboard'), // Example property
        isActive: false, // Example property
        children: [],
    },
    {
        name: "Vehicle Management",
        icon: TruckIcon,
        href: route('internal.vehicles.index'), // Example property
        isActive: false,
        children: [
            /* ... */
        ],
    },
    {
        name: "Rental Orders",
        icon: ShoppingBagIcon,
        href: "#",
        isActive: false,
        children: [
            /* ... */
        ],
    },
    {
        name: "User Management",
        icon: UsersIcon,
        href: "#",
        isActive: false,
        children: [
            /* ... */
        ],
    },
    {
        name: "Reports",
        icon: DocumentChartBarIcon,
        href: "#",
        isActive: false,
        children: [
            /* ... */
        ],
    },
    {
        name: "Maintenance",
        icon: WrenchScrewdriverIcon,
        href: "#",
        isActive: false,
        children: [
            /* ... */
        ],
    },
    // ... add more top-level apps as needed
];
</script>

<template>
    <InternalLayout>
        <div
            class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-8 gap-5"
        >
            <Link
                v-for="app in navigation"
                :key="app.name"
                :href="app.href"
                class="flex flex-col items-center justify-center p-4 space-y-2 bg-white/50 backdrop-blur-sm border border-gray-200/50 rounded-xl shadow-sm transition-all duration-300 ease-in-out hover:shadow-lg hover:scale-105 hover:border-gray-300"
            >
                <component
                    v-if="app.icon"
                    :is="app.icon"
                    class="w-10 h-10 text-gray-700"
                    aria-hidden="true"
                />
                <span class="text-xs font-semibold text-gray-800 text-center">
                    {{ app.name }}
                </span>
            </Link>
        </div>
    </InternalLayout>
</template>
