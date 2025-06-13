<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  table: any;
}>();

// Form verilerini tanımla
const form = useForm({
  name: props.table.name,
  description: props.table.description || ''
});

// Form gönderme fonksiyonu
const submit = () => {
  form.put(route('tables.update', props.table.id), {
    onSuccess: () => {
      // Başarıyla güncellendiğinde bir şey yapma (otomatik redirect olacak)
    }
  });
};
</script>

<template>
  <AppLayout title="Masa Düzenle">
    <template #header>
      <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-white flex items-center gap-2">
          <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <path d="M8 12h8M12 8v8" />
          </svg>
          Masa Düzenle: <span class="text-indigo-600">{{ table.name }}</span>
        </h2>
        <Link :href="route('tables.index')"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold shadow">
        Geri Dön
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl p-8 border border-gray-100 dark:border-gray-800">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="name" class="block text-base font-medium text-gray-700 dark:text-gray-200 mb-1">Masa
                Adı</label>
              <input v-model="form.name" type="text" id="name"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2"
                required placeholder="Masa adı giriniz">
              <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
            </div>
            <div>
              <label for="description"
                class="block text-base font-medium text-gray-700 dark:text-gray-200 mb-1">Açıklama</label>
              <textarea v-model="form.description" id="description" rows="3"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2"
                placeholder="Açıklama (isteğe bağlı)"></textarea>
              <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
            </div>
            <div class="flex items-center justify-end gap-2">
              <Link :href="route('tables.index')"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
              İptal
              </Link>
              <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50 shadow"
                :disabled="form.processing">
                {{ form.processing ? 'Güncelleniyor...' : 'Güncelle' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
