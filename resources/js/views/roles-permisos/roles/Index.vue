<script setup>
import RoleCards from '@/views/roles-permisos/roles/RoleCards.vue'
import UserList from '@/views/roles-permisos/roles/UserList.vue'

import AddEditPermissionDialog from '../permisos/components/AddEditPermissionComponentDialog.vue'; // Asegúrate de importar el componente
import AddEditRoleDialog from './components/AddEditRoleDialog.vue';

const permisos = ref([]);

const fetchPermisos = async () => {
    try {
        const { data } = await useApi(`/modulos`);

        permisos.value = data.value.data;
    } catch (error) {
        console.error("Error al cargar la configuración de la tabla:", error);
    }
};

 // Llamar `fetchInitTabla` una vez al montar el componente
 onMounted(async () => {await fetchPermisos();});

</script>

<template>
    <VRow>
        <!-- <VCol cols="12">
        <h4 class="text-h4 mb-1">
            Roles List
        </h4>
        <p class="text-body-1 mb-0">
            A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what he need
        </p>
        </VCol> -->

        <!-- 👉 Roles Cards -->
        <!-- <VCol cols="12">
        <RoleCards />
        </VCol> -->

        <!-- <VCol cols="12">
        <h4 class="text-h4 mb-1 mt-6">
            Total users with their roles
        </h4>
        <p class="text-body-1 mb-0">
            Find all of your company’s administrator accounts and their associate roles.
        </p>
        </VCol> -->

        <VCol cols="12">
        <!-- 👉 User List  -->
        <!-- <UserList /> -->
        <CustomerDataTabe endpoint="roles"
                :dynamic-component="AddEditRoleDialog"
                :component-props="{
                    isDialogVisible: false,
                    endpoint: 'roles',
                    permisos: permisos
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>

</template>
