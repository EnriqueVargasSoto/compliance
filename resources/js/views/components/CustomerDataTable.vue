<script setup>

    const props = defineProps({
        endpoint: String, // Ruta API
        paramsInit: {
            type: Object,
            default: () => ({}),
        },
        dynamicComponent: {
            type: Object,
            required: false,
            default: null
        },
        dynamicComponentShowProcesses: {
            type: Object,
            required: false,
            default: null
        },
        componentProps: {
            type: Object,
            default: () => ({}),
        },
        componentPropsShowProcesses: {
            type: Object,
            default: () => ({}),
        },

    })

    //Loading visible
    const isDialogVisibleLoading = ref(false);
    //Variables para la tabla
    const title = ref('');
    const headers = ref([]);
    const items_selects = ref([]);
    const permissions = ref([]);
    const button_add = ref({});
    const params = ref({
        page: 1,
        per_page: 5,
        search: null,
    });
    //Traer data y paginacion
    const totalPages = computed(() => {
        return data.value?.recordsTotal
            ? Math.ceil(data.value.recordsTotal / params.value.per_page)
            : 1; // Evita NaN si `data.value.total` no está definido
    });
    const { data } = await useApi(createUrl(`/${props.endpoint}`, {query: params.value,}) ?? {});
    const tableData = computed(() => data.value?.data ?? []);
    const totalItems = computed(() => data.value?.recordsTotal ?? 0);

    const localComponentProps = ref({ ...props.componentProps });
    const localComponentPropsShowProcesses = ref({ ...props.componentPropsShowProcesses });

    const emit = defineEmits(["update:componentProps", "update:componentPropsShowProcesses"]);

    //Funciones
    //Función verificar permiso
    const hasPermission = (permiso) => {
        return permissions.value.includes(permiso);
    };

    // Función para inicializar la tabla (obtener configuración inicial)
    const fetchInitTabla = async () => {
        isDialogVisibleLoading.value = true;
        try {
            const { data } = await useApi(createUrl(`/${props.endpoint}-init-table`,{query:props.paramsInit}));

            headers.value = data?.value.data?.headers || [];
            title.value = data?.value.data?.title || "Tabla";
            params.value.per_page = data?.value.data?.per_page || 10;
            params.value.page = data?.value.data?.page || 1;
            items_selects.value = data?.value.data?.item_selects || [];
            /* items_selects.value.push({ value: totalItems, title: 'Todos' }); */
            permissions.value = data?.value.data?.permissions || [];
            button_add.value = data?.value.data?.button_add || {};
            isDialogVisibleLoading.value = false;
        } catch (error) {
            isDialogVisibleLoading.value = false;
            console.error("Error al cargar la configuración de la tabla:", error);
            //await logout();
        }
    };

    const handleAction = (item, action) => {
        switch (action) {
            case 'edit':
            // 🔥 Resetear batch antes de asignar nuevos valores
            localComponentProps.value.data = {};
            nextTick(() => {
                localComponentProps.value.data = item;
                localComponentProps.value.isDialogVisible = true;
            });
            break;
            case 'delete':
            console.log(`Eliminar lote ID: ${item.batch}, ${action}`);
            break;
            case 'show_processes':
                console.log('show: ',localComponentPropsShowProcesses.value );
                localComponentPropsShowProcesses.value.data = {};
                nextTick(() => {
                    localComponentPropsShowProcesses.value.data = item;
                    localComponentPropsShowProcesses.value.isDialogVisibleShowProcesses = true;
                });
                console.log(`Ver procesos del lote ID: ${item}, ${action}`);
            break;
            case 'create':
            localComponentProps.value.isDialogVisible = true;

            break;
        }
    };

    const closeModal = () => {
        localComponentProps.value.isDialogVisible = false;
        /* localComponentPropsShowProcesses.isDialogVisibleShowProcesses = false; */
        emit("update:componentProps", { ...localComponentProps.value });
        /* emit("update:componentPropsShowProcesses", { ...localComponentPropsShowProcesses.value }); */
    };

    const closeModalShowProcesses = () => {

        localComponentPropsShowProcesses.value.isDialogVisibleShowProcesses = false;

        emit("update:componentPropsShowProcesses", { ...localComponentPropsShowProcesses.value });
    };

    // Llamar funciones una vez al montar el componente
    onMounted(async () => {
        await fetchInitTabla();
    });

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
                            <!-- Botón Crear Nuevo Módulo (Fuera de la Tabla) -->

                            <VBtn
                                v-if="hasPermission(`${props.paramsInit.module_name}.create`)"
                                :density="button_add.density"
                                :prepend-icon="button_add.icon"
                                :color="button_add.color"
                                @click="handleAction(null, 'create')"
                                :disabled="disabled"
                            >
                                {{ button_add.label }}
                            </VBtn>
                        </div>
                    </div>

                </VCardItem>

                <VCardText class="d-flex align-center justify-space-between flex-wrap gap-4">
                    <div class="d-flex align-center gap-4 flex-wrap">
                        <AppTextField

                            v-model="params.search"
                            placeholder="Buscar..."
                            style="inline-size: 15.625rem;"
                        />

                    </div>
                </VCardText>

                <VDivider />

                <VDataTableServer
                    v-model:items-per-page="params.per_page"
                    v-model:page="params.page"
                    :items-length="totalItems"
                    :headers="headers"
                    :items="tableData"
                    :show-select="true"
                    prev-page-label="'Previous'"
                    item-value="name"
                    class="text-no-wrap"
                >

                    <template #item.actions="{ item }">

                        <VMenu>
                            <template v-slot:activator="{ props }">
                                <VBtn v-bind="props" icon size="small">
                                    <VIcon>tabler-dots-vertical</VIcon>
                                </VBtn>
                            </template>

                            <VList>
                                <VListItem v-for="(permiso, index) in item.permisos" :key="index" @click="handleAction(item, permiso.action)">
                                    <VListItemTitle>
                                        <VIcon small class="mr-2">{{ permiso.icon }}</VIcon>
                                        {{ permiso.title }}
                                    </VListItemTitle>
                                </VListItem>
                            </VList>
                        </VMenu>
                        <!-- <div class="me-n2">
                            <MoreBtn
                                size="small"
                                :menu-list="moreList"
                            />
                        </div> -->
                    </template>

                    <!-- Actions -->



                    <template #bottom>
                        <VDivider />
                        <div class="d-flex flex-column pa-4" style="padding-left: 24px!important;padding-right: 24px!important;">
                            <!-- Select para la cantidad de registros por página -->
                            <div class="d-flex gap-2 align-center">
                                <p class="text-body-1 mb-0">Ver</p>
                                <AppSelect
                                    :model-value="params.per_page"
                                    :items="items_selects"
                                    style="inline-size: 7.0rem;"
                                    @update:model-value="params.per_page = parseInt($event, 10)"
                                />
                            </div>

                            <!-- Texto de información y la paginación -->
                            <div class="d-flex justify-space-between align-center w-100">
                                <!-- Texto de "Mostrando X al Y de Z registros" -->
                                <span class="text-caption text-secondary">Mostrando {{ (params.page - 1) * params.per_page + 1 }} al {{ Math.min(params.page * params.per_page, totalItems) }} de {{ totalItems }} registros</span>

                                <!-- Paginación -->
                                <VPagination
                                    v-model="params.page"
                                    :length="totalPages"
                                    :total-visible="5"
                                    :show-first-last-page="false"
                                    active-color="info"
                                />
                            </div>
                        </div>
                    </template>
                </VDataTableServer>

            </VCard>

             <!-- Componente dinámico -->
             <component
                :is="dynamicComponentShowProcesses"
                v-bind="localComponentPropsShowProcesses"

                @update:is-dialog-visible-show-processes="closeModalShowProcesses"
                @refreshTable="reloadTable"
            />

            <!-- Componente dinámico -->
            <component
                :is="dynamicComponent"
                v-bind="localComponentProps"

                @update:is-dialog-visible="closeModal"
                @refreshTable="reloadTable"
            />



        </VCol>
    </VRow>

     <!-- Dialog -->
     <VDialog
        v-model="isDialogVisibleLoading"
        width="300"
    >
        <VCard
        color="primary"
        width="300"
        >
            <VCardText class="pt-3">
                Cargando...
                <VProgressLinear
                indeterminate
                bg-color="rgba(var(--v-theme-surface), 0.1)"
                :height="8"
                class="mb-0 mt-4"
                />
            </VCardText>
        </VCard>
    </VDialog>
</template>
<style scoped>
</style>