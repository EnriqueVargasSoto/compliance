<script setup>
import AddEditCargoComponentDialog from './components/AddEditCargoComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="cargos"
                :dynamic-component="AddEditCargoComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Cargo',
                    endpoint: 'cargos'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
