<script setup>
import AddEditTipoUnidadOrganicaComponentDialog from './components/AddEditTipoUnidadOrganicaComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="tipos-unidades-organica"
                :dynamic-component="AddEditTipoUnidadOrganicaComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Tipo Unidad Organica',
                    endpoint: 'tipos-unidades-organica'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
