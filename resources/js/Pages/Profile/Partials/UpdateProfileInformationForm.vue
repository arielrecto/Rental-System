<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
    profile: { type: Object, default: () => null },
});

const page = usePage();
const user = page.props.auth.user;

const form = ref({
    _method: 'PATCH',
    name: user.name,
    email: user.email,
    first_name: props.profile?.first_name ?? '',
    last_name: props.profile?.last_name ?? '',
    phone_number: props.profile?.phone_number ?? '',
    address: props.profile?.address ?? '',
    gender: props.profile?.gender ?? 'male',
    birth_date: props.profile?.birth_date ?? '',
    emergency_contact_name: props.profile?.emergency_contact_name ?? '',
    emergency_contact_relationship: props.profile?.emergency_contact_relationship ?? '',
    emergency_contact_phone: props.profile?.emergency_contact_phone ?? '',
    drivers_license_number: props.profile?.drivers_license_number ?? '',
    drivers_license_expiry: props.profile?.drivers_license_expiry ?? '',
    valid_id: [],
    drivers_license_file: [],
});

const errors = ref({});
const recentlySuccessful = ref(false);
const processing = ref(false);

const validIdAttachments = computed(() =>
    (props.profile?.attachments ?? []).filter(a => a.category === 'valid_id')
);
const licenseAttachments = computed(() =>
    (props.profile?.attachments ?? []).filter(a => a.category === 'drivers_license')
);
const isLicenseVerified = computed(() => props.profile?.drivers_license_verified ?? false);

const handleFileInput = (event, key) => {
    form.value[key] = Array.from(event.target.files);
};

const deleteAttachment = (attachment) => {
    router.delete(route('profile.attachment.delete', attachment.id), {
        preserveScroll: true,
    });
};

const submit = () => {
    processing.value = true;
    errors.value = {};

    const data = new FormData();
    Object.entries(form.value).forEach(([key, val]) => {
        if (key === 'valid_id' || key === 'drivers_license_file') {
            val.forEach(file => data.append(`${key}[]`, file));
        } else {
            data.append(key, val ?? '');
        }
    });

    router.post(route('profile.update'), data, {
        preserveScroll: true,
        onSuccess: () => {
            recentlySuccessful.value = true;
            form.value.valid_id = [];
            form.value.drivers_license_file = [];
            setTimeout(() => { recentlySuccessful.value = false; }, 2000);
        },
        onError: (e) => { errors.value = e; },
        onFinish: () => { processing.value = false; },
    });
};
</script>

