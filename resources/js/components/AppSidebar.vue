
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutDashboard } from '@lucide/vue';

import MentoraBrand from '@/components/mentora/MentoraBrand.vue';
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

import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

/*
|--------------------------------------------------------------------------
| MENTORA — Dashboard Navigation
|--------------------------------------------------------------------------
| Untuk Sprint 0, hanya menu dengan route yang sudah tersedia
| yang ditampilkan. Navigasi berdasarkan role akan ditambahkan
| pada tahap implementasi fitur dan authorization.
|--------------------------------------------------------------------------
*/

const mainNavItems: NavItem[] = [
    {
        title: 'Beranda',
        href: dashboard(),
        icon: LayoutDashboard,
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="border-r border-mentora-border bg-white text-mentora-navy"
    >
        <!-- MENTORA Branding -->
        <SidebarHeader class="px-3 pt-4">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="hover:bg-mentora-lavender"
                    >
                        <Link
                            :href="dashboard()"
                            aria-label="Beranda Dashboard MENTORA"
                        >
                            <MentoraBrand />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Main Navigation -->
        <SidebarContent class="px-2">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- User Profile -->
        <SidebarFooter
            class="border-t border-mentora-border p-2"
        >
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
