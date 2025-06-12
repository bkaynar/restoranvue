<script setup lang="ts">

import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Category {
    id: number;
    name: string;
}
interface Product {
    id: number;
    name: string;
    description?: string;
    price: number;
    stock: number;
    is_active: boolean;
    category_id: number;
    category?: Category;
}

const props = defineProps<{ categories: Category[]; products: Product[] }>();
const selectedCategoryId = ref<number | null>(null);
const deleteConfirmation = ref(false);
const selectedProduct = ref<Product | null>(null);
const loading = ref(false);

const filteredProducts = computed(() => {
    if (!selectedCategoryId.value) return props.products;
    return props.products.filter(p => p.category_id === selectedCategoryId.value);
});

function toggleActive(product: Product) {
    router.patch(route('products.toggle', product.id));
}

function confirmDelete(product: Product) {
    selectedProduct.value = product;
    deleteConfirmation.value = true;
}

function closeDeleteModal() {
    deleteConfirmation.value = false;
    selectedProduct.value = null;
}

function deleteProduct() {
    if (!selectedProduct.value) return;
    loading.value = true;
    router.delete(route('products.destroy', selectedProduct.value.id), {
        onFinish: () => {
            loading.value = false;
            deleteConfirmation.value = false;
            selectedProduct.value = null;
        },
    });
}
</script>

<template>
    <AppLayout title="Ürünler (Kategoriye Göre)">
        <section class="py-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                        <h2 class="text-2xl font-bold">Ürünler</h2>
                        <div class="flex gap-2 w-full md:w-auto">
                            <select v-model="selectedCategoryId" class="px-4 py-2 border rounded w-full md:w-64">
                                <option :value="null">Tüm Kategoriler</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <Link :href="route('products.create')"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition whitespace-nowrap">
                            + Yeni Ürün Ekle
                            </Link>
                        </div>
                    </div>
                    <div v-if="filteredProducts.length === 0"
                        class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Seçilen kategoriye ait ürün bulunamadı.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Adı</th>
                                    <th class="px-4 py-2 text-left">Açıklama</th>
                                    <th class="px-4 py-2 text-left">Fiyat</th>
                                    <th class="px-4 py-2 text-left">Stok</th>
                                    <th class="px-4 py-2 text-left">Kategori</th>
                                    <th class="px-4 py-2 text-center">Aktif</th>
                                    <th class="px-4 py-2 text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in filteredProducts" :key="product.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="px-4 py-2 font-semibold">{{ product.name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{
                                        product.description || '-' }}</td>
                                    <td class="px-4 py-2">₺{{ product.price }}</td>
                                    <td class="px-4 py-2">{{ product.stock }}</td>
                                    <td class="px-4 py-2">{{ product.category?.name }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <button @click="toggleActive(product)"
                                            :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'"
                                            class="px-3 py-1 rounded-full text-xs font-bold">
                                            {{ product.is_active ? 'Aktif' : 'Pasif' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 text-center flex gap-2 justify-center">
                                        <Link :href="route('products.edit', product.id)"
                                            class="text-green-600 hover:text-green-800">✏️</Link>
                                        <button @click="confirmDelete(product)"
                                            class="text-red-600 hover:text-red-800">🗑️</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- Silme Onay Modalı -->
        <transition name="fade">
            <div v-if="deleteConfirmation"
                class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
                aria-modal="true" role="dialog">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-sm w-full">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Ürünü Sil</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                        <strong>{{ selectedProduct?.name }}</strong> ürününü silmek istiyor musunuz? Bu işlem geri
                        alınamaz.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="closeDeleteModal"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">İptal</button>
                        <button @click="deleteProduct" :disabled="loading"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50">
                            {{ loading ? 'Siliniyor...' : 'Evet, Sil' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<style scoped></style>
