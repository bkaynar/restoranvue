<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';
import { ref, onMounted } from 'vue';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            roles: { name: string }[];
        };
    };
    stats: {
        totalTables: number;
        occupiedTables: number;
        activeOrders: number;
        dailyRevenue: number;
    };
    lastOrders: Array<{
        id: number;
        table: { id: number; name: string } | null;
        total: number;
        status: string;
        created_at: string;
    }>;
}>();
const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
const userHasRole = (role: string) => {
    return props.auth.user?.roles?.some(r => r.name === role);
};

const statCards = [
    { label: 'Toplam Masa', value: props.stats.totalTables, icon: 'table', iconBg: 'bg-blue-100', iconText: 'text-blue-700' },
    { label: 'Dolu Masa', value: props.stats.occupiedTables, icon: 'users', iconBg: 'bg-orange-100', iconText: 'text-orange-700' },
    { label: 'Aktif Sipariş', value: props.stats.activeOrders, icon: 'clipboard-list', iconBg: 'bg-green-100', iconText: 'text-green-700' },
    { label: 'Günlük Ciro', value: `${props.stats.dailyRevenue?.toLocaleString('tr-TR', { minimumFractionDigits: 2 })} ₺`, icon: 'currency-pound', iconBg: 'bg-purple-100', iconText: 'text-purple-700' },
];
const occupancy = props.stats.totalTables > 0 ? Math.round((props.stats.occupiedTables / props.stats.totalTables) * 100) : 0;

// Örnek saatlik satış verisi (görseldeki gibi)
const salesData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 14, 0, 0, 0];
const salesLabels = [
    '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
    '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '24:00'
];

const chartData = ref(null);

onMounted(async () => {
    try {
        const response = await fetch('/payments/hourly-sales');
        if (!response.ok) throw new Error('Veri alınamadı');
        const result = await response.json();
        chartData.value = {
            labels: result.labels,
            datasets: [
                {
                    label: 'Tutar(₺)',
                    data: result.data,
                    borderColor: '#1976d2',
                    backgroundColor: 'rgba(25, 118, 210, 0.1)',
                    pointBackgroundColor: '#ffa726',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                },
            ],
        };
    } catch (e) {
        console.error(e);
    }
});

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        title: {
            display: true,
            text: 'Günlük Satış Miktarları',
            align: 'start' as const, // Chart.js sadece 'start' | 'center' | 'end' kabul eder
            font: { size: 18 },
        },
        tooltip: {
            callbacks: {
                label: (ctx: any) => `Tutar: ₺${ctx.parsed.y}`,
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Tutar(₺)'
            }
        },
        x: {
            title: {
                display: false,
            }
        }
    },
};
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-6xl mx-12 py-8">
            <div v-if="userHasRole('yönetici')">
                <div
                    class="rounded-xl bg-white dark:bg-gray-900 p-12 mb-2 h-120 border border-gray-200 dark:border-gray-100">
                    <div v-if="!chartData" class="flex items-center justify-center h-32 text-gray-400">Yükleniyor...
                    </div>
                    <Line v-else :data="chartData" :options="chartOptions" class="mx-auto w-400" />
                </div>
                <!-- İstatistik Kutuları -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div v-for="stat in statCards" :key="stat.label"
                        class="rounded-xl p-5 flex flex-col gap-2 items-center shadow border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                        <span
                            :class="['inline-flex items-center justify-center w-10 h-10 rounded-full', stat.iconBg, stat.iconText]">
                            <template v-if="stat.icon === 'table'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <rect x="3" y="7" width="18" height="10" rx="2" />
                                    <path d="M3 10h18M7 7v10" />
                                </svg>
                            </template>
                            <template v-else-if="stat.icon === 'users'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87M16 3.13a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                                </svg>
                            </template>
                            <template v-else-if="stat.icon === 'clipboard-list'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <rect x="9" y="2" width="6" height="4" rx="1" />
                                    <path d="M4 7h16M4 7v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7" />
                                    <path d="M9 12h6M9 16h6" />
                                </svg>
                            </template>
                            <template v-else-if="stat.icon === 'currency-pound'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M17 17H7v-2a4 4 0 0 1 4-4h2a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4H7" />
                                </svg>
                            </template>
                        </span>
                        <div class="text-gray-800 dark:text-white text-lg font-bold">{{ stat.value }}</div>
                        <div class="text-gray-500 dark:text-white/80 text-sm">{{ stat.label }}</div>
                    </div>
                </div>
                <!-- Hızlı İşlemler -->
                <div class="rounded-xl bg-white dark:bg-gray-900 p-6 mb-6 border border-gray-200 dark:border-gray-800">
                    <div class="font-bold mb-4 text-gray-800 dark:text-white">Hızlı İşlemler</div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button
                            class="flex-1 px-4 py-3 rounded-lg bg-blue-600 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition"
                            @click="router.visit('/orders/create')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" />
                            </svg>
                            Yeni Sipariş
                        </button>
                        <button
                            class="flex-1 px-4 py-3 rounded-lg bg-green-600 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-green-700 transition"
                            @click="router.visit('/products/create')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" />
                            </svg>
                            Yeni Ürün

                        </button>
                        <!-- Ödeme Al Para İconu-->
                        <button
                            class="flex-1 px-4 py-3 rounded-lg bg-orange-500 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-orange-600 transition"
                            @click="router.visit('/payments/create')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" />
                            </svg> Ödeme Al
                        </button>
                        <button
                            class="flex-1 px-4 py-3 rounded-lg bg-red-500 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-red-600 transition"
                            @click="router.visit('/tables/create')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" />
                            </svg> Yeni Masa
                        </button>
                    </div>
                </div>

                <!-- Satış Grafiği -->

                <!-- Masalar ve Son Siparişler -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div class="rounded-xl bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
                        <div class="font-bold text-lg mb-2 text-gray-800 dark:text-white">Son Siparişler</div>
                        <div v-if="props.lastOrders.length === 0"
                            class="flex flex-col items-center justify-center h-full min-h-[80px]">
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <rect x="3" y="7" width="18" height="10" rx="2" />
                                <path d="M3 10h18M7 7v10" />
                            </svg>
                            <span class="text-gray-400">Henüz sipariş bulunmuyor</span>
                        </div>
                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-800">
                            <li v-for="order in props.lastOrders" :key="order.id"
                                class="py-2 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-white">#{{ order.id }}</span>
                                    <span class="ml-2 text-gray-500 dark:text-gray-400">{{ order.table?.name ||
                                        'MasaYok' }}</span>
                                    <span class="ml-2 text-xs text-gray-400">{{ new
                                        Date(order.created_at).toLocaleString('tr-TR')
                                    }}</span>
                                </div>
                                <div class="font-bold text-green-600 dark:text-green-400">₺{{
                                    Number(order.total).toFixed(2)
                                }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div v-else-if="userHasRole('garson')">
                <div
                    class="rounded-xl bg-white dark:bg-gray-900 p-6 mb-6 border border-gray-200 dark:border-gray-800 flex flex-col items-center">
                    <button
                        class="px-6 py-3 rounded-lg bg-blue-600 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition"
                        @click="router.visit('/orders/create')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        Yeni Sipariş
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.min-h-80 {
    min-height: 80px;
}
</style>
