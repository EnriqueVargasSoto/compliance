<script setup>
    import Swal from 'sweetalert2';

    const props = defineProps({
        endpoint: String, // Ruta API
        isDialogVisible: {
            type: Boolean,
            required: true,
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

    const oficina = ref('');
    const encargado = ref('');

    const onReset = () => {
        emit('update:isDialogVisible', false)
        oficina.value = ''
        encargado.value = ''
    }

    const onSubmit = async() => {
        try {
            if (!props.dato) {
                const { data, error } = await useApi(`/${props.endpoint}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        oficina: oficina.value,
                        encargado: encargado.value,
                    }),
                });


                Swal.fire({
                    title: '¡Éxito!',
                    text: 'LA oficina se ha agregado correctamente.',
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
                    body: JSON.stringify({
                        oficina: oficina.value,
                        encargado: encargado.value,
                    }),
                });


                Swal.fire({
                    title: '¡Éxito!',
                    text: 'La oficina se ha actualizado correctamente.',
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
                text: 'Hubo un problema al agregar la oficina.',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo',
            });
        } finally{
            emit('refreshTable');
            emit('update:isDialogVisible', false)
            emit('update:permissionName', '')
        }
    }

    watch(() => props.dato, (newDato) => {
        oficina.value = newDato?.oficina || ''
        encargado.value = newDato?.encargado || ''
    }, { immediate: true }) // `immediate: true` para actualizar al inicio

    /* const fetchModulos = async () => {
        try {
            const { data } = await useApi(`/modulos`);
            modulos.value = data.value.data.filter(item => item.submodulos.length === 0);
        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    }; */

    // Llamar `fetchInitTabla` una vez al montar el componente
    //onMounted(async () => {await fetchModulos();});
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
                    {{ props.dato ? 'Editar' : 'Agregar Nuevo' }} Oficina
                </h4>
                <!-- <p class="text-body-1 text-center mb-6">
                    {{ props.dato ? 'Editar' : 'Agregar' }}  permiso según sus requisitos.
                </p> -->

                <!-- 👉 Form -->
                <VForm  style="text-align: center;">
                    <!-- <VAlert
                        type="warning"
                        title="¡Advertencia!"
                        variant="tonal"
                        class="mb-6"

                    >
                        <template #text>
                            Al {{ props.dato ? 'editar' : 'agregar' }} el nombre del permiso, es posible que se rompa la funcionalidad de permisos del sistema.
                        </template>
                    </VAlert> -->

                    <!-- 👉 Role name -->
                    <!-- <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">

                        <AppSelect
                            v-model="modulo_id"
                            :items="modulos"
                            item-title="name"
                            item-value="id"
                            placeholder="Modulo"
                        />
                    </div> -->
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="oficina"
                            placeholder="Nombre de Oficina"
                        />
                    </div>
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="encargado"
                            placeholder="Nombre de Encargado"
                        />
                    </div>
                    <!-- 👉 Role name -->
                    <!-- <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextarea
                            v-model="description"
                            placeholder="Descripcion de Permiso"
                        />
                    </div> -->
                    <VBtn @click="onSubmit">
                        {{ props.dato ? 'Actualizar' : 'Agregar' }}
                    </VBtn>

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
