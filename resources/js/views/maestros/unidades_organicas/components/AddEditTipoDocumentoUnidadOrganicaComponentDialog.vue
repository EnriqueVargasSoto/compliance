<script setup>
    import Swal from 'sweetalert2';

    const props = defineProps({
        endpoint: String, // Ruta API
        isDialogVisible: {
            type: Boolean,
            required: true,
        },
        id: {
            type: Number,
            default: null,
        },
        dato: {
            type: Object,
            default: () => ({}),
        },
    })

    const emit = defineEmits([
        'update:isDialogVisible',
        'update:permission',
    ])

    const tipos_documentos = ref([]);

    const documento_unidad_organica = ref({
        unidad_organica_id: null,
        tipo_documento_id: null,
        tipo: 'Salida',
        correlativo: null,
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const refVForm = ref()

    const onReset = () => {
        emit('update:isDialogVisible', false)
        documento_unidad_organica.value = {
            unidad_organica_id: null,
            tipo_documento_id: null,
            tipo: null,
            correlativo: null,
        }
    }

    const onSubmit = async() => {
        documento_unidad_organica.value.unidad_organica_id = props.id;
        refVForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            try {
                if (!props.dato) {
                    const { data, error } = await useApi(`/${props.endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(documento_unidad_organica.value),
                    });

                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,//'La importancia se ha agregado correctamente.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        buttonsStyling: false, // Desactiva los estilos predeterminados
                        customClass: {
                            confirmButton: 'custom-ok-button'
                        }
                    });

                    emit('refreshTable'); // Actualiza la tabla

                } else {
                    const { data, error } = await useApi(`/${props.endpoint}/${props.dato.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(documento_unidad_organica.value),
                    });

                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,//'La importancia se ha actualizado correctamente.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        buttonsStyling: false, // Desactiva los estilos predeterminados
                        customClass: {
                            confirmButton: 'custom-ok-button'
                        }
                    });

                    emit('refreshTable'); // Actualiza la tabla
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: error.error,//'Hubo un problema al agregar la estado.',
                    icon: 'error',
                    confirmButtonText: 'Intentar de nuevo',
                });
            } finally{
                emit('refreshTable');
                emit('update:isDialogVisible', false)
                emit('update:permissionName', '')
            }
        });

    }

    watch(() => props.dato, (newDato) => {
        if (newDato) {
            documento_unidad_organica.value = { ...newDato };
        } else {
            documento_unidad_organica.value = {
                unidad_organica_id: null,
                tipo_documento_id: null,
                tipo: null,
                correlativo: null,
            };
        }
    }, { immediate: true }) // `immediate: true` para actualizar al inicio

    const fetchTiposDocumentos = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/tipos-documento`);

            tipos_documentos.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    onMounted(async () => {await fetchTiposDocumentos();});

</script>

<template>
    <VDialog
        :width="$vuetify.display.smAndDown ? 'auto' : 600"
        :model-value="props.isDialogVisible"
        @update:model-value="onReset"
    >
        <!-- 👉 dialog close btn -->
        <DialogCloseBtn @click="onReset" />

        <VCard class="pa-2 pa-sm-10">
            <VCardText>
                <!-- 👉 Title -->
                <h4 class="text-h4 text-center mb-2">
                    {{ props.dato ? 'Editar' : 'Agregar Nueva' }} Documento Unidad Organica
                </h4>
                <!-- <p class="text-body-1 text-center mb-6">
                    {{ props.dato ? 'Editar' : 'Agregar' }}  permiso según sus requisitos.
                </p> -->

                <!-- 👉 Form -->
                <VForm  ref="refVForm" @submit.prevent="onSubmit">
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <!-- <AppTextField
                            v-model="documento_unidad_organica.tipo_documento_id"
                            placeholder="Tipo"
                            :rules="[rules.required]"
                        /> -->

                        <AppSelect
                            v-model="documento_unidad_organica.tipo_documento_id"
                            :items="tipos_documentos"
                            item-title="nombre"
                            item-value="id"
                            label="Tipo de Documento"
                            placeholder="Tipo de Documento"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="documento_unidad_organica.correlativo"
                            placeholder="Correlativo"
                            type="number"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row" style="justify-content: flex-end;">
                        <VBtn type="submit">
                            {{ props.dato ? 'Actualizar' : 'Agregar' }}
                        </VBtn>
                    </div>

                </VForm>
            </VCardText>
        </VCard>
    </VDialog>
</template>

<style lang="scss">
    .permission-table {
        td {
            border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
            padding-block: 0.5rem;
            padding-inline: 0;
        }
    }

    .custom-ok-button {
        background-color: #28a745 !important; /* Verde éxito */
        color: white !important;
        font-weight: bold !important;
        padding: 10px 20px !important;
        border-radius: 5px !important;
        border: none !important;
    }
</style>
