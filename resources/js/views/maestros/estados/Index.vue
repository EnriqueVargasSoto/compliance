<script setup>
import AddEditEstadoComponentDialog from './components/AddEditEstadoComponentDialog.vue'; // Asegúrate de importar el componente

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="estados"
                :dynamic-component="AddEditEstadoComponentDialog"
                :component-props="{
                    isDialogVisible: false,
                    permissionName: 'Agregar Estado',
                    endpoint: 'estados'
                }"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>

</style>
