<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
// import { Table } from '@/types'; // Removed because 'Table' is not exported from '@/types'

const props = defineProps<{
  table: any; // Changed from Table to any to avoid type error
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
      <div class="flex justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Masa Düzenle: {{ table.name }}
        </h2>
        <Link :href="route('tables.index')" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-300">
          Geri Dön
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            
            <form @submit.prevent="submit">
              <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700">Masa Adı</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  id="name" 
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              
              <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                <textarea 
                  v-model="form.description" 
                  id="description" 
                  rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
              </div>
              
              <div class="flex items-center justify-end">
                <button 
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300 disabled:opacity-50"
                  :disabled="form.processing"
                >
                  {{ form.processing ? 'Güncelleniyor...' : 'Güncelle' }}
                </button>
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
