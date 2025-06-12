<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Form verilerini tanımla
const form = useForm({
    name: '',
    description: ''
});

// Durum değişkenleri
const showSuccess = ref(false);
const showError = ref(false);
const nameLength = ref(0);
const isSubmitting = ref(false);

// İsim alanının karakter sayısını takip et
watch(() => form.name, (value) => {
    nameLength.value = value.length;
});

// Form gönderme fonksiyonu
const submit = () => {
    isSubmitting.value = true;
    form.post(route('tables.store'), {
        onSuccess: () => {
            form.reset();
            nameLength.value = 0;
            showSuccess.value = true;
            showError.value = false;
            isSubmitting.value = false;

            // 3 saniye sonra başarı mesajını kaldır
            setTimeout(() => {
                showSuccess.value = false;
            }, 3000);
        },
        onError: () => {
            showError.value = true;
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <AppLayout title="Yeni Masa Ekle">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Yeni Masa Ekle
                </h2>
                <Link :href="route('tables.index')"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-300 shadow-sm">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Geri Dön
                </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Bildirimler -->
                <div v-if="showSuccess"
                    class="mb-6 flex p-4 border-l-4 border-green-500 bg-green-50 dark:bg-green-900/20 dark:border-green-600 rounded-md shadow-sm transition-all duration-500 ease-in-out">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-700 dark:text-green-300">Başarılı!</h3>
                        <div class="mt-1 text-sm text-green-600 dark:text-green-200">
                            Yeni masa başarıyla eklendi. Masalar listesinden görüntüleyebilirsiniz.
                        </div>
                    </div>
                </div>

                <div v-if="showError"
                    class="mb-6 flex p-4 border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-600 rounded-md shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-700 dark:text-red-300">Hata!</h3>
                        <div class="mt-1 text-sm text-red-600 dark:text-red-200">
                            Masa eklenirken bir hata oluştu. Lütfen formu kontrol edip tekrar deneyin.
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl">
                    <!-- Form başlık bölümü -->
                    <div
                        class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-950/40 dark:to-blue-950/30">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                class="w-6 h-6 mr-2 text-indigo-600 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            Yeni Masa Ekle
                        </h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Restoranınıza yeni bir masa ekleyin. Masalar için açık ve anlaşılır isimler kullanmanız
                            önerilir.
                        </p>
                    </div>

                    <!-- Form içerik bölümü -->
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Masa Adı -->
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Masa Adı <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ nameLength }}/50 karakter
                                    </span>
                                </div>

                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" class="w-5 h-5"
                                            :class="form.errors.name ? 'text-red-500 dark:text-red-400' : 'text-gray-400 dark:text-gray-500'">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </div>

                                    <input v-model="form.name" type="text" id="name" maxlength="50"
                                        class="pl-10 mt-1 block w-full rounded-md shadow-sm transition duration-150 focus:ring-2 focus:ring-offset-0"
                                        :class="[
                                            form.errors.name
                                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500 dark:border-red-600 dark:bg-gray-800'
                                                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:ring-indigo-500'
                                        ]" required autocomplete="off"
                                        placeholder="Örn: Masa 1, Teras-2, VIP-3, Bahçe-4" autofocus>

                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                                        v-if="form.name.length > 0">
                                        <svg v-if="!form.errors.name" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5 text-green-600 dark:text-green-400">
                                            <path fill-rule="evenodd"
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5 text-red-600 dark:text-red-400">
                                            <path fill-rule="evenodd"
                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <div v-if="form.errors.name"
                                    class="text-sm text-red-600 dark:text-red-400 flex items-center mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4 mr-1 flex-shrink-0">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.name }}
                                </div>

                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Masa adı personelin masayı kolayca tanıması ve müşterilere yönlendirmelerde
                                    kullanılacaktır.
                                </p>
                            </div>

                            <!-- Açıklama -->
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Açıklama <span class="text-xs text-gray-500 dark:text-gray-400">(İsteğe
                                            bağlı)</span>
                                    </label>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ form.description.length }}/200 karakter
                                    </span>
                                </div>

                                <div class="mt-1">
                                    <textarea v-model="form.description" id="description" rows="3" maxlength="200"
                                        class="block w-full rounded-md shadow-sm transition duration-150 focus:ring-2 focus:ring-offset-0 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:ring-indigo-500"
                                        placeholder="Masa hakkında ek bilgi girin (konum, özellikler, kapasite vb.)"></textarea>
                                </div>

                                <div v-if="form.errors.description"
                                    class="text-sm text-red-600 dark:text-red-400 flex items-center mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4 mr-1 flex-shrink-0">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.description }}
                                </div>

                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Bu açıklama masanın konumu, kapasitesi veya özellikleri hakkında bilgi verebilir.
                                    Örneğin:
                                    "Pencere kenarı, 6 kişilik"
                                </p>
                            </div>

                            <!-- Butonlar -->
                            <div
                                class="flex items-center justify-end space-x-3 pt-4 mt-6 border-t border-gray-100 dark:border-gray-700">
                                <Link :href="route('tables.index')"
                                    class="px-4 py-2 inline-flex items-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-md font-medium text-sm shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:focus:ring-gray-700 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                İptal
                                </Link>

                                <button type="submit"
                                    class="px-4 py-2 inline-flex items-center border border-transparent bg-indigo-600 dark:bg-indigo-700 text-white rounded-md font-medium text-sm shadow-sm hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                                    :disabled="isSubmitting">
                                    <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ isSubmitting ? 'Kaydediliyor...' : 'Masa Ekle' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- İpuçları kartı -->
                <div
                    class="mt-8 bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/40 dark:to-blue-950/30 rounded-xl overflow-hidden shadow-md">
                    <div class="px-6 py-4 flex items-center border-b border-indigo-100 dark:border-indigo-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400">
                            <path fill-rule="evenodd"
                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022Z"
                                clip-rule="evenodd" />
                        </svg>
                        <h4 class="text-base font-semibold text-indigo-900 dark:text-indigo-200">Masa Yönetimi İpuçları
                        </h4>
                    </div>

                    <div class="p-6 text-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-indigo-800 dark:text-indigo-300 font-medium mb-2">İyi Masa İsimlendirme
                                </h5>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 pl-5">
                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 mr-1.5 mt-1 text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                            <path fill-rule="evenodd"
                                                d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Masaları numaralandırın (Masa 1, Masa 2...)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 mr-1.5 mt-1 text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                            <path fill-rule="evenodd"
                                                d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Konum bilgisi ekleyin (Teras-1, Bahçe-3)</span>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="text-indigo-800 dark:text-indigo-300 font-medium mb-2">Masa Düzeni İpuçları
                                </h5>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300 pl-5">
                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 mr-1.5 mt-1 text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                            <path fill-rule="evenodd"
                                                d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Bölümlere ayırarak düzenleyin (Kat 1, Kat 2)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 mr-1.5 mt-1 text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                            <path fill-rule="evenodd"
                                                d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Özel masalar için VIP, Aile gibi tanımlar kullanın</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
