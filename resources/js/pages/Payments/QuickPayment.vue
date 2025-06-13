<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Table { id: number; name: string; active_order?: { id: number; total: number; status: string } }

const props = defineProps<{ tables: Table[] }>();

const paymentType = ref('nakit');
const loading = ref(false);
const selectedOrder = ref<{ id: number; total: number; status: string } | null>(null);
const showConfirm = ref(false);
const partialAmount = ref('');
const amountError = ref('');

const paymentTypes = [
    { value: 'nakit', label: 'Nakit' },
    { value: 'kredi_karti', label: 'Kredi Kartı' },
    { value: 'yemek_karti', label: 'Yemek Kartı' },
    { value: 'banka', label: 'Banka Havalesi' },
];

function openPayment(order: any) {
    selectedOrder.value = order;
}

function closePayment() {
    selectedOrder.value = null;
}

function submitPayment() {
    if (!selectedOrder.value) return;
    const amount = partialAmount.value ? Number(partialAmount.value) : selectedOrder.value.total;
    if (isNaN(amount) || amount <= 0) {
        amountError.value = 'Geçerli bir tutar girin.';
        return;
    }
    if (amount > selectedOrder.value.total) {
        amountError.value = 'Girilen tutar sipariş toplamını aşamaz.';
        return;
    }
    amountError.value = '';
    loading.value = true;
    router.post(route('payments.store'), {
        order_id: selectedOrder.value.id,
        payment_method: paymentType.value,
        amount,
    }, {
        onFinish: () => loading.value = false,
        onSuccess: () => {
            closePayment();
            router.reload();
        },
    });
}

function confirmAndSubmitPayment() {
    showConfirm.value = true;
}

function handleConfirm(result: boolean) {
    showConfirm.value = false;
    if (result) {
        submitPayment();
    }
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto py-10">
            <h1 class="text-2xl font-bold mb-6 text-center">Aktif Masalar</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div v-for="table in tables.filter(t => t.active_order)" :key="table.id"
                    class="rounded-lg shadow bg-white dark:bg-gray-900 p-6 border border-gray-100 dark:border-gray-800 cursor-pointer hover:shadow-lg transition"
                    @click="openPayment(table.active_order)">
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 text-xl font-bold">{{
                                table.name.charAt(0) }}</span>
                        <div>
                            <div class="font-semibold text-lg">{{ table.name }}</div>
                            <div class="text-xs text-gray-500">Sipariş ID: #{{ table.active_order?.id }}</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="capitalize text-gray-500 text-sm">Toplam Tutar:
                        </span>
                        <span class="font-bold text-sm">₺{{ Number(table.active_order?.total).toFixed(2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Ödeme Modalı -->
            <div v-if="selectedOrder"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-8 w-full max-w-md relative">
                    <button @click="closePayment"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
                    <h2 class="text-xl font-bold mb-4">Ödeme Al</h2>
                    <div class="mb-4 flex justify-between">
                        <span class="font-medium">Sipariş Tutarı:</span>
                        <span class="font-bold text-lg">₺{{ Number(selectedOrder.total).toFixed(2) }}</span>
                    </div>
                    <div class="mb-4 flex justify-between">
                        <span class="font-medium">Durum:</span>
                        <span class="capitalize">{{ selectedOrder.status }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Ödeme Türü</label>
                        <select v-model="paymentType" class="w-full border rounded px-3 py-2">
                            <option v-for="type in paymentTypes" :key="type.value" :value="type.value">{{ type.label }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Ödenecek Tutar</label>
                        <input v-model="partialAmount" type="number" min="0.01" :max="selectedOrder.total" step="0.01"
                            class="w-full border rounded px-3 py-2"
                            placeholder="Tutar girin (boş bırakılırsa tam tutar)" />
                        <div v-if="amountError" class="text-red-500 text-xs mt-1">{{ amountError }}</div>
                    </div>
                    <button :disabled="loading" @click="confirmAndSubmitPayment"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition disabled:opacity-50">
                        {{ loading ? 'İşleniyor...' : 'Ödemeyi Tamamla ve Masayı Boşa Düşür' }}
                    </button>

                    <!-- Canlı Onay Popup -->
                    <div v-if="showConfirm"
                        class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
                            <div class="text-2xl mb-3 text-yellow-500">
                                <svg xmlns='http://www.w3.org/2000/svg' class='inline w-10 h-10' fill='none'
                                    viewBox='0 0 24 24' stroke='currentColor'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                                        d='M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z' />
                                </svg>
                            </div>
                            <div class="font-bold text-lg mb-2">Ödeme işlemini onaylıyor musunuz?</div>
                            <div class="text-gray-500 mb-4">Bu işlem geri alınamaz. Ödeme tamamlanacak ve masa boşa
                                düşecek.</div>
                            <div class="flex gap-4 justify-center">
                                <button @click="handleConfirm(true)"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Evet,
                                    Onayla</button>
                                <button @click="handleConfirm(false)"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Vazgeç</button>
                            </div>
                        </div>
                    </div>
                    <!-- /Canlı Onay Popup -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
select:focus,
button:focus {
    outline: none;
    box-shadow: 0 0 0 2px #2563eb33;
}
</style>
