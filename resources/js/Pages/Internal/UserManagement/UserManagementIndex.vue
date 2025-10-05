<script setup>
import { ref } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true
    }
});

const headers = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phone', label: 'Phone' },
    { key: 'created_at', label: 'Created' },
    { key: 'actions', label: 'Actions' }
];

const deleteModalOpen = ref(false);
const userToDelete = ref(null);

const confirmDelete = (user) => {
    userToDelete.value = user;
    deleteModalOpen.value = true;
};

const handleDelete = () => {
    router.delete(route('internal.user-management.destroy', userToDelete.value.id), {
        onSuccess: () => {
            deleteModalOpen.value = false;
            userToDelete.value = null;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="User Management" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Users</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage internal users</p>
                    </div>
                    <Link
                        :href="route('internal.user-management.create')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Add User
                    </Link>
                </div>

                <!-- Table -->
                <DataTable
                    :headers="headers"
                    :items="users.data"
                    class="mb-6"
                >
                    <template #default="{ item }">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ item.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ item.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ item.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ item.profile?.phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(item.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <Link
                                        :href="route('internal.user-management.show', item.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('internal.user-management.edit', item.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="confirmDelete(item)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </DataTable>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal v-model="deleteModalOpen">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Delete User
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete this user? This action cannot be undone.
                </p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="handleDelete"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </InternalLayout>
</template>
