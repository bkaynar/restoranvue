<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps<{ users: Array<{ id: number; name: string; email: string; role: string }> }>();
const users = ref(props.users);

const newUser = useForm({ name: '', email: '', password: '', role: 'garson' });
const showAddModal = ref(false);
const showDeleteConfirm = ref<{ id: number | null, name: string }>({ id: null, name: '' });

function openAddModal() {
    newUser.reset();
    newUser.role = 'garson';
    showAddModal.value = true;
}
function closeAddModal() {
    showAddModal.value = false;
}
function addUser() {
    newUser.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddModal();
            // Formdan yeni kullanıcıyı oluşturup ekle
            users.value.push({
                id: Date.now(), // Geçici id, backend response ile değiştirilebilir
                name: newUser.name,
                email: newUser.email,
                role: newUser.role,
            });
        }
    });
}
function getInitials(name: string) {
    return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2);
}

function confirmDelete(user: { id: number; name: string }) {
    showDeleteConfirm.value = { id: user.id, name: user.name };
}
function cancelDelete() {
    showDeleteConfirm.value = { id: null, name: '' };
}
function deleteUser() {
    if (!showDeleteConfirm.value.id) return;
    const id = showDeleteConfirm.value.id;
    router.delete(route('users.destroy', id), {
        onSuccess: () => {
            users.value = users.value.filter(u => u.id !== id);
            cancelDelete();
        }
    });
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto py-10">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Kullanıcılar</h1>
                <button @click="openAddModal"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 font-semibold">+
                    Kullanıcı Ekle</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="user in users" :key="user.id"
                    class="flex items-center gap-4 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800 p-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-2xl font-bold text-blue-700">
                        {{ getInitials(user.name) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-lg text-gray-800 dark:text-white">{{ user.name }}</div>
                        <div class="text-gray-500 text-sm">{{ user.email }}</div>
                    </div>
                    <span
                        :class="user.role === 'yönetici' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700'"
                        class="px-3 py-1 rounded-full text-xs font-bold capitalize">
                        {{ user.role }}
                    </span>
                    <button @click="confirmDelete(user)" title="Kullanıcıyı Sil"
                        class="ml-2 text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Kullanıcı Ekle Modalı -->
            <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 w-full max-w-md relative">
                    <button @click="closeAddModal"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Kullanıcı Ekle</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Ad Soyad</label>
                        <input v-model="newUser.name" type="text" class="w-full border rounded px-3 py-2" />
                        <div v-if="newUser.errors.name" class="text-red-500 text-xs mt-1">{{ newUser.errors.name }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">E-posta</label>
                        <input v-model="newUser.email" type="email" class="w-full border rounded px-3 py-2" />
                        <div v-if="newUser.errors.email" class="text-red-500 text-xs mt-1">{{ newUser.errors.email }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Şifre</label>
                        <input v-model="newUser.password" type="password" class="w-full border rounded px-3 py-2" />
                        <div v-if="newUser.errors.password" class="text-red-500 text-xs mt-1">{{ newUser.errors.password
                        }}</div>
                    </div>
                    <div class="mb-6">
                        <label class="block mb-1 font-medium">Rol</label>
                        <select v-model="newUser.role" class="w-full border rounded px-3 py-2">
                            <option value="garson">Garson</option>
                            <option value="yönetici">Yönetici</option>
                        </select>
                        <div v-if="newUser.errors.role" class="text-red-500 text-xs mt-1">{{ newUser.errors.role }}
                        </div>
                    </div>
                    <button @click="addUser" :disabled="newUser.processing"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Kaydet</button>
                </div>
            </div>

            <!-- Silme Onay Modalı -->
            <div v-if="showDeleteConfirm.id"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 w-full max-w-md relative">
                    <button @click="cancelDelete"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Kullanıcıyı Sil</h2>
                    <p class="mb-6">{{ showDeleteConfirm.name }} adlı kullanıcıyı silmek istediğinize emin misiniz?</p>
                    <div class="flex gap-4 justify-end">
                        <button @click="cancelDelete"
                            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Vazgeç</button>
                        <button @click="deleteUser"
                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Sil</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
input,
select {
    background: #f9fafb;
}

.dark input,
.dark select {
    background: #23272f;
    color: #fff;
}
</style>
