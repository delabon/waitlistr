<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard as dashboardRoute } from '@/routes';
import { signups as adminSignupsRoute } from '@/routes/dashboard/admin';
import { type NavItem } from '@/types';
import {Link, usePage} from '@inertiajs/vue3';
import { Folder, LayoutGrid, MailCheck } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboardRoute(),
        icon: LayoutGrid,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Signups',
        href: adminSignupsRoute(),
        icon: MailCheck,
        count: usePage().props.signupsCount,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/delabon/waitlistr',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardRoute()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" :adminItems="adminNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
