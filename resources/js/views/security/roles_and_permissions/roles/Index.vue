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
        endpoint="roles"
        :params-init="{
            office_id: 1,
            module_name:'roles'
        }"
    />
</VCol>
</VRow>
</template>
<style scoped>
</style>