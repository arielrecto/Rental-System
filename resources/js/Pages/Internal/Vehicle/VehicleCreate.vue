<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';

const form = ref({
    brand: '',
    plate_no: '',
    country_of_origin: '',
    manufacturer: '',
    model: '',
    color: '',
    type: '',
    year: new Date().getFullYear(),
    mileage: '',
    condition: 'New',
    rental_rate: '',
    status: 'Available',
    image: null
});

const imagePreview = ref(null);
const errors = ref({});

const vehicleTypes = [
    'Sport Bike',
    'Cruiser',
    'Touring',
    'Standard',
    'Dual Sport',
    'Scooter'
];

const conditions = ['New', 'Used', 'Excellent', 'Good', 'Fair'];
const statuses = ['Available', 'Maintenance', 'Reserved', 'Rented'];

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.value.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    router.post(route('internal.vehicles.store'), {
        ...form.value,
        _method: 'POST'
    }, {
        forceFormData: true,
        onSuccess: () => {
            form.value = {
                brand: '',
                plate_no: '',
                country_of_origin: '',
                manufacturer: '',
                model: '',
                color: '',
                type: '',
                year: new Date().getFullYear(),
                mileage: '',
                condition: 'New',
                rental_rate: '',
                status: 'Available',
                image: null
            };
            imagePreview.value = null;
        },
        onError: (e) => {
            errors.value = e;
        }
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Add Vehicle" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Add New Vehicle</h1>
                    <p class="mt-1 text-sm text-gray-600">Create a new vehicle in the system</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Image Upload -->
                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Vehicle Image
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div v-if="imagePreview" class="relative w-40 h-40">
                                            <img :src="imagePreview" class="object-cover rounded-lg w-full h-full" />
                                            <button
                                                type="button"
                                                @click="imagePreview = null; form.image = null"
                                                class="absolute top-0 right-0 p-1 bg-red-600 text-white rounded-full -mt-2 -mr-2"
                                            >
                                                <span class="sr-only">Remove</span>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-else class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                            <label class="cursor-pointer">
                                                <span class="flex items-center justify-center">
                                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </span>
                                                <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p>Upload a high-quality image of the vehicle</p>
                                        <p class="mt-1">JPG, PNG or GIF up to 10MB</p>
                                    </div>
                                </div>
                                <div v-if="errors.image" class="mt-1 text-sm text-red-600">
                                    {{ errors.image }}
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Brand</label>
                                    <input
                                        type="text"
                                        v-model="form.brand"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.brand" class="mt-1 text-sm text-red-600">
                                        {{ errors.brand }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Model</label>
                                    <input
                                        type="text"
                                        v-model="form.model"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.model" class="mt-1 text-sm text-red-600">
                                        {{ errors.model }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plate Number</label>
                                    <input
                                        type="text"
                                        v-model="form.plate_no"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <div v-if="errors.plate_no" class="mt-1 text-sm text-red-600">
                                        {{ errors.plate_no }}
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Type</label>
                                    <select
                                        v-model="form.type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="">Select Type</option>
                                        <option v-for="type in vehicleTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <div v-if="errors.type" class="mt-1 text-sm text-red-600">
                                        {{ errors.type }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Year</label>
                                        <input
                                            type="number"
                                            v-model="form.year"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Mileage</label>
                                        <input
                                            type="number"
                                            v-model="form.mileage"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Daily Rental Rate</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₱</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="form.rental_rate"
                                            class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        >
                                    </div>
                                </div>
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
                                Create Vehicle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
