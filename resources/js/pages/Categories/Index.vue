<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Category {
    id: number;
    name: string;
    description?: string;
}

const props = defineProps<{ categories: Category[] }>();
const categories = ref<Category[]>(props.categories);
const newCategory = ref({ name: '', description: '' });
const loading = ref(false);
const deleteConfirmation = ref(false);
const selectedCategory = ref<Category | null>(null);

const addCategory = () => {
    loading.value = true;
    router.post(route('categories.store'), newCategory.value, {
        onSuccess: () => {
            newCategory.value = { name: '', description: '' };
        },
        onFinish: () => {
            loading.value = false;
        },
        preserveState: false, // Sayfa tamamen yenilensin, yeni kategoriler gelsin
        preserveScroll: true,
    });
};

const confirmDelete = (category: Category) => {
    selectedCategory.value = category;
    deleteConfirmation.value = true;
};

const closeDeleteModal = () => {
    deleteConfirmation.value = false;
    selectedCategory.value = null;
};

const deleteCategory = () => {
    if (!selectedCategory.value) return;
    loading.value = true;
    const deletedId = selectedCategory.value.id;
    router.delete(route('categories.destroy', deletedId), {
        onSuccess: () => {
            // Kategori silindikten sonra listeden çıkar
            categories.value = categories.value.filter(cat => cat.id !== deletedId);
        },
        onFinish: () => {
            loading.value = false;
            closeDeleteModal();
        },
    });
};
</script>

<template>
    <AppLayout title="Kategoriler">
        <section class="py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Kategoriler</h2>
                    <form @submit.prevent="addCategory" class="flex flex-col sm:flex-row gap-4 mb-8">
                        <input v-model="newCategory.name" type="text" placeholder="Kategori Adı" required
                            class="flex-1 px-4 py-2 border rounded" />
                        <input v-model="newCategory.description" type="text" placeholder="Açıklama (isteğe bağlı)"
                            class="flex-1 px-4 py-2 border rounded" />
                        <button type="submit" :disabled="loading"
                            class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition disabled:opacity-50">
                            {{ loading ? 'Ekleniyor...' : 'Ekle' }}
                        </button>
                    </form>
                    <div v-if="categories.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-10">
                        Henüz kategori yok.
                    </div>
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="category in categories" :key="category.id"
                            class="flex justify-between items-center py-4">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ category.name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ category.description ||
                                    'Açıklama yok' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <Link :href="route('categories.edit', category.id)"
                                    class="text-green-600 hover:text-green-800">✏️</Link>
                                <button @click="confirmDelete(category)"
                                    class="text-red-600 hover:text-red-800">🗑️</button>
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kategoriyi Sil</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                        <strong>{{ selectedCategory?.name }}</strong> kategorisini silmek istiyor musunuz? Bu işlem geri
                        alınamaz.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="closeDeleteModal"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">İptal</button>
                        <button @click="deleteCategory" :disabled="loading"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50">
                            {{ loading ? 'Siliniyor...' : 'Evet, Sil' }}
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
