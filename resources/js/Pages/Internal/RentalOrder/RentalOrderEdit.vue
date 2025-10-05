<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    rentalOrder: {
        type: Object,
        required: true
    },
    customers: {
        type: Array,
        required: true
    },
    vehicles: {
        type: Array,
        required: true
    }
});

const form = ref({
    user_id: props.rentalOrder.user_id,
    vehicle_id: props.rentalOrder.vehicle_id,
    start_date: props.rentalOrder.start_date,
    end_date: props.rentalOrder.end_date,
    notes: props.rentalOrder.notes,
    status: props.rentalOrder.status
});

const errors = ref({});
const selectedVehicle = ref(props.vehicles.find(v => v.id === props.rentalOrder.vehicle_id));

const statuses = ['pending', 'active', 'completed', 'cancelled'];

const handleVehicleChange = () => {
    selectedVehicle.value = props.vehicles.find(v => v.id === form.value.vehicle_id);
};

const submit = () => {
    router.put(route('internal.rental-orders.update', props.rentalOrder.id), form.value, {
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Edit Rental Order" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Rental Order #{{ rentalOrder.id }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Modify rental order details</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Reuse the same form layout from RentalOrderCreate.vue -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Customer</label>
                                <select
                                    v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }} ({{ customer.email }})
                                    </option>
                                </select>
                                <div v-if="errors.user_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.user_id }}
                                </div>
                            </div>

                            <!-- Vehicle Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Vehicle</label>
                                <select
                                    v-model="form.vehicle_id"
                                    @change="handleVehicleChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                        {{ vehicle.name }} ({{ vehicle.plate_no }})
                                    </option>
                                </select>
                                <div v-if="errors.vehicle_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.vehicle_id }}
                                </div>
                            </div>

                            <!-- Selected Vehicle Details -->
                            <div v-if="selectedVehicle" class="md:col-span-2 p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold text-gray-800">Selected Vehicle Details</h3>
                                <p class="text-red-600 font-semibold mt-1">₱{{ selectedVehicle.rental_rate.toLocaleString() }}/day</p>
                            </div>

                            <!-- Dates -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input
                                    type="date"
                                    v-model="form.start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                <div v-if="errors.start_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.start_date }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input
                                    type="date"
                                    v-model="form.end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    :min="form.start_date"
                                >
                                <div v-if="errors.end_date" class="mt-1 text-sm text-red-600">
                                    {{ errors.end_date }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option v-for="status in statuses" :key="status" :value="status">
                                        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Any special notes or requirements?"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="$router.go(-1)"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
