<script setup>
    import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
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
        'userData',
    ])

    const usuario = ref({
        //datos persona
        unidad_organica_id: null,
        nombres: null,
        apellidos: null,
        tipo_documento_identidad_id: null,
        numero_documento: null,
        edad: null,
        telefono: null,
        direccion: null,
        //datos usuario
        email: null,
        host: null,
        mac: null,
        ip: null,
        password: null,
        rol_id: null
    })

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 6) || 'Debe tener al menos 6 caracteres.'
    };

    const unidades_organicas = ref([])
    const tipos_documento_identidad = ref([])

    const isFormValid = ref(false)
    const refForm = ref()

    const isPasswordVisible = ref(false)

    const roles = ref([]);

    // 👉 drawer close
    const closeNavigationDrawer = () => {
        emit('update:isDialogVisible', false)
        nextTick(() => {
            refForm.value?.reset()
            refForm.value?.resetValidation()
        })
    }

    const onSubmit = async() => {
        refForm.value?.validate().then(async({ valid: isValid }) => {
            if (isValid)
            try {
                if (!props.dato) {
                    const { data, error } = await useApi(`/${props.endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(usuario.value),
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
                        body: JSON.stringify(usuario.value),
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
                /* emit('refreshTable');
                emit('update:isDialogVisible', false)
                emit('update:permissionName', '') */
                /* emit('userData', {
                    id: 0,
                    fullName: fullName.value,
                    company: company.value,
                    role: role.value,
                    country: country.value,
                    contact: contact.value,
                    email: email.value,
                    currentPlan: plan.value,
                    status: status.value,
                    avatar: '',
                    billing: 'Auto Debit',
                }) */
                emit('update:isDialogVisible', false)
                nextTick(() => {
                    refForm.value?.reset()
                    refForm.value?.resetValidation()
                })
            }


        })
    }

    const handleDrawerModelValueUpdate = val => {
        emit('update:isDialogVisible', val)
    }

    watch(() => props.dato, (newDato) => {

        usuario.value = {
            unidad_organica_id: newDato?.persona?.unidad_organica_id || null,
            nombres: newDato?.persona?.nombres || null,
            apellidos: newDato?.persona?.apellidos || null,
            tipo_documento_identidad_id: newDato?.persona?.tipo_documento_identidad_id || null,
            numero_documento: newDato?.persona?.numero_documento || null,
            edad: newDato?.persona?.edad || null,
            telefono: newDato?.persona?.telefono || null,
            direccion: newDato?.persona?.direccion || null,

            email: newDato?.email || null,
            host: newDato?.host || null,
            mac: newDato?.mac || null,
            ip: newDato?.ip || null,
            password: null,
            rol_id: Array.isArray(newDato?.roles) && newDato.roles.length > 0 ? newDato.roles[0].id : null,

        }
       /*  name.value = newDato?.name || ''
        lastname.value = newDato?.lastname || ''
        email.value = newDato?.email || ''
        rol_id.value = (newDato?.roles && newDato.roles.length > 0) ? newDato.roles[0].id : null;//newDato?.roles[0].id || null */
    }, { immediate: true }) // `immediate: true` para actualizar al inicio

    const fetchRoles = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/roles`);

            roles.value = data.value.data;

        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const fetchUnidadesOrganicas = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/unidades-organicas`);

            unidades_organicas.value = data.value.data;


        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    const fetchTiposDocumentoIdentidad = async () => {
        //isSelectAll.value = false
        try {
            const { data } = await useApi(`/tipos-documentos-identidad`);

            tipos_documento_identidad.value = data.value.data;


        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    // Llamar `fetchInitTabla` una vez al montar el componente
    onMounted(async () => {await fetchRoles(); await fetchUnidadesOrganicas(); await fetchTiposDocumentoIdentidad(); });
</script>

<template>
    <VNavigationDrawer
        data-allow-mismatch
        temporary
        :width="400"
        location="end"
        class="scrollable-content"
        :model-value="props.isDialogVisible"
        @update:model-value="handleDrawerModelValueUpdate"
    >
        <!-- 👉 Title -->
        <AppDrawerHeaderSection
            :title="props.dato ? 'Editar Usuario' : 'Agregar Nuevo Usuario'"
            @cancel="closeNavigationDrawer"
        />

        <VDivider />

        <PerfectScrollbar :options="{ wheelPropagation: false }">
            <VCard flat>
                <VCardText>
                    <!-- 👉 Form -->
                    <VForm
                        ref="refForm"
                        v-model="isFormValid"
                        @submit.prevent="onSubmit"
                    >
                        <VRow>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.apellidos"
                                    :rules="[rules.required]"
                                    label="Apellidos"
                                    placeholder="Apellidos"
                                />
                            </VCol>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.nombres"
                                    :rules="[rules.required]"
                                    label="Nombres"
                                    placeholder="Nombres"
                                />
                            </VCol>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppSelect
                                    v-model="usuario.tipo_documento_identidad_id"
                                    :items="tipos_documento_identidad"
                                    :item-title="item => `${item.slug} - ${item.tipo}`"
                                    item-value="id"
                                    label="Tipo de Documento de Identidad"
                                    placeholder="Tipo de Documento de Identidad"
                                    :rules="[rules.required]"
                                />
                            </VCol>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.numero_documento"
                                    :rules="[rules.required]"
                                    label="Numero de Documento"
                                    placeholder="Numero de Documento"
                                />
                            </VCol>


                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppSelect
                                    v-model="usuario.unidad_organica_id"
                                    :items="unidades_organicas"
                                    item-title="nombre"
                                    item-value="id"
                                    label="Unidad Organica"
                                    placeholder="Unidad Organica"
                                    :rules="[rules.required]"
                                />
                            </VCol>
                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.edad"
                                    type="number"
                                    :rules="[rules.required]"
                                    label="Edad"
                                    placeholder="Edad"
                                />
                            </VCol>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.telefono"
                                    label="Telefono"
                                    placeholder="Telefono"
                                />
                            </VCol>

                            <!-- 👉 Full name -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.direccion"
                                    label="Direccion"
                                    placeholder="Direccion"
                                />
                            </VCol>

                            <!-- 👉 Country -->
                            <VCol cols="12">
                                <AppSelect
                                    v-model="usuario.rol_id"
                                    label="Rol"
                                    placeholder="Rol"
                                    :rules="[rules.required]"
                                    :items="roles"
                                    item-title="name"
                                    item-value="id"
                                />
                            </VCol>

                            <!-- 👉 Email -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.email"
                                    :rules="[rules.required, rules.email]"
                                    label="Email"
                                    placeholder="johndoe@email.com"
                                    validate-on="input"
                                    lazy-rules
                                />
                            </VCol>

                            <VCol cols="12">
                                <AppTextField
                                    v-model="usuario.password"
                                    label="Password"
                                    placeholder="············"
                                    :rules="props.dato ? [] : [  rules.required, rules.minLength]"
                                    :type="isPasswordVisible ? 'text' : 'password'"


                                    :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                                />
                            </VCol>

                            <!-- 👉 Submit and Cancel -->
                            <VCol cols="12">
                                <VBtn
                                    type="submit"
                                    class="me-3"
                                >
                                    {{ props.dato ? 'Actualizar' : 'Agregar' }}
                                </VBtn>
                                <VBtn
                                    type="reset"
                                    variant="tonal"
                                    color="error"
                                    @click="closeNavigationDrawer"
                                >
                                    Cancelar
                                </VBtn>
                            </VCol>
                        </VRow>
                    </VForm>
                </VCardText>
            </VCard>
        </PerfectScrollbar>
    </VNavigationDrawer>
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
