<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    UserIcon,
    TruckIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    DeviceTabletIcon,
    PhoneIcon,
    IdentificationIcon,
    ShieldCheckIcon,
    PaperClipIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    rentalOrder: {
        type: Object,
        required: true,
    },
});

const getStatusColor = (status) => {
    const colors = {
        pending: "bg-yellow-100 text-yellow-800",
        active: "bg-green-100 text-green-800",
        completed: "bg-blue-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};

const calculateDuration = () => {
    const start = new Date(props.rentalOrder.rental_date);
    const end = new Date(props.rentalOrder.return_date);
    return Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
};

const profile = computed(() => props.rentalOrder.user?.profile ?? null);

// Documents uploaded directly on this rental order
const rentalAttachments = computed(() => props.rentalOrder.attachments ?? []);

// Profile-level documents (valid ID, driver's license)
const profileValidIds = computed(() =>
    (profile.value?.attachments ?? []).filter(a => a.category === 'valid_id')
);
const profileLicenseScans = computed(() =>
    (profile.value?.attachments ?? []).filter(a => a.category === 'drivers_license')
);

// All docs available for this rental (rental uploads take priority)
const rentalValidIds = computed(() =>
    rentalAttachments.value.filter(a => a.category === 'valid_id')
);
const rentalLicenseScans = computed(() =>
    rentalAttachments.value.filter(a => a.category === 'drivers_license')
);
const rentalOtherDocs = computed(() =>
    rentalAttachments.value.filter(a => !['valid_id','drivers_license'].includes(a.category))
);

const isLicenseVerified = computed(() => profile.value?.drivers_license_verified ?? false);

const verifyLicense = () => {
    router.patch(route('internal.user-management.verify-license', props.rentalOrder.user.id), {}, {
        preserveScroll: true,
    });
};

const formatDate = (d) => d
    ? new Date(d).toLocaleString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true })
    : '—';
const isImage = (ext) => ['jpg','jpeg','png','gif','webp'].includes((ext ?? '').toLowerCase());
</script>

