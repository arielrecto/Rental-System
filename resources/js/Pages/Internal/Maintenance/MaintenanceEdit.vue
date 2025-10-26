<script setup>
import { ref } from 'vue';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    request: Object,
    vehicles: Array
});

const form = ref({
    vehicle_id: props.request.vehicle_id,
    description: props.request.description,
    requested_date: props.request.requested_date,
    maintainance_date: props.request.maintainance_date,
    cost: props.request.cost,
    is_completed: props.request.is_completed
});

const errors = ref({});

const submit = () => {
    router.put(route('internal.maintenance.update', props.request.id), form.value, {
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Edit Maintenance Request" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Maintenance Request</h1>
                    <p class="mt-1 text-sm text-gray-600">Edit maintenance request details</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Vehicle Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Vehicle
                            </label>
                            <select
                                v-model="form.vehicle_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option v-for="vehicle in vehicles"
                                    :key="vehicle.id"
                                    :value="vehicle.id"
                                >
                                    {{ vehicle.brand }} {{ vehicle.model }} ({{ vehicle.plate_no }})
                                </option>
                            </select>
                            <p v-if="errors.vehicle_id" class="mt-1 text-sm text-red-600">
                                {{ errors.vehicle_id }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            ></textarea>
                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                                {{ errors.description }}
                            </p>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Requested Date
                                </label>
                                <input
                                    type="date"
                                    v-model="form.requested_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                <p v-if="errors.requested_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.requested_date }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Maintenance Date
                                </label>
                                <input
                                    type="date"
                                    v-model="form.maintainance_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                <p v-if="errors.maintainance_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.maintainance_date }}
                                </p>
                            </div>
                        </div>

                        <!-- Cost -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Cost
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input
                                    type="number"
                                    v-model="form.cost"
                                    step="0.01"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="0.00"
                                >
                            </div>
                            <p v-if="errors.cost" class="mt-1 text-sm text-red-600">
                                {{ errors.cost }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_completed"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                >
                                <label class="ml-2 block text-sm text-gray-900">
                                    Mark as completed
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <Link
                                :href="route('internal.maintenance.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                            >
                                Update Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
