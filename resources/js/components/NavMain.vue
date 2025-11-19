<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
    adminItems: NavItem[];
}>();

const page = usePage();
const isAdmin = page.props.auth?.user.role === 'admin';
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>

    <SidebarGroup v-if="isAdmin" class="px-2 py-0">
        <SidebarGroupLabel>Admin</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in adminItems" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span class="inline-flex items-center justify-between gap-1 grow">
                            <span>{{ item.title }}</span>
                            <span
                                v-if="item.count !== undefined"
                                class="signups-count inline-flex items-center justify-center h-5 min-w-5 leading-none rounded-full bg-primary text-xs font-medium text-primary-foreground"
                            >{{ item.count }}</span>
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