<template>
    <InternalLayout>
        <Head :title="`Rental Order #${rentalOrder.id}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Rental Order #{{ rentalOrder.id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Created {{ formatDate(rentalOrder.created_at) }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link :href="route('internal.rental-orders.edit', rentalOrder.id)"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            Edit Order
                        </Link>
                    </div>
                </div>

                <!-- Top row: Status + Vehicle + Rental Period + Payment -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                    <!-- Status -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Order Status</h2>
                        <span :class="[getStatusColor(rentalOrder.status), 'px-3 py-1 rounded-full text-sm font-semibold']">
                            {{ rentalOrder.status.charAt(0).toUpperCase() + rentalOrder.status.slice(1) }}
                        </span>
                    </div>

                    <!-- Vehicle -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <TruckIcon class="h-4 w-4 text-gray-400" />
                            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vehicle</h2>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ rentalOrder.vehicle.brand }} {{ rentalOrder.vehicle.model }}</p>
                        <p class="text-sm text-gray-500">Plate: {{ rentalOrder.vehicle.plate_no }}</p>
                        <p class="text-sm font-bold text-red-600 mt-1">₱{{ rentalOrder.vehicle.rental_rate?.toLocaleString() }}/day</p>
                    </div>

                    <!-- Rental Period -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <CalendarIcon class="h-4 w-4 text-gray-400" />
                            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rental Period</h2>
                        </div>
                        <p class="text-sm text-gray-600">Start: {{ formatDate(rentalOrder.rental_date) }}</p>
                        <p class="text-sm text-gray-600">End: {{ formatDate(rentalOrder.return_date) }}</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ calculateDuration() }} day(s)</p>
                    </div>

                    <!-- Payment -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <CurrencyDollarIcon class="h-4 w-4 text-gray-400" />
                            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment</h2>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Daily Rate</span>
                                <span>₱{{ rentalOrder.vehicle.rental_rate?.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Days</span>
                                <span>{{ calculateDuration() }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-red-600 border-t pt-1 mt-1">
                                <span>Total</span>
                                <span>₱{{ rentalOrder.total_amount?.toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== CUSTOMER INFORMATION SECTION ===== -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <UserIcon class="h-5 w-5 text-red-500" />
                        Customer Information
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                        <!-- Personal Details -->
                        <div class="bg-white shadow rounded-lg p-5">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Personal Details</h3>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-xs text-gray-400">Full Name</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ profile?.first_name || rentalOrder.user.name }}
                                            {{ profile?.last_name ?? '' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Username / Display</p>
                                        <p class="text-sm text-gray-700">{{ rentalOrder.user.name }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-xs text-gray-400">Email</p>
                                        <p class="text-sm text-gray-700">{{ rentalOrder.user.email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Phone</p>
                                        <p class="text-sm text-gray-700">{{ profile?.phone_number ?? '—' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-xs text-gray-400">Gender</p>
                                        <p class="text-sm text-gray-700 capitalize">{{ profile?.gender ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Date of Birth</p>
                                        <p class="text-sm text-gray-700">{{ profile?.birth_date ? formatDate(profile.birth_date) : '—' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Address</p>
                                    <p class="text-sm text-gray-700">{{ profile?.address ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="bg-white shadow rounded-lg p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <PhoneIcon class="h-4 w-4 text-gray-400" />
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Emergency Contact</h3>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">Name</p>
                                    <p class="text-sm font-medium text-gray-900">{{ profile?.emergency_contact_name ?? '—' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-xs text-gray-400">Relationship</p>
                                        <p class="text-sm text-gray-700">{{ profile?.emergency_contact_relationship ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Phone</p>
                                        <p class="text-sm text-gray-700">{{ profile?.emergency_contact_phone ?? '—' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver's License -->
                        <div class="bg-white shadow rounded-lg p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <IdentificationIcon class="h-4 w-4 text-gray-400" />
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Driver's License</h3>
                                </div>
                                <span v-if="isLicenseVerified"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <ShieldCheckIcon class="h-3.5 w-3.5" /> Verified
                                </span>
                                <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Pending Verification
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <p class="text-xs text-gray-400">License Number</p>
                                        <p class="text-sm font-mono font-semibold text-gray-900">{{ profile?.drivers_license_number ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Expiry Date</p>
                                        <p class="text-sm text-gray-700">{{ profile?.drivers_license_expiry ? formatDate(profile.drivers_license_expiry) : '—' }}</p>
                                    </div>
                                </div>

                                <div v-if="profile?.drivers_license_verified_at">
                                    <p class="text-xs text-gray-400">Verified On</p>
                                    <p class="text-sm text-gray-700">{{ formatDate(profile.drivers_license_verified_at) }}</p>
                                </div>

                                <!-- License Scans -->
                                <div v-if="profileLicenseScans.length > 0 || rentalLicenseScans.length > 0">
                                    <p class="text-xs text-gray-400 mb-2">License Scans</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template v-for="att in [...rentalLicenseScans, ...profileLicenseScans]" :key="att.id">
                                            <a :href="att.file_path" target="_blank" class="relative block group">
                                                <img v-if="isImage(att.file_extension)" :src="att.file_path"
                                                    class="h-20 w-20 object-cover rounded-lg border border-gray-200 hover:ring-2 hover:ring-red-400" />
                                                <div v-else class="h-20 w-20 flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:ring-2 hover:ring-red-400">
                                                    <PaperClipIcon class="h-6 w-6 text-gray-400" />
                                                    <span class="text-xs text-gray-500 mt-1 uppercase">{{ att.file_extension }}</span>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </div>

                                <!-- LTO Verification Actions -->
                                <div class="flex gap-2 pt-2 border-t">
                                    <a href="https://portal.lto.gov.ph" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                        Open LTO Portal
                                    </a>
                                    <button v-if="!isLicenseVerified" @click="verifyLicense"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md bg-green-600 text-white hover:bg-green-700">
                                        <ShieldCheckIcon class="h-3.5 w-3.5" /> Mark as Verified
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submitted Documents (Valid ID + rental uploads) -->
                        <div class="bg-white shadow rounded-lg p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <DocumentTextIcon class="h-4 w-4 text-gray-400" />
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted Documents</h3>
                            </div>

                            <!-- Valid IDs -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-400 mb-2">Valid Government ID</p>
                                <div v-if="rentalValidIds.length > 0 || profileValidIds.length > 0" class="flex flex-wrap gap-2">
                                    <template v-for="att in [...rentalValidIds, ...profileValidIds]" :key="att.id">
                                        <a :href="att.file_path" target="_blank" class="block group">
                                            <img v-if="isImage(att.file_extension)" :src="att.file_path"
                                                class="h-24 w-24 object-cover rounded-lg border border-gray-200 hover:ring-2 hover:ring-red-400" />
                                            <div v-else class="h-24 w-24 flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:ring-2 hover:ring-red-400">
                                                <PaperClipIcon class="h-6 w-6 text-gray-400" />
                                                <span class="text-xs text-gray-500 mt-1 uppercase">{{ att.file_extension }}</span>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                                <p v-else class="text-sm text-gray-400 italic">No ID documents submitted.</p>
                            </div>

                            <!-- Other rental attachments -->
                            <div v-if="rentalOtherDocs.length > 0">
                                <p class="text-xs text-gray-400 mb-2">Other Rental Uploads</p>
                                <div class="flex flex-wrap gap-2">
                                    <a v-for="att in rentalOtherDocs" :key="att.id" :href="att.file_path" target="_blank" class="block group">
                                        <img v-if="isImage(att.file_extension)" :src="att.file_path"
                                            class="h-20 w-20 object-cover rounded-lg border border-gray-200 hover:ring-2 hover:ring-red-400" />
                                        <div v-else class="h-20 w-20 flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:ring-2 hover:ring-red-400">
                                            <PaperClipIcon class="h-6 w-6 text-gray-400" />
                                            <span class="text-xs text-gray-500 mt-1 uppercase">{{ att.file_extension }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div v-if="rentalAttachments.length === 0 && profileValidIds.length === 0" class="text-sm text-gray-400 italic">
                                No documents attached to this rental.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes + Session row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Notes -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <DocumentTextIcon class="h-4 w-4 text-gray-400" />
                            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Notes</h2>
                        </div>
                        <p class="text-sm text-gray-700">{{ rentalOrder.notes || 'No notes provided.' }}</p>
                    </div>

                    <!-- Rental Session -->
                    <div class="bg-white shadow rounded-lg p-5">
                        <template v-if="rentalOrder.rental_vehicle_sessions?.length > 0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <DeviceTabletIcon class="h-4 w-4 text-gray-400" />
                                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rental Session</h2>
                                </div>
                                <span class="text-xs font-bold text-green-600">{{ rentalOrder.rental_vehicle_sessions[0].status }}</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Started</span>
                                    <span class="text-gray-900">{{ rentalOrder.rental_vehicle_sessions[0].started_at ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Ended</span>
                                    <span class="text-gray-900">{{ rentalOrder.rental_vehicle_sessions[0].ended_at ?? '—' }}</span>
                                </div>
                                <a class="text-blue-500 text-xs underline" target="_blank"
                                    :href="route('kiosk.session', { sessionId: rentalOrder.rental_vehicle_sessions[0].session_token, rentalOrderId: rentalOrder.id })">
                                    View Session
                                </a>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-center gap-2 mb-3">
                                <DeviceTabletIcon class="h-4 w-4 text-gray-400" />
                                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rental Session</h2>
                            </div>
                            <p class="text-sm text-gray-400 italic">No session data available.</p>
                        </template>
                    </div>

                </div>

            </div>
        </div>
    </InternalLayout>
</template>
