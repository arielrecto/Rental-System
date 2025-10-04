<script setup>
import { computed } from 'vue'; // 1. Import 'computed' from Vue

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    totalPages: {
        type: Number,
        required: true
    },
    itemsPerPage: {
        type: Number,
        required: true
    },
    totalItems: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['page-change']);

const changePage = (page) => {
    if (page > 0 && page <= props.totalPages) {
        emit('page-change', page);
    }
};

// 2. Add a computed property to generate the smart page list
const pages = computed(() => {
    const pagesArray = [];
    const range = 2; // How many pages to show on each side of the current page

    for (let i = 1; i <= props.totalPages; i++) {
        // Always show the first page, the last page, and pages within the range
        if (i === 1 || i === props.totalPages || (i >= props.currentPage - range && i <= props.currentPage + range)) {
            // Add ellipses if there is a gap
            if (pagesArray.length > 0 && i > pagesArray[pagesArray.length - 1] + 1) {
                pagesArray.push('...');
            }
            pagesArray.push(i);
        }
    }
    return pagesArray;
});
</script>

<template>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ ((currentPage - 1) * itemsPerPage) + 1 }}</span>
                    to
                    <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, totalItems) }}</span>
                    of
                    <span class="font-medium">{{ totalItems }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button
                        @click="changePage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                    >
                        Previous
                    </button>

                    <template v-for="(page, index) in pages" :key="index">
                        <button
                            v-if="typeof page === 'number'"
                            @click="changePage(page)"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                currentPage === page
                                    ? 'z-10 bg-red-50 border-red-500 text-red-600'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                        <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                            ...
                        </span>
                    </template>

                    <button
                        @click="changePage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                    >
                        Next
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>
