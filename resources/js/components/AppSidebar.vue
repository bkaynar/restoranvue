<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { usePage } from '@inertiajs/vue3';

const mainNavItems: NavItem[] = [
    {
        title: 'Ana Sayfa',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Masa Yönetimi',
        href: '/tables',
        icon: LayoutGrid,
    },
    {
        title: 'Kategoriler',
        href: '/categories',
        icon: LayoutGrid,
    },
    {
        title: 'Ürünler',
        href: '/products',
        icon: LayoutGrid,
    },
    {
        title: 'Siparişler',
        href: '/orders',
        icon: LayoutGrid,
    },
    {
        title: 'Ödeme Al',
        href: '/payments/create',
        icon: LayoutGrid,
    },
    {
        title: 'Kullanıcılar',
        href: '/users',
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [

];

const page = usePage();
const user = page.props.auth.user as any;

const isAdmin = user?.roles?.some((r: any) => r.name === 'yönetici');
const isWaiter = user?.roles?.some((r: any) => r.name === 'garson');

const filteredNavItems = isAdmin
    ? mainNavItems
    : isWaiter
        ? [
            {
                title: 'Siparişler',
                href: '/orders',
                icon: LayoutGrid,
            },
        ]
        : [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
