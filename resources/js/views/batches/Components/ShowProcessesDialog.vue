<script setup>
    const props = defineProps({
        endpoint: String, // Ruta API
        isDialogVisibleShowProcesses: {
            type: Boolean,
            required: true,
        },
        data: {
            type: Object,
            default: () => ({}),
        },
        title: {
            type: String,
        }
    })

    const search = ref('')
    const searchBool = ref(true)

    const batch = ref({
        batch: null,
        date_init: null,
        date_end: null,
        status: 0,
        signed: 0
    });

    const emit = defineEmits([
        'update:isDialogVisibleShowProcesses'
    ])

    const onReset = async () => {
        emit('update:isDialogVisibleShowProcesses', false);
        batch.value = {
            batch: null,
            date_init: null,
            date_end: null,
            status: 0,
            signed: 0
        };

    }
</script>
<template>
    <VDialog
        :width="$vuetify.display.smAndDown ? 'auto' : 1200"
        :model-value="props.isDialogVisibleShowProcesses"
        @update:model-value="onReset"
    >
        <!-- 👉 dialog close btn -->
        <DialogCloseBtn @click="onReset" />



        <VCard class="pa-2 pa-sm-10">

            <VCardItem>
                <div class="d-flex align-center justify-space-between flex-wrap gap-4">
                    <!-- Titulo -->
                    <div class="d-flex gap-2 align-center">
                        <VCardTitle>{{props.title}}</VCardTitle>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex align-center gap-4 flex-wrap">
                        <!-- Botón Crear Nuevo Módulo (Fuera de la Tabla) -->


                    </div>
                </div>

            </VCardItem>
            <VCardText class="d-flex align-center justify-space-between flex-wrap gap-4">
                <div class="d-flex align-center gap-4 flex-wrap">
                    <AppTextField
                        v-if="searchBool"
                        v-model="search"
                        placeholder="Buscar..."
                        style="inline-size: 15.625rem;"
                    />

                </div>
            </VCardText>

            <VDivider />
        </VCard>

    </VDialog>
</template>
<style scoped>

</style>