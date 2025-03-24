<script setup>
import AddEditPermissionDialog from './components/AddEditPermissionComponentDialog.vue'; // Asegúrate de importar el componente

</script>
<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="permisos"
                :dynamic-component="AddEditPermissionDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Permiso',
                    endpoint: 'permisos'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
