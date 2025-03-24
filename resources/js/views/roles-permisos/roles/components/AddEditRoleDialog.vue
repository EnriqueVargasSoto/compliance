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
        'update:rolePermissions',
    ])

    const rol = ref({
        name: null,
        permissions: []
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };

    const isSelectAll = ref(false)
    const refPermissionForm = ref()

    const checkedCount = computed(() => {
        let counter = 0
        rol.value.permissions.forEach(modulos => {
            if (modulos.submodulos.length == 0) {
                modulos.permisos.forEach(permisos => {
                    if (permisos.selected) {
                        counter++
                    }
                })

            }
        })

        return counter
    })

    const checksCount = computed(() => {
        let counter = 0
        rol.value.permissions.forEach(modulos => {
            if (modulos.submodulos.length == 0) {
                modulos.permisos.forEach(permisos => {

                        counter++
                })

            }
        })

        return counter
    })

    const isIndeterminate = computed(() => checkedCount.value > 0 && checkedCount.value == checksCount.value)

    watch(isSelectAll, val => {

        rol.value.permissions = rol.value.permissions.map( modulo => ({
            ...modulo,
            permisos: modulo.permisos.map( permiso => ({
                ...permiso,
                selected: val
            }))
        }));
    })

    watch(isIndeterminate, () => {
        if (!isIndeterminate.value)
            isSelectAll.value = false
    })


    const onSubmit = async () => {
        refPermissionForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            try {
                if (!props.dato) {
                    const { data, error } = await useApi(`/${props.endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(rol.value),
                    });


                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,
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
                        body: JSON.stringify(rol.value),
                    });


                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.value.mensaje,
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
                    text: error.error,//'Hubo un problema al agregar el rol.',
                    icon: 'error',
                    confirmButtonText: 'Intentar de nuevo',
                });
            } finally{
                emit('update:rolePermissions', rol.value)
                emit('update:isDialogVisible', false)
                isSelectAll.value = false
                refPermissionForm.value?.reset()
            }
        });


    }

    const onReset = () => {
        emit('update:isDialogVisible', false)
        isSelectAll.value = false
        refPermissionForm.value?.reset()
    }

    watch(() => props.dato, (newDato) => {
        rol.value.name = newDato?.name || ''
        rol.value.permissions = rol.value.permissions.map( modulo => ({
            ...modulo,
            permisos: modulo.permisos.map( permiso => {
                const rolePermission = newDato?.permissions.find(item => item.id === permiso.id)

                if (rolePermission) {

                   permiso.selected = true;
                }

                return permiso
            })
        }));

    }, { immediate: true }) // `immediate: true` para actualizar al inicio

    const fetchPermisos = async () => {
        try {
            const { data } = await useApi(`/modulos`);

            rol.value.permissions = data.value.data.map( modulo => ({
                ...modulo,
                permisos: modulo.permisos.map( permiso => ({
                    ...permiso,
                    selected: false
                }))
            }));
        } catch (error) {
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    // Llamar `fetchInitTabla` una vez al montar el componente
    onMounted(async () => {await fetchPermisos();});

</script>

<template>
    <VDialog
        :width="$vuetify.display.smAndDown ? 'auto' : 900"
        :model-value="props.isDialogVisible"
        @update:model-value="onReset"
    >
        <!-- 👉 Dialog close btn -->
        <DialogCloseBtn @click="onReset" />

        <VCard class="pa-sm-10 pa-2">
            <VCardText>
                <!-- 👉 Title -->
                <h4 class="text-h4 text-center mb-2">{{ props.dato ? 'Editar' : 'Agregar Nuevo' }} Rol</h4>

                <p class="text-body-1 text-center mb-6">Establecer Permisos de Rol</p>

                <!-- 👉 Form -->
                <VForm ref="refPermissionForm" @submit.prevent="onSubmit">
                    <!-- 👉 Role name -->
                    <AppTextField
                        v-model="rol.name"
                        label="Nombre de Rol"
                        placeholder="Rol"
                         :rules="[rules.required]"
                    />

                    <h5 class="text-h5 my-6">Permisos de Rol</h5>

                    <!-- 👉 Role Permissions -->

                    <VTable class="permission-table text-no-wrap mb-6">
                        <!-- 👉 Admin  -->
                        <tr>
                            <td>
                                <h6 class="text-h6">
                                    Administrator Access
                                </h6>
                            </td>
                            <td colspan="4">
                                <div class="d-flex justify-end">
                                    <VCheckbox
                                        v-model="isSelectAll"
                                        v-model:indeterminate="isIndeterminate"
                                        label="Seleccionar Todo"
                                    />
                                </div>
                            </td>
                        </tr>

                        <!-- 👉 Other permission loop -->
                        <template
                            v-for="modulo in rol.permissions"

                            :key="modulo.nombre"
                        >
                            <tr v-if="modulo.submodulos.length == 0">
                                <td>
                                    <h6 class="text-h6">
                                        {{ modulo.nombre }}
                                    </h6>
                                </td>
                                <td
                                    v-for="permission in modulo.permisos"
                                    :key="permission.id"
                                >
                                    <div class="d-flex justify-end">
                                        <VCheckbox
                                        v-model="permission.selected"
                                        :label="permission.slug"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </template>

                    </VTable>

                    <!-- 👉 Actions button -->
                    <div class="d-flex align-center justify-center gap-4">
                        <VBtn type="submit">
                            {{ props.dato ? 'Actualizar' : 'Agregar' }}
                        </VBtn>

                        <VBtn
                            color="secondary"
                            variant="tonal"
                            @click="onReset"
                        >
                            Cancelar
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

            .v-checkbox {
            min-inline-size: 4.75rem;
            }

            &:not(:first-child) {
            padding-inline: 0.5rem;
            }

            .v-label {
            white-space: nowrap;
            }
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
