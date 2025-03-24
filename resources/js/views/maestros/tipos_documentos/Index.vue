<script setup>
import AddEditTipoDocumentoComponentDialog from './components/AddEditTipoDocumentoComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="tipos-documento"
                :dynamic-component="AddEditTipoDocumentoComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Tipo de Documento',
                    endpoint: 'tipos-documento'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
