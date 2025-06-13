<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table } from '@/types';

const props = defineProps<{ tables: Table[] }>();
const tables = ref<Table[]>(props.tables);
const loading = ref(false);
const deleteConfirmation = ref(false);
const selectedTable = ref<Table | null>(null);

const deleteTable = () => {
    if (!selectedTable.value) return;
    loading.value = true;
    const id = selectedTable.value.id;
    router.delete(route('tables.destroy', id), {
        onFinish: () => {
            loading.value = false;
            closeDeleteModal();
        },
    });
};

const confirmDelete = (table: Table) => {
    selectedTable.value = table;
    deleteConfirmation.value = true;
};

const closeDeleteModal = () => {
    deleteConfirmation.value = false;
    selectedTable.value = null;
};
</script>

<template>
    <AppLayout title="Masalar">
        <section class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <div v-if="tables.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Henüz masa bulunmuyor. Yeni masa ekleyerek başlayabilirsiniz.
                    </div>

                    <div v-else class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                        <div v-for="table in tables" :key="table.id"
                            class="relative p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    {{ table.name }}
                                </h3>
                                <div class="flex gap-2">
                                    <Link :href="route('tables.show', table.id)"
                                        class="text-blue-600 hover:text-blue-800">
                                    🔍
                                    </Link>
                                    <Link :href="route('tables.edit', table.id)"
                                        class="text-green-600 hover:text-green-800">
                                    ✏️
                                    </Link>
                                    <button @click="confirmDelete(table)" class="text-red-600 hover:text-red-800">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                {{ table.description || 'Açıklama yok' }}
                            </p>
                            <div class="mt-4 flex justify-between text-xs text-gray-500 dark:text-gray-300">
                                <span :class="table.orders_count > 0 ? 'text-yellow-600' : 'text-green-600'">
                                    {{ table.orders_count > 0 ? 'Aktif Sipariş Var' : 'Boş Masa' }}
                                </span>
                                <Link :href="route('tables.show', table.id)"
                                    class="hover:underline text-indigo-600 hover:text-indigo-800">
                                {{ table.orders_count > 0 ? 'Siparişi Gör' : 'Yeni Sipariş' }}
                                </Link>
                            </div>
                        </div>
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Masayı Sil</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                        <strong>{{ selectedTable?.name }}</strong> masasını silmek istiyor musunuz? Bu işlem geri
                        alınamaz.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="closeDeleteModal"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            ❌ İptal
                        </button>
                        <button @click="deleteTable" :disabled="loading"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50">
                            {{ loading ? 'Siliniyor...' : '✔ Evet, Sil' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
