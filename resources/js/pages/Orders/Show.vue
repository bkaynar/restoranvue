<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

interface Product {
    id: number;
    name: string;
    price: number;
}
interface OrderItem {
    id: number;
    product: Product;
    quantity: number;
    price: number;
    status: string;
}
interface Table { id: number; name: string }
interface User { id: number; name: string }
interface Payment { id: number; amount: number; status: string }

const props = defineProps<{
    order: {
        id: number;
        table: Table;
        user: User;
        status: string;
        total: number;
        note: string;
        created_at: string;
        items: OrderItem[];
        payments: Payment[];
    }
}>();

const totalPaid = computed(() => props.order.payments?.filter(p => p.status === 'ödendi').reduce((sum, p) => sum + p.amount, 0) || 0);
const remaining = computed(() => (props.order.total || 0) - totalPaid.value);
</script>

<template>
    <AppLayout>
        <div class="max-w-3xl mx-auto py-8 px-2">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sipariş Detayı</h1>
                <Link :href="route('orders.index')" class="text-blue-600 hover:underline">← Geri</Link>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded shadow p-6">
                <div class="mb-4 flex flex-col md:flex-row md:items-center md:gap-8">
                    <div>
                        <div class="font-semibold">Masa:</div>
                        <div>{{ order.table?.name || '-' }}</div>
                    </div>
                    <div>
                        <div class="font-semibold">Oluşturan:</div>
                        <div>{{ order.user?.name || '-' }}</div>
                    </div>
                    <div>
                        <div class="font-semibold">Durum:</div>
                        <div>{{ order.status }}</div>
                    </div>
                    <div>
                        <div class="font-semibold">Tarih:</div>
                        <div>{{ new Date(order.created_at).toLocaleString('tr-TR') }}</div>
                    </div>
                </div>
                <div v-if="order.note" class="mb-4">
                    <div class="font-semibold">Not:</div>
                    <div class="text-gray-700 dark:text-gray-300">{{ order.note }}</div>
                </div>
                <div class="mb-6">
                    <div class="font-semibold mb-2">Ürünler</div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Ürün</th>
                                <th class="px-4 py-2 text-center">Adet</th>
                                <th class="px-4 py-2 text-center">Birim Fiyat</th>
                                <th class="px-4 py-2 text-center">Tutar</th>
                                <th class="px-4 py-2 text-center">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in order.items" :key="item.id">
                                <td class="px-4 py-2">{{ item.product?.name || '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                                <td class="px-4 py-2 text-center">₺{{ Number(item.price).toFixed(2) }}</td>
                                <td class="px-4 py-2 text-center">₺{{ (Number(item.price) *
                                    Number(item.quantity)).toFixed(2) }}</td>
                                <td class="px-4 py-2 text-center">{{ item.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col md:flex-row md:justify-between gap-4">
                    <div>
                        <div class="font-semibold">Toplam Tutar:</div>
                        <div class="text-lg font-bold">₺{{ Number(order.total).toFixed(2) }}</div>
                    </div>
                    <div>
                        <div class="font-semibold">Ödenen:</div>
                        <div>₺{{ totalPaid.toFixed(2) }}</div>
                    </div>
                    <div>
                        <div class="font-semibold">Kalan:</div>
                        <div>₺{{ remaining.toFixed(2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
