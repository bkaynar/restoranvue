<template>
    <AppLayout>
        <div class="max-w m-2 p-2 px-2">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Aktif Siparişler</h1>
                <Link :href="route('orders.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">+ Sipariş Ekle</Link>
            </div>
            <div class="mb-4 flex gap-2">
                <input v-model="search" @input="onSearch" type="text" placeholder="Sipariş veya masa ara..."
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:ring focus:ring-blue-200" />
            </div>
            <div v-if="orders.data.length === 0"
                class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                <svg width="64" height="64" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="1.5"
                        d="M7 7h10M7 11h10M7 15h6M5 19V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2Z" />
                </svg>
                <div class="mt-2">Aktif sipariş yok.</div>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="order in orders.data.filter(o => !['iptal', 'ödendi', 'kapandı'].includes(o.status))"
                    :key="order.id"
                    class="rounded-lg shadow bg-white dark:bg-gray-900 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-center">
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 font-bold text-lg">{{
                                    order.table?.name?.charAt(0) || '-' }}</span>
                            <span class="text-xs text-gray-500 mt-1">Masa</span>
                        </div>
                        <div>
                            <div class="font-semibold text-lg text-gray-900 dark:text-white">{{ order.table?.name || '-'
                            }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold">
                                    <svg v-if="!order.user" class="w-5 h-5" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75a.75.75 0 01-.75.75h-13.5a.75.75 0 01-.75-.75v-.75z" />
                                    </svg>
                                    <span v-else>{{ order.user.name.charAt(0) }}</span>
                                </span>
                                <span>{{ order.user?.name || '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 md:items-end">
                        <span
                            :class="'inline-block px-3 py-1 rounded text-xs font-semibold ' + statusClass(order.status)">
                            {{ order.status }}
                        </span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">₺{{ typeof order.total ===
                            'number' ?
                            order.total.toFixed(2) : (parseFloat(order.total) || 0).toFixed(2) }}</span>
                        <span class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</span>
                        <div class="flex gap-2 mt-2">
                            <Link :href="route('orders.show', order.id)"
                                class="text-blue-600 dark:text-blue-400 hover:underline">Detay</Link>
                            <Link :href="route('orders.edit', order.id)"
                                class="text-yellow-600 dark:text-yellow-400 hover:underline">Düzenle</Link>
                            <Link v-if="order.status !== 'iptal' && order.status !== 'ödendi'"
                                :href="route('orders.cancel', order.id)" method="patch" as="button"
                                class="text-red-600 dark:text-red-400 hover:underline">İptal</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import dayjs from 'dayjs';
import AppLayout from '@/layouts/AppLayout.vue';
const props = defineProps({
    orders: Object
});
const search = ref('');
function formatDate(date) {
    return dayjs(date).format('DD.MM.YYYY HH:mm');
}
function statusClass(status) {
    switch (status) {
        case 'hazırlanıyor': return 'bg-yellow-100 text-yellow-800';
        case 'hazır': return 'bg-green-100 text-green-800';
        case 'teslim': return 'bg-blue-100 text-blue-800';
        case 'kapandı': return 'bg-gray-200 text-gray-500 line-through';
        case 'ödendi': return 'bg-green-200 text-green-900 font-bold';
        case 'iptal': return 'bg-red-100 text-red-700 line-through';
        default: return 'bg-gray-100 text-gray-700';
    }
}
function onSearch() {
    router.get(route('orders.index'), { search }, { preserveState: true, replace: true });
}
</script>

<style scoped>
input:focus {
    outline: none;
    box-shadow: 0 0 0 2px #2563eb33;
}
</style>
