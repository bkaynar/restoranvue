<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, Order } from '@/types';

const props = defineProps<{
  table: Table & { active_order?: Order };
}>();

const table = ref(props.table);
const loading = ref(false);
const showNewOrderForm = ref(!props.table.active_order);
const orderNote = ref('');

const hasActiveOrder = computed(() => !!table.value.active_order);

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
      router.reload();
    } else {
      alert(data.message || 'Bir hata oluştu');
    }
  } catch (error) {
    alert('Sipariş oluşturulurken bir hata oluştu');
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <AppLayout :title="`Masa: ${table.name}`">
    <div class="py-10">
      <div class="max-w-2xl mx-auto space-y-8">
        <div class="flex items-center justify-between gap-4 mb-4">
          <div class="flex items-center gap-3">
            <span
              class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-700 text-2xl font-bold">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </span>
            <div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Masa: {{ table.name }}</h2>
              <div class="text-gray-500 text-sm">{{ table.description || 'Açıklama yok' }}</div>
            </div>
          </div>
          <Link :href="route('tables.index')"
            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">Geri Dön</Link>
        </div>

        <div v-if="hasActiveOrder"
          class="rounded-lg shadow bg-white dark:bg-gray-900 p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-green-700 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4 -4" />
              </svg>
              Aktif Sipariş
            </h3>
            <Link :href="`/orders/${table.active_order.id}`"
              class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Siparişi Görüntüle</Link>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <div>
              <div class="text-xs text-gray-500">Sipariş ID</div>
              <div class="font-bold">#{{ table.active_order.id }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500">Durum</div>
              <div class="font-bold capitalize">{{ table.active_order.status }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500">Toplam</div>
              <div class="font-bold">₺{{ table.active_order.total ? Number(table.active_order.total).toFixed(2) :
                'Hesaplanıyor...' }}</div>
            </div>
          </div>
          <div v-if="table.active_order.note" class="mb-2">
            <div class="text-xs text-gray-500">Not</div>
            <div class="font-medium text-gray-700 dark:text-gray-300">{{ table.active_order.note }}</div>
          </div>
          <div v-if="table.active_order.items && table.active_order.items.length > 0" class="mt-4">
            <div class="text-xs text-gray-500 mb-2">Sipariş Edilen Ürünler</div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th class="px-4 py-2 text-left">Ürün</th>
                    <th class="px-4 py-2 text-center">Adet</th>
                    <th class="px-4 py-2 text-center">Birim Fiyat</th>
                    <th class="px-4 py-2 text-center">Tutar</th>
                    <th class="px-4 py-2 text-center">Durum</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                  <tr v-for="item in table.active_order.items" :key="item.id">
                    <td class="px-4 py-2">{{ item.product.name }}</td>
                    <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                    <td class="px-4 py-2 text-center">₺{{ Number(item.price).toFixed(2) }}</td>
                    <td class="px-4 py-2 text-center">₺{{ (Number(item.quantity) * Number(item.price)).toFixed(2) }}
                    </td>
                    <td class="px-4 py-2 text-center capitalize">{{ item.status }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-else class="rounded-lg shadow bg-white dark:bg-gray-900 p-6 border border-gray-100 dark:border-gray-800">

          <form @submit.prevent="createNewOrder" class="space-y-4">

            <div class="flex justify-end">
              <Link :href="route('orders.create', { table_id: table.id })"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
              Sipariş Oluştur
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Modern ve sade görünüm için ek stiller */
th,
td {
  white-space: nowrap;
}
</style>
