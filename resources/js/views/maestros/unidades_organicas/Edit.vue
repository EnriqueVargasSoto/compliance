<script setup>
    import AddEditTipoDocumentoUnidadOrganicaComponentDialog from './components/AddEditTipoDocumentoUnidadOrganicaComponentDialog.vue'; // Asegúrate de importar el componente
    import AddEditEncargadoComponentDialog from './components/AddEditEncargadoComponentDialog.vue'; // Asegúrate de importar el componente
    import Swal from 'sweetalert2';

    const route = useRoute();
    const objeto = route.query; // Obtiene los datos de la URL

    const tipos_unidad_organica = ref([]);
    const tipos_derivacion = ref([]);
    const padres = ref([]);

    const disabled = ref(true);

    const unidad_organica = ref({
        tipo_unidad_organica_id: null,
        tipo_derivacion_id: null,
        padre_id: null,
        nombre: null,
        slug: null,
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const refVForm = ref();

    const onSubmit = async() => {
        refVForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            try {
                //if (!props.dato) {
                    const { data, error } = await useApi(`/unidades-organicas`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(unidad_organica.value),
                    });

                    unidad_organica.value = data.value.data;
                    disabled.value = true;

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

                    console.log('error: ',error);
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: error.error,//'Hubo un problema al agregar la estado.',
                    icon: 'error',
                    confirmButtonText: 'Intentar de nuevo',
                });
            } finally{
                /* emit('refreshTable');
                emit('update:isDialogVisible', false)
                emit('update:permissionName', '') */
            }
        });

    }

    const fetchTiposUnidadOrganica = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/tipos-unidades-organica`);

            tipos_unidad_organica.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const fetchTiposDerivacion = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/tipos-derivacion`);

            tipos_derivacion.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const fetchPadres = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/unidades-organicas`);

            padres.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const setData = async () => {
        if (objeto) {
            unidad_organica.value = {
                id: objeto.id ?? null,
                tipo_unidad_organica_id: parseInt(objeto.tipo_unidad_organica_id) ?? null,
                tipo_derivacion_id: parseInt(objeto.tipo_derivacion_id) ?? null,
                padre_id: parseInt(objeto.padre_id) ?? null,
                nombre: objeto.nombre ?? '',
                slug: objeto.slug ?? '',
            };
        }

        console.log('Unidad Orgánica cargada: ', unidad_organica.value);
    }



    onMounted(async () => {
        await setData();
        /* unidad_organica.value = objeto; */
        await fetchTiposUnidadOrganica();
        await fetchTiposDerivacion();
        await fetchPadres();

        // Asegurar que unidad_organica tiene valores compatibles con los selects

    });
</script>

<template>
    <div>
        <div class="d-flex flex-wrap justify-start justify-sm-space-between gap-y-4 gap-x-6 mb-6">
            <div class="d-flex flex-column justify-center">
                <h4 class="text-h4 font-weight-medium">Editar Unidad Orgánica</h4>
            </div>
        </div>

        <VRow>
            <VCol md="12" cols="12">
                <!-- 👉 Product Information -->
                <VCard
                    class="mb-6"
                    title="Informacion de Unidad Orgánica"
                >
                    <VCardText>
                        <VForm  ref="refVForm" @submit.prevent="onSubmit">
                            <VRow>

                                <VCol cols="12" md="4">
                                    <AppSelect
                                        v-model="unidad_organica.tipo_unidad_organica_id"
                                        placeholder="Tipo Unidad Organica"
                                        label="Tipo Unidad Organica"
                                        :items="tipos_unidad_organica"
                                        item-title="nombre"
                                        item-value="id"
                                        :rules="[rules.required]"
                                        :disabled="!disabled"
                                    />
                                </VCol>

                                <VCol cols="12" md="4">
                                    <AppSelect
                                        v-model="unidad_organica.tipo_derivacion_id"
                                        placeholder="Tipo de Derivación"
                                        label="Tipo de Derivación"
                                        :items="tipos_derivacion"
                                        item-title="derivacion"
                                        item-value="id"
                                        :rules="[rules.required]"
                                        :disabled="!disabled"
                                    />
                                </VCol>

                                <VCol cols="12" md="4">
                                    <AppSelect
                                        v-model="unidad_organica.padre_id"
                                        placeholder="Unidad Orgánica Padre"
                                        label="Unidad Orgánica Padre"
                                        :items="padres"
                                        item-title="nombre"
                                        item-value="id"
                                        :disabled="!disabled"
                                    />
                                </VCol>

                                <VCol cols="12" md="10">
                                    <AppTextField
                                        v-model="unidad_organica.nombre"
                                        label="Nombre de la Unidad Orgánica"
                                        placeholder="Nombre"
                                        :rules="[rules.required]"
                                        :disabled="!disabled"
                                    />
                                </VCol>

                                <VCol cols="12" md="2">
                                    <AppTextField
                                        v-model="unidad_organica.slug"
                                        label="Abreviatura"
                                        placeholder="Abreviatura"
                                        :rules="[rules.required]"
                                        :disabled="!disabled"
                                    />
                                </VCol>

                                <!-- 👉 Role name -->
                                <VCol cols="12" class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row" style="justify-content: flex-end;">
                                    <VBtn type="submit" color="info" :disabled="!disabled">
                                        Editar Unidad Orgánica
                                    </VBtn>
                                </VCol>
                                <!-- daaaaa -->
                            </VRow>
                        </VForm>

                    </VCardText>
                </VCard>

                <!-- 👉 Media -->

                <VCard class="mb-6">
                    <VRow>
                        <VCol cols="12">
                            <CustomerDataTabe v-if="unidad_organica.id" endpoint="encargados"
                                :dynamic-component="AddEditEncargadoComponentDialog"
                                :disabled="!disabled"
                                :id = "unidad_organica.id"
                                :component-props="{
                                    isDialogVisible: false,
                                    permissionName: 'Agregar Encargado',
                                    endpoint: 'encargados'
                                }"
                                @refreshTable="reloadTable"
                            />
                        </VCol>
                    </VRow>
                </VCard>

                <VCard class="mb-6">
                    <VRow>
                        <VCol cols="12">
                            <CustomerDataTabe endpoint="documentos-unidades-organicas"
                                :dynamic-component="AddEditTipoDocumentoUnidadOrganicaComponentDialog"
                                :disabled="!disabled"
                                :id = "unidad_organica.id"
                                :component-props="{
                                    isDialogVisible: false,
                                    permissionName: 'Agregar Documento Unidad Organica',
                                    endpoint: 'documentos-unidades-organicas'
                                }"
                                @refreshTable="reloadTable"
                            />
                        </VCol>
                    </VRow>
                </VCard>
            </VCol>
        </VRow>
    </div>
</template>

<style scoped>

</style>
