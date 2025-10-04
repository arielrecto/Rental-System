<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    navigation: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const expandedItems = ref(new Set());

const toggleItem = (itemName) => {
    if (expandedItems.value.has(itemName)) {
        expandedItems.value.delete(itemName);
    } else {
        expandedItems.value.add(itemName);
    }
};
</script>

<template>
    <div class="w-64 bg-white shadow-lg">
        <!-- Logo Section -->
        <div class="flex items-center h-16 px-6 border-b border-gray-200">
            <span class="text-2xl font-bold text-red-600">Anaia's</span>
        </div>

        <!-- Navigation Menu -->
        <div class="px-3 py-4 overflow-y-auto">
            <div v-for="item in navigation" :key="item.name" class="mb-2">
                <!-- Parent Menu Item -->
                <button
                    v-if="item.children?.length > 0"
                    @click="toggleItem(item.name)"
                    class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium transition-colors rounded-lg group"
                    :class="{
                        'bg-red-50 text-red-600': item.isActive,
                        'text-gray-700 hover:bg-gray-50': !item.isActive,
                    }"
                >
                    <div class="flex items-center">
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="w-5 h-5 me-3"
                        />
                        <span>{{ item.name }}</span>
                    </div>

                    <svg
                        v-if="item.children?.length"
                        class="w-4 h-4 transition-transform duration-200"
                        :class="{ 'rotate-180': expandedItems.has(item.name) }"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>

                <Link
                    v-else
                    :href="item.href"
                    class="flex items-center w-full px-4 py-2.5 text-sm font-medium transition-colors rounded-lg"
                    :class="{
                        'bg-red-50 text-red-600': item.isActive,
                        'text-gray-700 hover:bg-gray-50': !item.isActive,
                    }"
                >
                    <div class="flex items-center">
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="w-5 h-5 me-3"
                        />
                        <span>{{ item.name }}</span>
                    </div>
                </Link>

                <!-- Submenu Items -->
                <div
                    v-if="item.children?.length"
                    v-show="expandedItems.has(item.name)"
                    class="mt-1 ml-4 space-y-1"
                >
                    <Link
                        v-for="child in item.children"
                        :key="child.name"
                        :href="child.href"
                        class="block px-4 py-2 text-sm transition-colors rounded-lg"
                        :class="{
                            'bg-red-50 text-red-600': child.isActive,
                            'text-gray-600 hover:text-red-600 hover:bg-gray-50':
                                !child.isActive,
                        }"
                    >
                        {{ child.name }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
