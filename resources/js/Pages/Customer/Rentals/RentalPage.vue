<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    vehicles: {
        type: Array,
        required: true,
    },
});

const rentModalState = ref(false);

const page = usePage();
const isAuthenticated = ref(page.props.auth.user !== null);

const changePage = (page) => {
    router.visit(route("customer.rentals.index", { page: page }));
    console.log("Page changed to:", page);
};

const form = ref({
    vehicle_id: null,
    start_date: '',
    end_date: '',
    notes: '',
    attachments: [] // Add this for file attachments
});

const selectedVehicle = ref(null);
const formErrors = ref({});

const handleRentAction = (vehicle) => {
    selectedVehicle.value = vehicle;
    form.value.vehicle_id = vehicle.id;
    rentModalState.value = true;
};

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    form.value.attachments = files;
};

const submitRentalOrder = () => {
    const formData = new FormData();
    formData.append('vehicle_id', form.value.vehicle_id);
    formData.append('start_date', form.value.start_date);
    formData.append('end_date', form.value.end_date);
    formData.append('notes', form.value.notes);

    // Append each file to FormData
    form.value.attachments.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file);
    });

    router.post(route('customer.rentals.store'), formData, {
        onSuccess: () => {
            rentModalState.value = false;
            form.value = {
                vehicle_id: null,
                start_date: '',
                end_date: '',
                notes: '',
                attachments: []
            };
        },
        onError: (errors) => {
            formErrors.value = errors;
        }
    });
};

const calculateTotalAmount = computed(() => {
    if (!form.value.start_date || !form.value.end_date || !selectedVehicle.value) {
        return 0;
    }

    const start = new Date(form.value.start_date);
    const end = new Date(form.value.end_date);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;



    return days * selectedVehicle.value.rental_rate;
});
</script>

<template>
    <CustomerLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-800">
                        Available vehicles
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Choose your perfect ride from our premium collection
                    </p>
                </div>

                <!-- Category Filter -->
                <!-- <div class="flex justify-center mb-8 space-x-4">
                    <button
                        v-for="category in categories"
                        :key="category"
                        @click="selectedCategory = category"
                        :class="[
                            'px-4 py-2 rounded-lg transition duration-300',
                            selectedCategory === category
                                ? 'bg-red-600 text-white'
                                : 'bg-white text-gray-700 hover:bg-red-50'
                        ]"
                    >
                        {{ category }}
                    </button>
                </div> -->

                <!-- vehicles Grid -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="vehicle in vehicles.data"
                        :key="vehicle.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300"
                    >
                        <img
                            src="https://i5.walmartimages.com/seo/Costway-12V-Kids-Ride-On-Motorcycle-Electric-Motor-Bike-w-Training-Wheels-Light-White_d435429e-f263-431b-8f32-1e2e578a3a14.f77ecd526dd5ddff468b78bfc5c3e41b.jpeg"
                            :alt="vehicle.brand + ' ' + vehicle.model"
                            class="w-full h-48 object-cover object-top"
                        />
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3
                                        class="text-xl font-semibold text-gray-800"
                                    >
                                        {{
                                            vehicle.brand + " " + vehicle.model
                                        }}
                                    </h3>
                                    <p class="text-gray-600">
                                        {{ vehicle.type }} | {{ vehicle.cc }}
                                    </p>
                                </div>
                                <span class="text-red-600 font-bold"
                                    >₱{{ vehicle.rental_rate.toLocaleString() }}/day</span
                                >
                            </div>
                            <p class="text-gray-600 mb-4">
                                {{ vehicle.description }}
                            </p>
                            <div class="flex justify-between items-center">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm',
                                        vehicle.status === 'Available'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ vehicle.status }}
                                </span>
                                <button
                                    v-if="isAuthenticated"
                                    @click="handleRentAction(vehicle)"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-300"
                                    :disabled="vehicle.status !== 'Available'"
                                >
                                    Rent Now
                                </button>
                                <Link
                                    v-else
                                    :href="route('login')"
                                    class="text-red-600 hover:text-red-700 font-semibold"
                                >
                                    Login to Rent
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <Pagination
                    @page-change="changePage"
                    :currentPage="vehicles.current_page"
                    :totalPages="vehicles.to"
                    :itemsPerPage="vehicles.per_page"
                    :totalItems="vehicles.total"
                />
            </div>
        </div>

        <Modal :modelValue="rentModalState" @close="rentModalState = false" size="xl">
            <template #header>
                <h2 class="text-xl font-bold text-gray-800">Rent Vehicle</h2>
            </template>

            <template #default>
                <form @submit.prevent="submitRentalOrder" class="space-y-4">
                    <div v-if="selectedVehicle" class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800">
                            {{ selectedVehicle.brand }} {{ selectedVehicle.model }}
                        </h3>
                        <p class="text-red-600 font-semibold">₱{{ selectedVehicle.rental_rate.toLocaleString() }}/day</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date
                            </label>
                            <input
                                type="date"
                                v-model="form.start_date"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                :min="new Date().toISOString().split('T')[0]"
                                required
                            >
                            <div v-if="formErrors.start_date" class="text-red-500 text-sm mt-1">
                                {{ formErrors.start_date }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End Date
                            </label>
                            <input
                                type="date"
                                v-model="form.end_date"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                :min="form.start_date"
                                required
                            >
                            <div v-if="formErrors.end_date" class="text-red-500 text-sm mt-1">
                                {{ formErrors.end_date }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Any special requests or notes?"
                            ></textarea>
                        </div>

                        <!-- Add this before the Total Amount section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Valid IDs (Required)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg
                                        class="mx-auto h-12 w-12 text-gray-400"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 48 48"
                                    >
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            for="file-upload"
                                            class="relative cursor-pointer rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500"
                                        >
                                            <span>Upload files</span>
                                            <input
                                                id="file-upload"
                                                type="file"
                                                multiple
                                                class="sr-only"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                @change="handleFileUpload"
                                            >
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, PDF up to 10MB each
                                    </p>
                                </div>
                            </div>
                            <!-- File Preview -->
                            <div v-if="form.attachments.length" class="mt-2 space-y-2">
                                <div
                                    v-for="(file, index) in form.attachments"
                                    :key="index"
                                    class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                                >
                                    <span class="text-sm text-gray-600">{{ file.name }}</span>
                                    <button
                                        type="button"
                                        @click="form.attachments.splice(index, 1)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <span class="sr-only">Remove</span>
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div v-if="formErrors.attachments" class="text-red-500 text-sm mt-1">
                                {{ formErrors.attachments }}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-700">Total Amount:</span>
                                <span class="text-lg font-bold text-red-600">
                                    ₱{{ calculateTotalAmount.toLocaleString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="rentModalState = false"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-300"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitRentalOrder"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300"
                    >
                        Confirm Rental
                    </button>
                </div>
            </template>
        </Modal>
    </CustomerLayout>
</template>

<style scoped>
.grid {
    @apply gap-6;
}

.vehicle-card {
    @apply transition-all duration-300 ease-in-out;
}

.vehicle-card:hover {
    @apply transform scale-105;
}
</style>
