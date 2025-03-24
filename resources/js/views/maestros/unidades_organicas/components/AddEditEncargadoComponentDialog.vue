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

    const date_inicio = ref('');
    const date_fin = ref('');
    const personas = ref([]);
    const cargos = ref([]);

    const encargado = ref({
        unidad_organica_id: null,
        persona_id: null,
        cargo_id: null,
        fecha_inicio: null,
        fecha_fin: null,
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const refVForm = ref()

    const onReset = () => {
        emit('update:isDialogVisible', false)
        encargado.value = {
            unidad_organica_id: null,
            persona_id: null,
            cargo_id: null,
            fecha_inicio: null,
            fecha_fin: null,
        }
    }

    const onSubmit = async() => {
        encargado.value.unidad_organica_id = props.id;
        refVForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            try {
                if (!props.dato) {
                    const { data, error } = await useApi(`/${props.endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(encargado.value),
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
                        body: JSON.stringify(encargado.value),
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
            encargado.value = { ...newDato };
        } else {
            encargado.value = {
                unidad_organica_id: null,
                persona_id: null,
                cargo_id: null,
                fecha_inicio: null,
                fecha_fin: null,
            };
        }
    }, { immediate: true }) // `immediate: true` para actualizar al inicio

    const fetchPersonas = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/personas`);

            personas.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const fetchCargos = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/cargos`);

            cargos.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    onMounted(async () => {
        await fetchPersonas();
        await fetchCargos();
        console.log('props.id', props.id);

    });

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
                    {{ props.dato ? 'Editar' : 'Agregar Nueva' }} Encargado
                </h4>

                <!-- 👉 Form -->
                <VForm  ref="refVForm" @submit.prevent="onSubmit">
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">

                        <AppSelect
                            v-model="encargado.persona_id"
                            :items="personas"
                            :item-title="item => `${item.nombres} - ${item.apellidos}`"
                            item-value="id"
                            label="Encargado"
                            placeholder="Encargado"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">

                        <AppSelect
                            v-model="encargado.cargo_id"
                            :items="cargos"
                            item-title="cargo"
                            item-value="id"
                            label="Cargo"
                            placeholder="Cargo"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column">

                        <AppDateTimePicker
                            v-model="encargado.fecha_inicio"
                            label="Fecha de Inicio"
                            placeholder="Fecha de Inicio"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column">

                        <AppDateTimePicker
                            v-model="encargado.fecha_fin"
                            label="Fecha Fin"
                            placeholder="Fecha Fin"
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
