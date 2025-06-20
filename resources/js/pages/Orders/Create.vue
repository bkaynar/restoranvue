<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Table { id: number; name: string }
interface Product { id: number; name: string; price: number; stock: number }
interface Category { id: number; name: string; products: Product[] }

const props = defineProps<{ tables: Table[]; categories: Category[] }>();
const form = ref({
    table_id: props.tables.length > 0 ? props.tables[0].id : null,
    items: [] as { product_id: number; quantity: number }[],
    note: '',
});
const loading = ref(false);

function addItem(product: Product) {
    const existing = form.value.items.find(i => i.product_id === product.id);
    if (existing) {
        existing.quantity++;
    } else {
        form.value.items.push({ product_id: product.id, quantity: 1 });
    }
}
function removeItem(productId: number) {
    form.value.items = form.value.items.filter(i => i.product_id !== productId);
}
function updateQuantity(productId: number, quantity: number) {
    const item = form.value.items.find(i => i.product_id === productId);
    if (item) item.quantity = Math.max(1, quantity);
}
function submitOrder() {
    loading.value = true;
    router.post(route('orders.store'), form.value, {
        onFinish: () => { loading.value = false; }
    });
}
function getProductName(productId: number) {
    for (const cat of props.categories) {
        const product = cat.products.find(p => p.id === productId);
        if (product) return product.name;
    }
    return '';
}
</script>

<template>
    <AppLayout title="Yeni Sipariş Oluştur">
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Yeni Sipariş</h2>
                    <form @submit.prevent="submitOrder" class="flex flex-col gap-6">
                        <div>
                            <label class="block mb-1 font-medium">Masa</label>
                            <select v-model="form.table_id" required class="px-4 py-2 border rounded w-full">
                                <option v-for="table in tables" :key="table.id" :value="table.id">{{ table.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Ürünler</label>
                            <div v-for="cat in categories" :key="cat.id" class="mb-4">
                                <div class="font-semibold text-indigo-700 mb-2">{{ cat.name }}</div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <button v-for="product in cat.products" :key="product.id" type="button"
                                        @click="addItem(product)"
                                        class="px-4 py-2 border rounded flex justify-between items-center hover:bg-indigo-50 dark:hover:bg-indigo-900">
                                        <span>{{ product.name }}</span>
                                        <span class="text-xs text-gray-500">₺{{ product.price }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.items.length > 0">
                            <label class="block mb-1 font-medium">Sipariş Listesi</label>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">Ürün</th>
                                        <th class="px-4 py-2 text-center">Adet</th>
                                        <th class="px-4 py-2 text-center">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in form.items" :key="item.product_id">
                                        <td class="px-4 py-2">{{ getProductName(item.product_id) }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" min="1" v-model.number="item.quantity"
                                                @change="updateQuantity(item.product_id, item.quantity)"
                                                class="w-16 px-2 py-1 border rounded text-center" />
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" @click="removeItem(item.product_id)"
                                                class="text-red-600 hover:text-red-800">Kaldır</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Not (isteğe bağlı)</label>
                            <textarea v-model="form.note" class="w-full px-4 py-2 border rounded" rows="2"
                                placeholder="Sipariş notu..."></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <Link :href="route('orders.index')"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            İptal</Link>
                            <button type="submit" :disabled="loading || form.items.length === 0"
                                class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition disabled:opacity-50">
                                {{ loading ? 'Oluşturuluyor...' : 'Siparişi Oluştur' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
<style scoped></style>
