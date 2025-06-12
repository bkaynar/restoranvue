<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Category {
    id: number;
    name: string;
    description?: string;
}

const props = defineProps<{ category: Category }>();
const form = ref({
    name: props.category.name,
    description: props.category.description || ''
});
const loading = ref(false);

const updateCategory = () => {
    loading.value = true;
    router.put(route('categories.update', props.category.id), form.value, {
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <AppLayout title="Kategori Düzenle">
        <section class="py-10">
            <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Kategori Düzenle</h2>
                    <form @submit.prevent="updateCategory" class="flex flex-col gap-4">
                        <input v-model="form.name" type="text" placeholder="Kategori Adı" required
                            class="px-4 py-2 border rounded" />
                        <input v-model="form.description" type="text" placeholder="Açıklama (isteğe bağlı)"
                            class="px-4 py-2 border rounded" />
                        <div class="flex gap-2 justify-end">
                            <Link :href="route('categories.index')"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            İptal</Link>
                            <button type="submit" :disabled="loading"
                                class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition disabled:opacity-50">
                                {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped></style>
