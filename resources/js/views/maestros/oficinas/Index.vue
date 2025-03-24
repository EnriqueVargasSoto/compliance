<script setup>
import AddEditOficinaComponentDialog from './components/AddEditOficinaComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="oficinas"
                :dynamic-component="AddEditOficinaComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Oficina',
                    endpoint: 'oficinas'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
