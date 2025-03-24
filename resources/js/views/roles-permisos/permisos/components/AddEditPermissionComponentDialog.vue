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

    const refVForm = ref()

    const permiso = ref({
        modulo_id: null,
        name: null,
        slug: null,
        description: null
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const modulos = ref([]);

    const onReset = () => {
        emit('update:isDialogVisible', false)
        permiso = {
            modulo_id: null,
            name: null,
            slug: null,
            description: null
        };
    }

    const onSubmit = async() => {
        refVForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            try {
                if (!props.dato) {
                    const { data, error } = await useApi(`/${props.endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(permiso.value),
                    });


                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,//'El permiso se ha agregado correctamente.',
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
                        body: JSON.stringify(permiso.value),
                    });


                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,//'El permiso se ha actualizado correctamente.',
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
                    text: error.error,//'Hubo un problema al agregar el permiso.',
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
            permiso.value = { ...newDato };
        } else {
            permiso.value = {
                modulo_id: null,
                name: null,
                slug: null,
                description: null
            };
        }
    }, {  immediate: true });

    const fetchModulos = async () => {
        try {
            const { data } = await useApi(`/modulos`);
            modulos.value = data.value.data;// modulos.value = data.value.data.filter(item => item.submodulos.length === 0);
        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    // Llamar `fetchInitTabla` una vez al montar el componente
    onMounted(async () => {await fetchModulos();});
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
                    {{ props.dato ? 'Editar' : 'Agregar Nuevo' }} Permiso
                </h4>
                <p class="text-body-1 text-center mb-6">
                    {{ props.dato ? 'Editar' : 'Agregar' }}  permiso según sus requisitos.
                </p>

                <!-- 👉 Form -->
                <VForm  ref="refVForm" @submit.prevent="onSubmit">
                    <VAlert
                        type="warning"
                        title="¡Advertencia!"
                        variant="tonal"
                        class="mb-6"

                    >
                        <template #text>
                            Al {{ props.dato ? 'editar' : 'agregar' }} el nombre del permiso, es posible que se rompa la funcionalidad de permisos del sistema.
                        </template>
                    </VAlert>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">


                        <AppSelect
                            v-model="permiso.modulo_id"
                            :items="modulos"
                            item-title="nombre"
                            item-value="id"
                            placeholder="Modulo"
                            :rules="[rules.required]"
                        />
                    </div>
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="permiso.name"
                            placeholder="Nombre de Permiso"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="permiso.slug"
                            placeholder="Slug de Permiso"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextarea
                            v-model="permiso.description"
                            placeholder="Descripcion de Permiso"
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
