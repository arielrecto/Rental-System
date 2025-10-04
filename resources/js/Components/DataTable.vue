<script setup>
import { computed, ref } from "vue";
import Pagination from "./Pagination.vue";

const props = defineProps({
    headers: {
        type: Array,
        required: true,
    },
    items: {
        type: Array,
        default: () => [],
    },
    itemsPerPage: {
        type: Number,
        default: 10,
    },
    searchable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["sort"]);

const currentPage = ref(1);
const searchQuery = ref("");
const sortBy = ref("");
const sortDesc = ref(false);

const filteredItems = computed(() => {
    let filtered = [...props.items];

    if (searchQuery.value && props.searchable) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter((item) =>
            Object.values(item).some((val) =>
                String(val).toLowerCase().includes(query)
            )
        );
    }

    if (sortBy.value) {
        filtered.sort((a, b) => {
            const modifier = sortDesc.value ? -1 : 1;
            return a[sortBy.value] > b[sortBy.value] ? modifier : -modifier;
        });
    }

    return filtered;
});

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * props.itemsPerPage;
    const end = start + props.itemsPerPage;
    return filteredItems.value.slice(start, end);
});

const totalPages = computed(() =>
    Math.ceil(filteredItems.value.length / props.itemsPerPage)
);

const handleSort = (header) => {
    if (sortBy.value === header.key) {
        sortDesc.value = !sortDesc.value;
    } else {
        sortBy.value = header.key;
        sortDesc.value = false;
    }
    emit("sort", { key: header.key, desc: sortDesc.value });
};

const handlePageChange = (page) => {
    currentPage.value = page;
};
</script>

<template>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Search Bar -->
        <div v-if="searchable" class="p-4 border-b border-gray-200">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="header in headers"
                            :key="header.key"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-red-600"
                            @click="handleSort(header)"
                        >
                            <div class="flex items-center space-x-1">
                                <span>{{ header.label }}</span>
                                <span v-if="sortBy === header.key" class="ml-1">
                                    {{ sortDesc ? "↓" : "↑" }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Default slot for custom row rendering -->
                    <slot
                        v-for="item in paginatedItems"
                        :key="item.id"
                        :item="item"
                    >
                        <tr class="hover:bg-gray-50">
                            <td
                                v-for="header in headers"
                                :key="header.key"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ item[header.key] }}
                            </td>
                        </tr>
                    </slot>



                    <!-- Empty state -->
                    <tr v-if="paginatedItems.length === 0">
                        <td
                            :colspan="headers.length"
                            class="px-6 py-4 text-center text-gray-500"
                        >
                            No results found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Component -->
        <Pagination
            :current-page="currentPage"
            :total-pages="totalPages"
            :items-per-page="itemsPerPage"
            :total-items="filteredItems.length"
            @page-change="handlePageChange"
        />
    </div>
</template>
