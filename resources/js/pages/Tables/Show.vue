<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, Order } from '@/types';

const props = defineProps<{
  table: Table & { active_order?: Order };
}>();

// Kullanılacak değişkenleri tanımla
const table = ref(props.table);
const loading = ref(false);
const showNewOrderForm = ref(!props.table.active_order);
const orderNote = ref('');

// Masanın aktif bir siparişi olup olmadığını kontrol et
const hasActiveOrder = computed(() => {
  return !!table.value.active_order;
});

// Yeni sipariş oluştur
const createNewOrder = async () => {
  loading.value = true;
  
  try {
    const response = await fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        table_id: table.value.id,
        note: orderNote.value
      })
    });
    
    const data = await response.json();
    
    if (response.ok) {
      // Sayfayı yeniden yükle ve yeni oluşturulan siparişi göster
      router.reload();
    } else {
      alert(data.message || 'Bir hata oluştu');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Sipariş oluşturulurken bir hata oluştu');
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <AppLayout :title="`Masa: ${table.name}`">
    <template #header>
      <div class="flex justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Masa: {{ table.name }}
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
            
            <!-- Masa Bilgileri -->
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Masa Bilgileri</h3>
              <div class="bg-gray-50 p-4 rounded-md">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Masa Adı</p>
                    <p class="font-medium">{{ table.name }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Açıklama</p>
                    <p class="font-medium">{{ table.description || 'Açıklama yok' }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Aktif Sipariş Bilgisi -->
            <div v-if="hasActiveOrder" class="mb-6">
              <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-medium">Aktif Sipariş</h3>
                <div class="flex space-x-2">
                  <Link :href="`/orders/${table.active_order.id}`" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                    Siparişi Görüntüle
                  </Link>
                </div>
              </div>
              
              <div class="bg-green-50 border border-green-200 p-4 rounded-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Sipariş ID</p>
                    <p class="font-medium">#{{ table.active_order.id }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Durum</p>
                    <p class="font-medium capitalize">{{ table.active_order.status }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Toplam</p>
                    <p class="font-medium">{{ table.active_order.total ? `₺${table.active_order.total}` : 'Hesaplanıyor...' }}</p>
                  </div>
                </div>
                
                <div v-if="table.active_order.note" class="mt-4">
                  <p class="text-sm text-gray-600">Not</p>
                  <p class="font-medium">{{ table.active_order.note }}</p>
                </div>
                
                <div v-if="table.active_order.items && table.active_order.items.length > 0" class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Sipariş Edilen Ürünler</p>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adet</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Birim Fiyat</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in table.active_order.items" :key="item.id">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.product.name }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.quantity }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ item.price }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ (item.quantity * item.price).toFixed(2) }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ item.status }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Yeni Sipariş Oluştur -->
            <div v-if="!hasActiveOrder" class="mb-6">
              <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-medium">Yeni Sipariş Oluştur</h3>
              </div>
              
              <form @submit.prevent="createNewOrder" class="bg-gray-50 p-4 rounded-md">
                <div class="mb-4">
                  <label for="note" class="block text-sm font-medium text-gray-700">Sipariş Notu</label>
                  <textarea 
                    v-model="orderNote" 
                    id="note" 
                    rows="2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Sipariş için özel notlar..."
                  ></textarea>
                </div>
                
                <div class="flex justify-end">
                  <button 
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300 disabled:opacity-50"
                    :disabled="loading"
                  >
                    {{ loading ? 'Oluşturuluyor...' : 'Sipariş Oluştur' }}
                  </button>
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
