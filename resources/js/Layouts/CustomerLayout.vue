<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Navbar from '@/Components/Navbar.vue';
import {
    HomeIcon,
    ShoppingBagIcon,
    UserCircleIcon,
    ChatBubbleLeftRightIcon,
    ClockIcon,
    CreditCardIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';
import Flash from '@/Components/Flash.vue';

const navigation = [
    {
        name: 'Dashboard',
        icon: HomeIcon,
        href: route('customer.dashboard'),
        isActive: route().current('customer.dashboard'),
        children: []
    },
    {
        name: 'Rentals',
        icon: ShoppingBagIcon,
        href: "#",
        isActive: route().current('customer.rentals.*'),
        children: [
            {
                name: 'Rentals',
                href: route('customer.rentals.index'),
                isActive: route().current('customer.rentals.active')
            },
            {
                name: 'Rental History',
                href: route('customer.rentals.history'),
                isActive: route().current('customer.rentals.history')
            }
        ]
    },
    {
        name: 'Payments',
        icon: CreditCardIcon,
        href: "#",
        isActive: route().current('customer.payments.*'),
        children: [
            {
                name: 'Payment History',
                href: "#",
                isActive: route().current('customer.payments.history')
            },
            {
                name: 'Pending Payments',
                href: '#',
                isActive: route().current('customer.payments.pending')
            }
        ]
    },
    {
        name: 'Documents',
        icon: DocumentTextIcon,
        href: "#",
        isActive: route().current('customer.documents.*'),
        children: [
            {
                name: 'Rental Agreements',
                href: "#",
                isActive: route().current('customer.documents.agreements')
            },
            {
                name: 'Receipts',
                href: '#',
                isActive: route().current('customer.documents.receipts')
            }
        ]
    },
    {
        name: 'Support',
        icon: ChatBubbleLeftRightIcon,
        href: "#",
        isActive: route().current('customer.support'),
        children: []
    },
    {
        name: 'Profile',
        icon: UserCircleIcon,
        href: '#',
        isActive: route().current('profile.edit'),
        children: []
    }
];

const user = {
    name: 'John Doe',
    avatar: 'https://ui-avatars.com/api/?name=John+Doe'
};


</script>

<template>

    <div class="min-h-screen flex flex-col">
        <Flash />
        <div class="flex flex-1">
            <Sidebar :navigation="navigation" />


            <div class="flex-1 flex flex-col">
                <Navbar  :user="user" :links="navigationLinks"/>

                <main class="flex-1 p-6">
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>

</template>
