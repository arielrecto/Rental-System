<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const currentStep = ref(1);
const totalSteps = 4;

const steps = [
    { number: 1, label: 'Account' },
    { number: 2, label: 'Personal' },
    { number: 3, label: 'Emergency' },
    { number: 4, label: 'License' },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    first_name: '',
    last_name: '',
    phone_number: '',
    address: '',
    gender: 'male',
    birth_date: '',
    emergency_contact_name: '',
    emergency_contact_relationship: '',
    emergency_contact_phone: '',
    drivers_license_number: '',
    drivers_license_expiry: '',
});

const stepErrors = ref({});

const validateStep = () => {
    stepErrors.value = {};

    if (currentStep.value === 1) {
        if (!form.name) stepErrors.value.name = 'Full name is required.';
        if (!form.email) stepErrors.value.email = 'Email is required.';
        if (!form.password) stepErrors.value.password = 'Password is required.';
        if (form.password !== form.password_confirmation)
            stepErrors.value.password_confirmation = 'Passwords do not match.';
    }

    if (currentStep.value === 2) {
        if (!form.first_name) stepErrors.value.first_name = 'First name is required.';
        if (!form.last_name) stepErrors.value.last_name = 'Last name is required.';
        if (!form.phone_number) stepErrors.value.phone_number = 'Phone number is required.';
        if (!form.address) stepErrors.value.address = 'Address is required.';
        if (!form.birth_date) stepErrors.value.birth_date = 'Date of birth is required.';
    }

    if (currentStep.value === 3) {
        if (!form.emergency_contact_name) stepErrors.value.emergency_contact_name = 'Contact name is required.';
        if (!form.emergency_contact_relationship) stepErrors.value.emergency_contact_relationship = 'Relationship is required.';
        if (!form.emergency_contact_phone) stepErrors.value.emergency_contact_phone = 'Contact phone is required.';
    }

    return Object.keys(stepErrors.value).length === 0;
};

const nextStep = () => {
    if (validateStep()) currentStep.value++;
};

