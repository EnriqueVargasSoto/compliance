<script setup>
    import CreateDialog from './Components/CreateDialog.vue';
    import ShowProcessesDialog from './Components/ShowProcessesDialog.vue';
</script>
<template>
    <CustomerDataTable
        endpoint="batches"
        :dynamic-component="CreateDialog"
        :dynamic-component-show-processes="ShowProcessesDialog"
        :params-init="{
            office_id: 1,
            module_name:'lotes'
        }"
        :component-props="{
            isDialogVisible: false,
            isDrawerOpen: false,
            title: 'Lote',
            endpoint: 'batches',
            data: {}
        }"
        :component-props-show-processes="{
            isDialogVisibleShowProcesses: false,
            isDrawerOpen: false,
            title: 'Procesos',
            endpoint: 'processes',
            data: {}
        }"
    />
</template>
<style scoped>

</style>