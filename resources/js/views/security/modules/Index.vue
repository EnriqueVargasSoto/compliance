<script setup>
</script>
<template>
  <VRow>


<VCol cols="12">
    <!-- <CustomerDataTabe endpoint="modulos"
        :dynamic-component="AddEditModuloComponentDialog"
        :component-props="{
            isDialogVisible: false,
            permissionName: 'Agregar Modulo',
            endpoint: 'modulos'
        }"
        @refreshTable="reloadTable"
    /> -->
    <CustomerDataTable
        endpoint="modules"
        :params-init="{
            office_id: 1,
            module_name:'modulos'
        }"
    />
</VCol>
</VRow>
</template>
<style scoped>
</style>