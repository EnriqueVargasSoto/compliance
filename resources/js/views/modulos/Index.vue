<script setup>
import AddEditModuloComponentDialog from './components/AddEditModuloComponentDialog.vue'; // Asegúrate de importar el componente

</script>
<template>
  <VRow>


    <VCol cols="12">
        <CustomerDataTabe endpoint="modulos"
            :dynamic-component="AddEditModuloComponentDialog"
            :component-props="{
                isDialogVisible: false,
                permissionName: 'Agregar Modulo',
                endpoint: 'modulos'
            }"
            @refreshTable="reloadTable"
        />
    </VCol>
  </VRow>
</template>
