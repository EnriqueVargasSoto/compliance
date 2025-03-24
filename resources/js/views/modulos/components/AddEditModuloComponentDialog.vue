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
        tipo: String
    })

    const emit = defineEmits([
        'update:isDialogVisible',
        'update:permission',
    ])

    const refVForm = ref()

    const modulo = ref({
        nombre: null,
        slug: null,
        ruta: null,
        padre_id: null,
        descripcion: null
    });

    const modulos = ref([]);

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const onReset = () => {
        emit('update:isDialogVisible', false)
        modulo.value = {
            nombre: null,
            slug: null,
            ruta: null,
            padre_id: null,
            descripcion: null,
            icono: null
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
                        body: JSON.stringify(modulo.value),
                    });

                    await fetchModulos();

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
                        body: JSON.stringify(modulo.value),
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
                nextTick(() => {
                    /* refVForm.value?.reset()
                    refVForm.value?.resetValidation(); */
                    onReset();
                })
            }

        });
    }

    watch(() => props.dato, (newDato) => {

        if (newDato) {
            modulo.value = { ...newDato };
        } else {
            modulo.value = {
                nombre: null,
                slug: null,
                ruta: null,
                padre_id: null,
                descripcion: null,
                icono: null
            };
        }
    }, {  immediate: true });

    const fetchModulos = async () => {
        try {
            const { data } = await useApi(`/modulos`);
            modulos.value = data.value.data;
        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

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
                    {{ props.dato ? 'Editar' : 'Agregar Nuevo' }} Modulo
                </h4>
                <p class="text-body-1 text-center mb-6">
                    {{ props.dato ? 'Editar' : 'Agregar' }} modulo según sus requisitos.
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
                            Al {{ props.dato ? 'editar' : 'agregar' }} el nombre del modulo, es posible que se rompa la funcionalidad de modulos del sistema.
                        </template>
                    </VAlert>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="modulo.nombre"
                            placeholder="Nombre de Modulo"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="modulo.slug"
                            placeholder="Slug de Modulo"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="modulo.ruta"
                            placeholder="Ruta de Modulo"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">

                        <AppSelect
                            v-model="modulo.padre_id"
                            :items="modulos"
                            item-title="nombre"
                            item-value="id"
                            placeholder="Modulo"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="modulo.icono"
                            placeholder="Icono de Modulo"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextarea
                            v-model="modulo.descripcion"
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
