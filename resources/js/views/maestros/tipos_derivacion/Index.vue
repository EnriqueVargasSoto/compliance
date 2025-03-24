<script setup>
import AddEditTipoDerivacionComponentDialog from './components/AddEditTipoDerivacionComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="tipos-derivacion"
                :dynamic-component="AddEditTipoDerivacionComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Tipo Derivacion',
                    endpoint: 'tipos-derivacion'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
