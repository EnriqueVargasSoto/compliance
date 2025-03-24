<script setup>
import AddEditUnidadOrganicaComponentDialog from './components/AddEditTipoDocumentoUnidadOrganicaComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="unidades-organicas"
                router="crear-unidad-organica"
                router_edit="editar-unidad-organica"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