<template>
    <section class="space-y-8">
        <form @submit.prevent="submit" class="space-y-8">

            <!-- Account -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Account</h3>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>
                    <div v-if="mustVerifyEmail && user.email_verified_at === null">
                        <p class="mt-2 text-sm text-gray-800">
                            Your email address is unverified.
                            <Link :href="route('verification.send')" method="post" as="button"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Click here to re-send the verification email.
                            </Link>
                        </p>
                        <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" />
                            <InputError class="mt-2" :message="errors.first_name" />
                        </div>
                        <div>
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" />
                            <InputError class="mt-2" :message="errors.last_name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="gender" value="Gender" />
                            <select id="gender" v-model="form.gender"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <InputError class="mt-2" :message="errors.gender" />
                        </div>
                        <div>
                            <InputLabel for="birth_date" value="Date of Birth" />
                            <TextInput id="birth_date" type="date" class="mt-1 block w-full" v-model="form.birth_date" />
                            <InputError class="mt-2" :message="errors.birth_date" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="phone_number" value="Phone Number" />
                        <TextInput id="phone_number" type="text" class="mt-1 block w-full" v-model="form.phone_number" />
                        <InputError class="mt-2" :message="errors.phone_number" />
                    </div>
                    <div>
                        <InputLabel for="address" value="Address" />
                        <textarea id="address" v-model="form.address" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                            placeholder="Street, Barangay, City, Province"
                        ></textarea>
                        <InputError class="mt-2" :message="errors.address" />
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Emergency Contact</h3>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="emergency_contact_name" value="Contact Name" />
                        <TextInput id="emergency_contact_name" type="text" class="mt-1 block w-full" v-model="form.emergency_contact_name" />
                        <InputError class="mt-2" :message="errors.emergency_contact_name" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="emergency_contact_relationship" value="Relationship" />
                            <TextInput id="emergency_contact_relationship" type="text" class="mt-1 block w-full" v-model="form.emergency_contact_relationship" placeholder="e.g. Parent, Spouse" />
                            <InputError class="mt-2" :message="errors.emergency_contact_relationship" />
                        </div>
                        <div>
                            <InputLabel for="emergency_contact_phone" value="Contact Phone" />
                            <TextInput id="emergency_contact_phone" type="text" class="mt-1 block w-full" v-model="form.emergency_contact_phone" />
                            <InputError class="mt-2" :message="errors.emergency_contact_phone" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver's License -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Driver's License</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="drivers_license_number" value="License Number" />
                            <TextInput id="drivers_license_number" type="text" class="mt-1 block w-full" v-model="form.drivers_license_number" />
                            <InputError class="mt-2" :message="errors.drivers_license_number" />
                        </div>
                        <div>
                            <InputLabel for="drivers_license_expiry" value="Expiry Date" />
                            <TextInput id="drivers_license_expiry" type="date" class="mt-1 block w-full" v-model="form.drivers_license_expiry" />
                            <InputError class="mt-2" :message="errors.drivers_license_expiry" />
                        </div>
                    </div>

                    <div>
                        <span v-if="isLicenseVerified"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15 3.293 9.879a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Verified by Staff
                        </span>
                        <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            Pending Verification
                        </span>
                    </div>

                    <div v-if="licenseAttachments.length > 0">
                        <p class="text-xs text-gray-500 mb-2">Uploaded scans</p>
                        <div class="flex flex-wrap gap-3">
                            <div v-for="att in licenseAttachments" :key="att.id" class="relative group">
                                <img v-if="['jpg','jpeg','png'].includes(att.file_extension)" :src="att.file_path"
                                    class="h-24 w-24 object-cover rounded-lg border border-gray-200" />
                                <div v-else class="h-24 w-24 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">{{ att.file_extension.toUpperCase() }}</span>
                                </div>
                                <button type="button" @click="deleteAttachment(att)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs hidden group-hover:flex items-center justify-center leading-none">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Upload License Scan" />
                        <p class="text-xs text-gray-400 mt-0.5 mb-1">Accepts jpg, png, pdf — max 10MB each</p>
                        <input type="file" multiple accept=".jpg,.jpeg,.png,.pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100"
                            @change="(e) => handleFileInput(e, 'drivers_license_file')" />
                        <InputError class="mt-2" :message="errors['drivers_license_file.0']" />
                    </div>
                </div>
            </div>

            <!-- Valid Government ID -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Valid Government ID</h3>
                <div class="space-y-4">
                    <div v-if="validIdAttachments.length > 0">
                        <p class="text-xs text-gray-500 mb-2">Uploaded IDs</p>
                        <div class="flex flex-wrap gap-3">
                            <div v-for="att in validIdAttachments" :key="att.id" class="relative group">
                                <img v-if="['jpg','jpeg','png'].includes(att.file_extension)" :src="att.file_path"
                                    class="h-24 w-24 object-cover rounded-lg border border-gray-200" />
                                <div v-else class="h-24 w-24 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">{{ att.file_extension.toUpperCase() }}</span>
                                </div>
                                <button type="button" @click="deleteAttachment(att)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs hidden group-hover:flex items-center justify-center leading-none">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Upload Government ID" />
                        <p class="text-xs text-gray-400 mt-0.5 mb-1">Accepted: National ID, Passport, PhilSys, Voter's ID — jpg, png, pdf — max 10MB each</p>
                        <input type="file" multiple accept=".jpg,.jpeg,.png,.pdf"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100"
                            @change="(e) => handleFileInput(e, 'valid_id')" />
                        <InputError class="mt-2" :message="errors['valid_id.0']" />
                    </div>
                </div>
            </div>

            <!-- Save -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="processing">Save</PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
