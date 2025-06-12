<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
    image_path?: string;
}

const props = defineProps<{ product: Product; categories: Category[] }>();
const form = ref({
    name: props.product.name,
    description: props.product.description || '',
    price: props.product.price,
    stock: props.product.stock,
    category_id: props.product.category_id,
    is_active: props.product.is_active,
    image: null as File | null,
});
const loading = ref(false);
const imagePreview = ref<string | null>(props.product.image_path ? `/storage/${props.product.image_path}` : null);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.value.image = target.files[0];
        imagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const removeImage = () => {
    form.value.image = null;
    imagePreview.value = null;
};

const updateProduct = () => {
    loading.value = true;
    const data = new FormData();
    data.append('name', form.value.name);
    data.append('description', form.value.description);
    data.append('price', String(form.value.price));
    data.append('stock', String(form.value.stock));
    data.append('category_id', String(form.value.category_id));
    data.append('is_active', form.value.is_active ? '1' : '0');
    if (form.value.image) {
        data.append('image', form.value.image);
    }
    router.post(route('products.update', props.product.id), data, {
        forceFormData: true,
        method: 'post',
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <AppLayout title="Ürün Düzenle">
        <section class="py-10">
            <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Ürün Düzenle</h2>
                    <form @submit.prevent="updateProduct" class="flex flex-col gap-4" enctype="multipart/form-data">
                        <input v-model="form.name" type="text" placeholder="Ürün Adı" required
                            class="px-4 py-2 border rounded" />
                        <textarea v-model="form.description" placeholder="Açıklama (isteğe bağlı)"
                            class="px-4 py-2 border rounded"></textarea>
                        <input v-model="form.price" type="number" min="0" step="0.01" placeholder="Fiyat" required
                            class="px-4 py-2 border rounded" />
                        <input v-model="form.stock" type="number" min="0" step="1" placeholder="Stok" required
                            class="px-4 py-2 border rounded" />
                        <select v-model="form.category_id" required class="px-4 py-2 border rounded">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Ürün
                                Görseli</label>
                            <div class="flex items-center gap-4">
                                <div v-if="imagePreview"
                                    class="w-20 h-20 rounded border overflow-hidden flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                                    <img :src="imagePreview || undefined" alt="Ürün Fotoğrafı"
                                        class="object-cover w-full h-full" />
                                </div>
                                <label
                                    class="cursor-pointer px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition block">
                                    <input type="file" accept="image/*" @change="handleFileChange" class="hidden" />
                                    Fotoğraf Seç
                                </label>
                                <button v-if="form.image" type="button" @click="removeImage"
                                    class="text-xs text-red-500 hover:underline ml-2">Kaldır</button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG veya WEBP. Maksimum 2MB.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input v-model="form.is_active" type="checkbox" id="is_active" class="h-4 w-4" />
                            <label for="is_active" class="text-sm">Aktif</label>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <Link :href="route('products.index')"
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
