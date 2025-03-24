<script setup>
import AddEditTipoMovimientoComponentDialog from './components/AddEditTipoMovimientoComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="tipos-movimiento"
                :dynamic-component="AddEditTipoMovimientoComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Tipo de Movimiento',
                    endpoint: 'tipos-movimiento'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
