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
const previewModalState = ref(false);
const previewFile = ref(null);

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
    form.value.attachments = [...form.value.attachments, ...files];
};

const getFilePreviewUrl = (file) => {
    return URL.createObjectURL(file);
};

const isImageFile = (file) => {
    return file.type.startsWith('image/');
};

const isPdfFile = (file) => {
    return file.type === 'application/pdf';
};

const previewFileHandler = (file) => {
    previewFile.value = {
        name: file.name,
        type: file.type,
        url: getFilePreviewUrl(file)
    };
    previewModalState.value = true;
};

const removeFile = (index) => {
    form.value.attachments.splice(index, 1);
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

                <!-- vehicles Grid -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="vehicle in vehicles.data"
                        :key="vehicle.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300"
                    >


                        <img v-if="!vehicle.image"
                            src="https://i5.walmartimages.com/seo/Costway-12V-Kids-Ride-On-Motorcycle-Electric-Motor-Bike-w-Training-Wheels-Light-White_d435429e-f263-431b-8f32-1e2e578a3a14.f77ecd526dd5ddff468b78bfc5c3e41b.jpeg"
                            :alt="vehicle.brand + ' ' + vehicle.model"
                            class="w-full h-48 object-cover object-top"
                        />

                        <img v-else
                            :src="`${vehicle.image.file_path}`"
                            :alt="vehicle.brand + ' ' + vehicle.model"
                            class="w-full h-48 object-cover object-top"
                        />



                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">

                                <div>
                                    <h3
                                        class="text-xl font-semibold text-gray-800"
                                    >
                                        {{ vehicle.brand + " " + vehicle.model }}
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
                                    :class=" [
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

        <!-- Rental Modal -->
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

                        <!-- Valid IDs Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Valid IDs (Required)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-red-500 transition-colors">
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

                            <!-- File Preview Grid -->
                            <div v-if="form.attachments.length" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div
                                    v-for="(file, index) in form.attachments"
                                    :key="index"
                                    class="relative group bg-gray-50 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-red-500 transition-colors"
                                >
                                    <!-- Image Preview -->
                                    <div v-if="isImageFile(file)" class="aspect-square">
                                        <img
                                            :src="getFilePreviewUrl(file)"
                                            :alt="file.name"
                                            class="w-full h-full object-cover cursor-pointer"
                                            @click="previewFileHandler(file)"
                                        />
                                    </div>

                                    <!-- PDF Preview -->
                                    <div v-else-if="isPdfFile(file)"
                                         class="aspect-square flex flex-col items-center justify-center p-4 cursor-pointer"
                                         @click="previewFileHandler(file)">
                                        <svg class="h-16 w-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="mt-2 text-xs text-gray-600 text-center font-medium">PDF</p>
                                    </div>

                                    <!-- File name and actions -->
                                    <div class="p-2 bg-white">
                                        <p class="text-xs text-gray-600 truncate" :title="file.name">
                                            {{ file.name }}
                                        </p>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs text-gray-500">
                                                {{ (file.size / 1024).toFixed(1) }} KB
                                            </span>
                                            <div class="flex space-x-1">
                                                <button
                                                    type="button"
                                                    @click="previewFileHandler(file)"
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded"
                                                    title="Preview"
                                                >
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="removeFile(index)"
                                                    class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded"
                                                    title="Remove"
                                                >
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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

        <!-- File Preview Modal -->
        <Modal v-model="previewModalState" @close="previewModalState = false" size="4xl">
            <template #header>
                <h3 class="text-lg font-semibold text-gray-900">{{ previewFile?.name }}</h3>
            </template>

            <template #default>
                <div v-if="previewFile" class="bg-gray-900 rounded-lg overflow-hidden">
                    <!-- Image Preview -->
                    <div v-if="isImageFile({ type: previewFile.type })" class="flex justify-center items-center p-4">
                        <img
                            :src="previewFile.url"
                            :alt="previewFile.name"
                            class="max-w-full max-h-[70vh] object-contain"
                        />
                    </div>

                    <!-- PDF Preview -->
                    <div v-else-if="isPdfFile({ type: previewFile.type })" class="w-full" style="height: 70vh;">
                        <iframe
                            :src="previewFile.url"
                            class="w-full h-full border-0"
                            title="PDF Preview"
                        ></iframe>
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end">
                    <button
                        @click="previewModalState = false"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-300"
                    >
                        Close
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
