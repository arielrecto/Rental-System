<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    CalendarIcon,
    IdentificationIcon,
    CakeIcon,
    UserCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    if (!date) return 'Not provided';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getGenderDisplay = (gender) => {
    if (!gender) return 'Not provided';
    return gender.charAt(0).toUpperCase() + gender.slice(1);
};

const calculateAge = (birthDate) => {
    if (!birthDate) return null;
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
};

const getFullName = () => {
    if (!props.user.profile?.first_name && !props.user.profile?.last_name) {
        return props.user.name;
    }
    return `${props.user.profile?.first_name || ''} ${props.user.profile?.last_name || ''}`.trim();
};

const validIdAttachments = computed(() =>
    (props.user.profile?.attachments ?? []).filter(a => a.category === 'valid_id')
);
const licenseAttachments = computed(() =>
    (props.user.profile?.attachments ?? []).filter(a => a.category === 'drivers_license')
);
const isLicenseVerified = computed(() => props.user.profile?.drivers_license_verified ?? false);

const ltoPortalUrl = 'https://portal.lto.gov.ph';

const verifyLicense = () => {
    router.patch(route('internal.user-management.verify-license', props.user.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <InternalLayout>
        <Head :title="`User - ${user.name}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">User Profile</h1>
                        <p class="mt-1 text-sm text-gray-600">View user information</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('internal.user-management.index')"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200"
                        >
                            Back
                        </Link>
                        <Link
                            :href="route('internal.user-management.edit', user.id)"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-800"
                        >
                            Edit User
                        </Link>
                    </div>
                </div>

                <!-- Profile Header Card -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-24 w-24 rounded-full bg-red-100 flex items-center justify-center">
                                <UserCircleIcon class="h-16 w-16 text-red-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900">{{ getFullName() }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ user.email }}</p>
                            <div class="mt-3 flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <div class="h-2 w-2 rounded-full bg-green-600 mr-2"></div>
                                    Active
                                </span>
                                <span class="text-sm text-gray-500">
                                    Joined {{ formatDate(user.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Account Information Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Account Information
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <UserIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Username</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ user.name }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Email Address</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <CalendarIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Member Since</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ formatDate(user.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Personal Information
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <IdentificationIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Full Name</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ user.profile?.first_name || 'Not provided' }} 
                                        {{ user.profile?.last_name || '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <UserIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Gender</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ getGenderDisplay(user.profile?.gender) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <CakeIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Birth Date</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ formatDate(user.profile?.birth_date) }}
                                        <span v-if="user.profile?.birth_date" class="text-gray-500">
                                            ({{ calculateAge(user.profile.birth_date) }} years old)
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Contact Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-start">
                                <PhoneIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Phone Number</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ user.profile?.phone_number || 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <MapPinIcon class="h-5 w-5 text-gray-400 mr-3 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Address</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ user.profile?.address || 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Emergency Contact
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p class="text-sm text-gray-800 mt-0.5">{{ user.profile?.emergency_contact_name || 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Relationship</p>
                                <p class="text-sm text-gray-800 mt-0.5">{{ user.profile?.emergency_contact_relationship || 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-sm text-gray-800 mt-0.5">{{ user.profile?.emergency_contact_phone || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Driver's License Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Driver's License</h2>
                            <span v-if="isLicenseVerified"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15 3.293 9.879a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                            <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">License Number</p>
                                <p class="text-sm text-gray-800 font-mono mt-0.5">{{ user.profile?.drivers_license_number || 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Expiry Date</p>
                                <p class="text-sm text-gray-800 mt-0.5">{{ user.profile?.drivers_license_expiry ? formatDate(user.profile.drivers_license_expiry) : 'Not provided' }}</p>
                            </div>

                            <div v-if="licenseAttachments.length > 0" class="flex flex-wrap gap-2 mt-2">
                                <a v-for="att in licenseAttachments" :key="att.id" :href="att.file_path" target="_blank" class="block">
                                    <img v-if="['jpg','jpeg','png'].includes(att.file_extension)" :src="att.file_path"
                                        class="h-20 w-20 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition" />
                                    <div v-else class="h-20 w-20 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition">
                                        <span class="text-xs text-gray-500 font-medium">{{ att.file_extension.toUpperCase() }}</span>
                                    </div>
                                </a>
                            </div>

                            <div class="flex gap-2 pt-2" v-if="user.profile?.drivers_license_number">
                                <a :href="ltoPortalUrl" target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition">
                                    Open LTO Portal
                                </a>
                                <button v-if="!isLicenseVerified" @click="verifyLicense"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                    Mark as Verified
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Valid Government IDs
                        </h2>
                        <div v-if="validIdAttachments.length > 0" class="flex flex-wrap gap-3">
                            <a v-for="att in validIdAttachments" :key="att.id" :href="att.file_path" target="_blank" class="block">
                                <img v-if="['jpg','jpeg','png'].includes(att.file_extension)" :src="att.file_path"
                                    class="h-28 w-28 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition" />
                                <div v-else class="h-28 w-28 flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition gap-1">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-xs text-gray-500 font-medium">{{ att.file_extension.toUpperCase() }}</span>
                                </div>
                            </a>
                        </div>
                        <p v-else class="text-sm text-gray-500">No government IDs uploaded yet.</p>
                    </div>

                    <!-- Activity Summary Card -->
                    <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Activity Summary
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-3xl font-bold text-red-600">0</p>
                                <p class="text-sm text-gray-600 mt-1">Total Rentals</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-3xl font-bold text-red-600">{{ user.payments?.length || 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Total Payments</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-3xl font-bold text-red-600">
                                    {{ formatDate(user.updated_at) }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Last Updated</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
