<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  admins: {
    type: Array,
    required: true,
  },
});

const page = usePage();
const editingId = ref(null);

const createForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

function submitCreate() {
  createForm.post(route('admin.users.store'), {
    onSuccess: () => createForm.reset(),
  });
}

function startEdit(admin) {
  editingId.value = admin.id;
  editForm.reset();
  editForm.clearErrors();
  editForm.name = admin.name;
  editForm.email = admin.email;
}

function cancelEdit() {
  editingId.value = null;
  editForm.reset();
  editForm.clearErrors();
}

function submitEdit(adminId) {
  editForm.put(route('admin.users.update', adminId), {
    preserveScroll: true,
    onSuccess: () => cancelEdit(),
  });
}

function removeAdmin(adminId) {
  if (!window.confirm('Deseja remover este administrador?')) {
    return;
  }

  router.delete(route('admin.users.destroy', adminId), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Administradores" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Administradores</h2>
    </template>

    <div class="py-8">
      <div class="px-4 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <div v-if="page.props.flash.success" class="p-3 text-sm text-green-800 bg-green-100 rounded-lg">
          {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash.error" class="p-3 text-sm text-red-800 bg-red-100 rounded-lg">
          {{ page.props.flash.error }}
        </div>

        <div class="p-6 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">Cadastrar novo admin</h3>

          <form class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2" @submit.prevent="submitCreate">
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Nome</label>
              <input v-model="createForm.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
              <p v-if="createForm.errors.name" class="mt-1 text-sm text-red-600">{{ createForm.errors.name }}</p>
            </div>

            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
              <input v-model="createForm.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
              <p v-if="createForm.errors.email" class="mt-1 text-sm text-red-600">{{ createForm.errors.email }}</p>
            </div>

            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Senha</label>
              <input v-model="createForm.password" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
              <p v-if="createForm.errors.password" class="mt-1 text-sm text-red-600">{{ createForm.errors.password }}</p>
            </div>

            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Confirmar senha</label>
              <input v-model="createForm.password_confirmation" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
            </div>

            <div class="md:col-span-2">
              <button type="submit" :disabled="createForm.processing" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                Criar administrador
              </button>
            </div>
          </form>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">Admins cadastrados</h3>

          <div class="mt-4 overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                  <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                  <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Criado em</th>
                  <th class="px-4 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acoes</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr v-for="admin in admins" :key="admin.id">
                  <template v-if="editingId === admin.id">
                    <td class="px-4 py-3">
                      <input v-model="editForm.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                      <p v-if="editForm.errors.name" class="mt-1 text-sm text-red-600">{{ editForm.errors.name }}</p>
                    </td>
                    <td class="px-4 py-3">
                      <input v-model="editForm.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                      <p v-if="editForm.errors.email" class="mt-1 text-sm text-red-600">{{ editForm.errors.email }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 align-top">
                      {{ new Date(admin.created_at).toLocaleDateString() }}
                      <div class="mt-2 space-y-2">
                        <input v-model="editForm.password" type="password" placeholder="Nova senha (opcional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        <input v-model="editForm.password_confirmation" type="password" placeholder="Confirmar nova senha" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        <p v-if="editForm.errors.password" class="text-sm text-red-600">{{ editForm.errors.password }}</p>
                      </div>
                    </td>
                    <td class="px-4 py-3 space-x-2 text-right align-top">
                      <button type="button" class="text-sm font-medium text-blue-600 hover:underline" @click="submitEdit(admin.id)">Salvar</button>
                      <button type="button" class="text-sm font-medium text-gray-600 hover:underline" @click="cancelEdit">Cancelar</button>
                    </td>
                  </template>

                  <template v-else>
                    <td class="px-4 py-3 text-gray-900">{{ admin.name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ admin.email }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ new Date(admin.created_at).toLocaleDateString() }}</td>
                    <td class="px-4 py-3 space-x-3 text-right">
                      <button type="button" class="text-sm font-medium text-blue-600 hover:underline" @click="startEdit(admin)">Editar</button>
                      <button type="button" class="text-sm font-medium text-red-600 hover:underline" @click="removeAdmin(admin.id)">Excluir</button>
                    </td>
                  </template>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
