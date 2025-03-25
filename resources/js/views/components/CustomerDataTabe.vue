<script setup>
    import Swal from 'sweetalert2';
    import dayjs from "dayjs";
    import { useRouter } from 'vue-router';

    const props = defineProps({
        endpoint: String, // Ruta API
        dynamicComponent: {
            type: Object,
            //required: true,
        },
        componentProps: {
            type: Object,
            default: () => ({}),
        },
        router: {
            type: String,
        },
        router_edit: {
            type: String,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        id: {
            type: Number,
            default: null,
        },
    });

    //Variables reactivas
    const search = ref('')
    const searchBool = ref(false)
    const itemsPerPage = ref(10)
    const totalItems = computed(() => data.value?.recordsTotal ?? 0)
    const page = ref(1)
    const check = ref(false);
    const items_selects = ref([]);

    //data obtenida del api
    const { data } = await useApi(createUrl(`/${props.endpoint}`, {query: {search, per_page: itemsPerPage, page, unidad_organica_id: props.id},}) ?? {})

    // Variables para la tabla
    const headers = ref([]);
    const colors = ref({});
    const buttons = ref([]);
    const filters = ref([]);
    const title = ref("Tabla");
    const tableData = computed(() => data.value?.data ?? [])

    const totalPages = computed(() => {
        return data.value?.recordsTotal
            ? Math.ceil(data.value.recordsTotal / itemsPerPage.value)
            : 1; // Evita NaN si `data.value.total` no está definido
    });

    // TODO: Get type from backend
    const userData = useCookie('userData')

    const isComponentVisible = ref(false);
    const router = useRouter();  // Usar router dentro de setup()

    // Función para inicializar la tabla (obtener configuración inicial)
    const fetchInitTabla = async () => {
        try {
            const { data } = await useApi(`/${props.endpoint}-inicializa-tabla`);

            headers.value = data?.value.data?.headers || [];
            buttons.value = data?.value.data?.buttons || [];
            filters.value = data?.value.data?.filters || [];
            title.value = data?.value.data?.title || "Tabla";
            itemsPerPage.value = data?.value.data?.par_page || 10;
            page.value = data?.value.data?.page || 1;
            check.value = data?.value.data?.check || false;
            colors.value = data?.value.data?.colors || {};
            searchBool.value = data?.value.data?.search || false;
            items_selects.value = data?.value.data?.item_selects || [];
            items_selects.value.push({ value: totalItems, title: 'Todos' });
        } catch (error) {

            console.error("Error al cargar la configuración de la tabla:", error);
            //await logout();
        }
    };

    const reloadTable = async () => {
        const response = await useApi(createUrl(`/${props.endpoint}`, {
            query: { search, per_page: itemsPerPage, page, unidad_organica_id: props.id},
        }));

        data.value = response.data.value; // 🔹 Esto actualizará `tableData` automáticamente
    };

    // Función para formatear la fecha
    const formatDate = (timestamp) => {
        return dayjs(timestamp).format("DD/MM/YYYY");
    };

    const handleAction = (action) => {
        if (!action) {
            if (props.router) {
                //router.push(props.router);
                router.push({ name: props.router });
            }
            isComponentVisible.value = true; // Muestra el componente dinámico
            openModal(null, 'create');
        } else {
            if (props.router_edit) {
                //router.push(props.router);
                console.log('action', action);
                router.push({ name: props.router_edit, query:action });
            }
            isComponentVisible.value = true; // También puedes manejar para "edit"
            openModal(action, 'edit');
        }
    };

    const logout = async () => {

// Remove "accessToken" from cookie
useCookie('accessToken').value = null

// Remove "userData" from cookie
userData.value = null
localStorage.removeItem("menu");

// Redirect to login page
await router.push('/login')

// ℹ️ We had to remove abilities in then block because if we don't nav menu items mutation is visible while redirecting user to login page

// Remove "userAbilities" from cookie
useCookie('userAbilityRules').value = null

// Reset ability to initial ability
ability.update([])
}

    // Llamar `fetchInitTabla` una vez al montar el componente
    onMounted(async () => {await fetchInitTabla();});

    const emit = defineEmits(["update:componentProps"]);

    // Estado local para manejar el modal
    const localComponentProps = ref({ ...props.componentProps });

    // Función para abrir el modal
    const openModal = (dato, tipo) => {

            localComponentProps.value.dato = (dato === null) ? null : { ...dato };



        localComponentProps.value.isDialogVisible = true;

        if (props.id) {
            localComponentProps.value.id = props.id;
        }
        //localComponentProps.value.dato = dato;
    };

    // Función para cerrar el modal
    const closeModal = () => {
        localComponentProps.value.isDialogVisible = false;
        emit("update:componentProps", { ...localComponentProps.value });
    };

    const eliminarRegistro = async (id) => {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Rojo para "Eliminar"
            cancelButtonColor: '#3085d6', // Azul para "Cancelar"
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    // Llamar a la API para eliminar el registro
                    await useApi(`/${props.endpoint}/${id}`, {method: 'DELETE',});

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El registro ha sido eliminado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                    // Emitir evento o actualizar lista de registros
                    reloadTable()

                } catch (error) {

                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar el registro.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    };

    const resolveUserRoleVariant = role => {
        const roleLowerCase = role.toLowerCase()
        if (roleLowerCase === 'subscriber')
            return {
            color: 'success',
            icon: 'tabler-user',
            }
        if (roleLowerCase === 'author')
            return {
            color: 'error',
            icon: 'tabler-device-desktop',
            }
        if (roleLowerCase === 'Editor')
            return {
            color: 'info',
            icon: 'tabler-chart-pie',
            }
        if (roleLowerCase === 'editor')
            return {
            color: 'warning',
            icon: 'tabler-edit',
            }
        if (roleLowerCase === 'Admin')
            return {
            color: 'primary',
            icon: 'tabler-crown',
            }

        return {
            color: 'primary',
            icon: 'tabler-user',
        }
    }

    const resolveUserStatusVariant = stat => {
        //const statLowerCase = stat.toLowerCase()
        if (stat === 0)
            return 'danger'
        /* if (stat === 'pending')
            return 'warning' */
        if (stat === 1)
            return 'success'
        /* if (statLowerCase === 'inactive')
            return 'secondary' */

        return 'primary'
    }
</script>

<template>
    <VRow>
        <VCol cols="12">
            <VCard>
                <VCardItem>
                    <div class="d-flex align-center justify-space-between flex-wrap gap-4">
                        <!-- Titulo -->
                        <div class="d-flex gap-2 align-center">
                            <VCardTitle>{{title}}</VCardTitle>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex align-center gap-4 flex-wrap">

                            <VBtn
                                v-for="btn in buttons"
                                :key="btn.action"
                                :density="btn.density"
                                :prepend-icon="btn.icon"
                                :color="btn.color"
                                @click="handleAction(null)"
                                :disabled="disabled"
                            >
                                {{ btn.label }}
                            </VBtn>
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

                <VDataTableServer
                    v-model:items-per-page="itemsPerPage"
                    v-model:page="page"
                    :items-length="totalItems"
                    :headers="headers"
                    :items="tableData"
                    :show-select="check"
                    prev-page-label="'Previous'"
                    item-value="name"
                    class="text-no-wrap"
                >

                    <!-- Name -->
                    <template #item.apellidos="{ item }">
                        <div class="text-high-emphasis text-body-1">
                             {{ item.persona.apellidos }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.nombres="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.persona.nombres }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.tipo_documento_identidad="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.persona.tipo_documento_identidad.slug }}
                        </div>
                    </template>


                     <!-- Name -->
                     <template #item.tipo_documento_id="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.tipo_documento.nombre }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.documento="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.persona.numero_documento }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.unidad_organica="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.persona.unidad_organica.nombre }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.persona_id="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.persona.nombres }} {{ item.persona.apellidos }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.cargo_id="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.cargo.cargo }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.modulo="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.modulo.nombre }}
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.name="{ item }">
                        <div class="text-high-emphasis text-body-1">
                            {{ item.name }}
                        </div>
                    </template>

                    <!-- 👉 Role -->
                    <template #item.roles="{ item }">
                        <div class="d-flex align-center gap-x-2">
                            <VIcon
                                :size="22"
                                :icon="resolveUserRoleVariant(item.roles[0].name).icon"
                                :color="resolveUserRoleVariant(item.roles[0].name).color"
                            />

                                <div class="text-capitalize text-high-emphasis text-body-1">
                                {{ item.roles[0].name }}
                            </div>
                        </div>
                    </template>

                    <!-- Status -->
                    <template #item.status="{ item }">
                        <VChip
                            :color="resolveUserStatusVariant(item.status)"
                            size="small"
                            label
                            class="text-capitalize"
                        >
                            {{ item.status == 1 ? 'Activo':'Inactivo' }}
                        </VChip>
                    </template>

                    <!-- Assigned To -->
                    <template #item.assignedTo="{ item }">
                        <div class="d-flex gap-4">
                            <VChip
                                v-for="text in item.roles"
                                :key="text"
                                label
                                size="small"
                                :color="colors[text.name] ? colors[text.name].color : colors['manager'].color"
                                class="font-weight-medium"
                            >
                                {{ colors[text.name] ? colors[text.name].text : colors['manager'].text }}
                            </VChip>
                        </div>
                    </template>

                    <!-- Name -->
                    <template #item.created_at="{ item }">
                        {{ formatDate(item.created_at) }}
                    </template>

                    <!-- parent -->
                    <template #item.padre_id="{ item }">
                        {{ item.modulo_padre ? item.modulo_padre.nombre :'' }}
                    </template>

                    <template #bottom>
                        <VDivider />
                        <div class="d-flex flex-column pa-4" style="padding-left: 24px!important;padding-right: 24px!important;">
                            <!-- Select para la cantidad de registros por página -->
                            <div class="d-flex gap-2 align-center">
                                <p class="text-body-1 mb-0">Ver</p>
                                <AppSelect
                                    :model-value="itemsPerPage"
                                    :items="items_selects"
                                    style="inline-size: 7.0rem;"
                                    @update:model-value="itemsPerPage = parseInt($event, 10)"
                                />
                            </div>

                            <!-- Texto de información y la paginación -->
                            <div class="d-flex justify-space-between align-center w-100">
                                <!-- Texto de "Mostrando X al Y de Z registros" -->
                                <span class="text-caption text-secondary">Mostrando {{ (page - 1) * itemsPerPage + 1 }} al {{ Math.min(page * itemsPerPage, totalItems) }} de {{ totalItems }} registros</span>

                                <!-- Paginación -->
                                <VPagination
                                    v-model="page"
                                    :length="totalPages"
                                    :total-visible="5"
                                    :show-first-last-page="false"
                                    active-color="info"
                                />
                            </div>
                        </div>
                    </template>

                    <!-- Actions -->
                    <template #item.actions="{ item }">
                        <VBtn
                            icon
                            size="small"
                            color="medium-emphasis"
                            variant="text"
                            @click="handleAction(item)"
                        >
                            <VIcon
                                size="22"
                                icon="tabler-edit"
                            />
                        </VBtn>
                        <VBtn
                            icon
                            size="small"
                            color="medium-emphasis"
                            variant="text"
                            @click="eliminarRegistro(item.id)"
                        >
                            <VIcon
                                icon="tabler-trash"
                                size="22"
                            />
                        </VBtn>
                        <!-- <IconBtn>
                        <VIcon
                            icon="tabler-dots-vertical"
                            size="22"
                        />
                        </IconBtn> -->
                    </template>

                </VDataTableServer>

            </VCard>

            <!-- Componente dinámico -->
            <component
                :is="dynamicComponent"
                v-bind="localComponentProps"

                @update:is-dialog-visible="closeModal"
                @refreshTable="reloadTable"
            />

        </VCol>
    </VRow>
</template>
