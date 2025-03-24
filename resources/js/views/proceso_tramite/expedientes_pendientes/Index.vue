<script setup>

</script>

<template>
    <VRow>
        <VCol cols="12">
            <CustomerDataTabe endpoint="expedientes"
                router="crear-expediente"
                @refreshTable="reloadTable"
            />
        </VCol>
    </VRow>
</template>
<style scoped>
</style>
