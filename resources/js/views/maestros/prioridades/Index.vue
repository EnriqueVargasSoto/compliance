<script setup>
import AddEditPrioridadComponentDialog from './components/AddEditPrioridadComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="prioridades"
                :dynamic-component="AddEditPrioridadComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Prioridad',
                    endpoint: 'prioridades'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