const prevStep = () => {
    stepErrors.value = {};
    currentStep.value--;
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Create Account</h2>
            <p class="text-gray-600 mt-2">Join Anaia's Motorcycle Rental today</p>
        </div>

        <!-- Step Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between relative">
                <!-- Progress line -->
                <div class="absolute left-0 top-4 h-0.5 bg-gray-200 w-full -z-10"></div>
                <div
                    class="absolute left-0 top-4 h-0.5 bg-red-500 -z-10 transition-all duration-500"
                    :style="{ width: `${((currentStep - 1) / (totalSteps - 1)) * 100}%` }"
                ></div>

                <div v-for="step in steps" :key="step.number" class="flex flex-col items-center gap-1">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                        :class="currentStep > step.number
                            ? 'bg-red-500 text-white'
                            : currentStep === step.number
                                ? 'bg-red-600 text-white ring-4 ring-red-100'
                                : 'bg-white border-2 border-gray-300 text-gray-400'"
                    >
                        <svg v-if="currentStep > step.number" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15 3.293 9.879a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span v-else>{{ step.number }}</span>
                    </div>
                    <span
                        class="text-xs font-medium"
                        :class="currentStep >= step.number ? 'text-red-600' : 'text-gray-400'"
                    >{{ step.label }}</span>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Step 1: Account Information -->
            <div v-if="currentStep === 1" class="space-y-4 animate-fade-in">
                <h3 class="text-base font-semibold text-gray-800">Account Information</h3>

                <div>
                    <InputLabel for="name" value="Full Name" class="text-gray-700" />
                    <TextInput id="name" type="text" class="mt-1 block w-full rounded-lg" v-model="form.name"
                        autofocus autocomplete="name" />
                    <InputError class="mt-1" :message="stepErrors.name || form.errors.name" />
                </div>
                <div>
                    <InputLabel for="email" value="Email Address" class="text-gray-700" />
                    <TextInput id="email" type="email" class="mt-1 block w-full rounded-lg" v-model="form.email"
                        autocomplete="username" />
                    <InputError class="mt-1" :message="stepErrors.email || form.errors.email" />
                </div>
                <div>
                    <InputLabel for="password" value="Password" class="text-gray-700" />
                    <TextInput id="password" type="password" class="mt-1 block w-full rounded-lg" v-model="form.password"
                        autocomplete="new-password" />
                    <InputError class="mt-1" :message="stepErrors.password || form.errors.password" />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-700" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full rounded-lg"
                        v-model="form.password_confirmation" autocomplete="new-password" />
                    <InputError class="mt-1" :message="stepErrors.password_confirmation || form.errors.password_confirmation" />
                </div>
            </div>

            <!-- Step 2: Personal Information -->
            <div v-if="currentStep === 2" class="space-y-4 animate-fade-in">
                <h3 class="text-base font-semibold text-gray-800">Personal Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="first_name" value="First Name" class="text-gray-700" />
                        <TextInput id="first_name" type="text" class="mt-1 block w-full rounded-lg" v-model="form.first_name" autofocus />
                        <InputError class="mt-1" :message="stepErrors.first_name || form.errors.first_name" />
                    </div>
                    <div>
                        <InputLabel for="last_name" value="Last Name" class="text-gray-700" />
                        <TextInput id="last_name" type="text" class="mt-1 block w-full rounded-lg" v-model="form.last_name" />
                        <InputError class="mt-1" :message="stepErrors.last_name || form.errors.last_name" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="gender" value="Gender" class="text-gray-700" />
                        <select id="gender" v-model="form.gender"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <InputError class="mt-1" :message="form.errors.gender" />
                    </div>
                    <div>
                        <InputLabel for="birth_date" value="Date of Birth" class="text-gray-700" />
                        <TextInput id="birth_date" type="date" class="mt-1 block w-full rounded-lg" v-model="form.birth_date" />
                        <InputError class="mt-1" :message="stepErrors.birth_date || form.errors.birth_date" />
                    </div>
                </div>
                <div>
                    <InputLabel for="phone_number" value="Phone Number" class="text-gray-700" />
                    <TextInput id="phone_number" type="text" class="mt-1 block w-full rounded-lg" v-model="form.phone_number" />
                    <InputError class="mt-1" :message="stepErrors.phone_number || form.errors.phone_number" />
                </div>
                <div>
                    <InputLabel for="address" value="Address" class="text-gray-700" />
                    <textarea id="address" v-model="form.address" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Street, Barangay, City, Province"
                    ></textarea>
                    <InputError class="mt-1" :message="stepErrors.address || form.errors.address" />
                </div>
            </div>

            <!-- Step 3: Emergency Contact -->
            <div v-if="currentStep === 3" class="space-y-4 animate-fade-in">
                <h3 class="text-base font-semibold text-gray-800">Emergency Contact</h3>
                <p class="text-sm text-gray-500">Person to contact in case of an emergency during your rental.</p>

                <div>
                    <InputLabel for="emergency_contact_name" value="Contact Name" class="text-gray-700" />
                    <TextInput id="emergency_contact_name" type="text" class="mt-1 block w-full rounded-lg"
                        v-model="form.emergency_contact_name" autofocus />
                    <InputError class="mt-1" :message="stepErrors.emergency_contact_name || form.errors.emergency_contact_name" />
                </div>
                <div>
                    <InputLabel for="emergency_contact_relationship" value="Relationship" class="text-gray-700" />
                    <TextInput id="emergency_contact_relationship" type="text" class="mt-1 block w-full rounded-lg"
                        v-model="form.emergency_contact_relationship" placeholder="e.g. Parent, Spouse, Sibling" />
                    <InputError class="mt-1" :message="stepErrors.emergency_contact_relationship || form.errors.emergency_contact_relationship" />
                </div>
                <div>
                    <InputLabel for="emergency_contact_phone" value="Contact Phone" class="text-gray-700" />
                    <TextInput id="emergency_contact_phone" type="text" class="mt-1 block w-full rounded-lg"
                        v-model="form.emergency_contact_phone" />
                    <InputError class="mt-1" :message="stepErrors.emergency_contact_phone || form.errors.emergency_contact_phone" />
                </div>
            </div>

            <!-- Step 4: Driver's License (Optional) -->
            <div v-if="currentStep === 4" class="space-y-4 animate-fade-in">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Driver's License</h3>
                    <p class="text-sm text-gray-500 mt-1">Optional — you can add or update this anytime from your profile.</p>
                </div>

                <div>
                    <InputLabel for="drivers_license_number" value="License Number" class="text-gray-700" />
                    <TextInput id="drivers_license_number" type="text" class="mt-1 block w-full rounded-lg"
                        v-model="form.drivers_license_number" autofocus />
                    <InputError class="mt-1" :message="form.errors.drivers_license_number" />
                </div>
                <div>
                    <InputLabel for="drivers_license_expiry" value="Expiry Date" class="text-gray-700" />
                    <TextInput id="drivers_license_expiry" type="date" class="mt-1 block w-full rounded-lg"
                        v-model="form.drivers_license_expiry" />
                    <InputError class="mt-1" :message="form.errors.drivers_license_expiry" />
                </div>

                <!-- Summary -->
                <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600 space-y-1">
                    <p class="font-semibold text-gray-700 mb-2">Ready to create your account</p>
                    <p><span class="text-gray-400">Name:</span> {{ form.name }}</p>
                    <p><span class="text-gray-400">Email:</span> {{ form.email }}</p>
                    <p><span class="text-gray-400">Full Name:</span> {{ form.first_name }} {{ form.last_name }}</p>
                    <p><span class="text-gray-400">Phone:</span> {{ form.phone_number }}</p>
                    <p><span class="text-gray-400">Emergency Contact:</span> {{ form.emergency_contact_name }}</p>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between pt-2">
                <button
                    v-if="currentStep > 1"
                    type="button"
                    @click="prevStep"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition"
                >
                    ← Back
                </button>
                <div v-else></div>

                <button
                    v-if="currentStep < totalSteps"
                    type="button"
                    @click="nextStep"
                    class="px-6 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition"
                >
                    Next →
                </button>

                <PrimaryButton
                    v-if="currentStep === totalSteps"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 focus:ring-red-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Create Account
                </PrimaryButton>
            </div>

            <div class="text-center">
                <span class="text-gray-600 text-sm">Already have an account?</span>
                <Link :href="route('login')" class="text-sm text-red-600 hover:text-red-700 ml-2 font-semibold">
                    Sign in here
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
:deep(.primary-button) {
    @apply transition-colors duration-300 rounded-lg shadow-sm;
}
:deep(.text-input) {
    @apply focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm;
}
</style>
