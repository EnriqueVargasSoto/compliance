<script setup>
import { type } from '../demos/components/alert/demoCodeAlert';

    const props = defineProps({
        endpoint: String, // Ruta API
        paramsInit: {
            type: Object,
            default: () => ({}),
        },

    })

    const isDialogVisible = ref(false);

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

    const totalPages = computed(() => {
        return data.value?.recordsTotal
            ? Math.ceil(data.value.recordsTotal / params.value.per_page)
            : 1; // Evita NaN si `data.value.total` no está definido
    });

    //data obtenida del api
    const { data } = await useApi(createUrl(`/${props.endpoint}`, {query: params.value,}) ?? {});
    const tableData = computed(() => data.value?.data ?? []);
    const totalItems = computed(() => data.value?.recordsTotal ?? 0);

    // Función para verificar si el usuario tiene un permiso específico
    const hasPermission = (permiso) => {
        return permissions.value.includes(permiso);
    };

    // Función para inicializar la tabla (obtener configuración inicial)
    const fetchInitTabla = async () => {
        isDialogVisible.value = true;
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
            isDialogVisible.value = false;
        } catch (error) {
            isDialogVisible.value = false;
            console.error("Error al cargar la configuración de la tabla:", error);
            //await logout();
        }
    };

    // Llamar `fetchInitTabla` una vez al montar el componente
    onMounted(async () => {await fetchInitTabla();});

    const menuList = ref([
                {
                    title: "Ver Procesos",
                    icon: "tabler-eye-spark",
                    action: "ver_proceso"
                },
                {
                    title: "Editar",
                    icon: "tabler-pencil",
                    action: "editar"
                },
                {
                    title: "Eliminar",
                    icon: "tabler-trash",
                    action: "eliminar"
                }
            ]);

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
                                @click="handleAction(null)"
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
                                <VListItem v-for="(permiso, index) in item.permisos" :key="index" @click="permiso.action">
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
        </VCol>
    </VRow>

     <!-- Dialog -->
     <VDialog
        v-model="isDialogVisible"
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