<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { signups as adminSignupsRoute } from '@/routes/dashboard/admin';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Pagination from "@/components/Pagination.vue";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Signups',
        href: adminSignupsRoute().url,
    },
];

defineProps({
    waitlistSignups: {
        type: Object,
        required: true,
    }
});

</script>

<template>
    <Head title="Signups" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mt-3 relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="bg-neutral-secondary-soft border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-bold w-8">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold w-28">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold w-28">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold w-20">
                            Welcome Email Sent At
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold w-20">
                            Signup At
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="waitlistSignup in waitlistSignups.data"
                            :key="waitlistSignup.id"
                            class="bg-neutral-primary border-b border-default"
                        >
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                {{  waitlistSignup.id }}
                            </th>
                            <td class="px-6 py-4">
                                {{  waitlistSignup.email }}
                            </td>
                            <td class="px-6 py-4">
                                {{  waitlistSignup.first_name }} {{  waitlistSignup.last_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{  waitlistSignup.formatted_welcome_email_sent_at }}
                            </td>
                            <td class="px-6 py-4">
                                {{  waitlistSignup.formatted_created_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-center">
                <Pagination :meta="waitlistSignups.meta"/>
            </div>
        </div>
    </AppLayout>
</template>
