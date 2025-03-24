<script setup>
import AddEditTipoDocumentoIdentidadComponentDialog from './components/AddEditTipoDocumentoIdentidadComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="tipos-documentos-identidad"
                :dynamic-component="AddEditTipoDocumentoIdentidadComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Tipo Documento Identidad',
                    endpoint: 'tipos-documentos-identidad'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
