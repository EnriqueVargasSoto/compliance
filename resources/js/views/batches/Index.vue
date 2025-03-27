<script setup>

</script>
<template>
    <CustomerDataTable
        endpoint="batches"
        :params-init="{
            office_id: 1,
            module_name:'lotes'
        }"
    />
</template>
<style scoped>

</style>