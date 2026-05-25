<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookMarked, BookOpen, Bookmark, ClipboardList, Heart, IdCard, LayoutGrid, Search, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
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
import type { NavItem, Peran } from '@/types';

const page = usePage();
const peran = computed<Peran>(() => page.props.auth?.user?.peran ?? 'user');

const sidebarHome = computed(() => {
    const routes: Record<Peran, string> = {
        super_admin: '/super-admin/kelola-admin',
        admin: '/admin/buku',
        user: '/app/dasbor',
    };

    return routes[peran.value];
});

const mainNavItems = computed<NavItem[]>(() => {
    const items: Record<Peran, NavItem[]> = {
        super_admin: [
            { title: 'Kelola Admin', href: '/super-admin/kelola-admin', icon: Users },
        ],
        admin: [
            { title: 'Manajemen Buku', href: '/admin/buku', icon: BookOpen },
            { title: 'Penulis', href: '/admin/penulis', icon: Users },
            { title: 'Laporan', href: '/admin/laporan', icon: ClipboardList },
            { title: 'Kartu Anggota', href: '/admin/kartu-anggota', icon: IdCard },
        ],
        user: [
            { title: 'Dasbor', href: '/app/dasbor', icon: LayoutGrid },
            { title: 'Jelajahi Buku', href: '/app/jelajahi', icon: Search },
            { title: 'Favorit', href: '/app/favorit', icon: Heart },
            { title: 'Markah', href: '/app/markah', icon: Bookmark },
        ],
    };

    return items[peran.value];
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="sidebarHome">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
