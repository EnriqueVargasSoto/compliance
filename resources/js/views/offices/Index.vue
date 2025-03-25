<!-- ❗Errors in the form are set on line 60 -->
<script setup>
    import { VForm } from 'vuetify/components/VForm'

    import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
    import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
    import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
    import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
    import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
    import authV2MaskDark from '@images/pages/misc-mask-dark.png'
    import authV2MaskLight from '@images/pages/misc-mask-light.png'
    import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
    import { themeConfig } from '@themeConfig'

    const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
    const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

    const isDialogVisible = ref(false);
    //const isAlertVisible = ref(false);

    definePage({
        meta: {
            layout: 'blank',
            unauthenticatedOnly: true,
        },
    })

    const route = useRoute()
    const router = useRouter()
    const ability = useAbility()

    const text_loading = ref('Cargando...');

    const offices = ref([]);
    const officesDetails = ref([]);

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.'
    };

    const refVForm = ref()

    const office = ref(null)

    const login = async () => {

        try {
            text_loading.value = 'Ingresando...';
            isDialogVisible.value = true
            const officeSelect = offices.value.find(item => item.id === office.value);
            const officeSelectDetail = officesDetails.value.find(item => item.id === office.value);

            useCookie('officeSelect').value = officeSelect;
            useCookie('roles').value = officeSelectDetail.roles;
            useCookie('permissions').value = officeSelectDetail.permissions;
            useCookie('modules').value = officeSelectDetail.modules;
            useCookie('routeInitial').value = officeSelectDetail.modules[0].to;
            const targetRoute = route.query.to ? String(route.query.to) : officeSelectDetail.modules[0].to;
            isDialogVisible.value = false;
            // Verificar si la ruta existe antes de redirigir
            if (router.hasRoute(targetRoute)) {
                console.warn(`Ruta encontrada: ${targetRoute}`);

                router.replace(targetRoute);
            } else {
                console.warn(`Ruta no encontrada: ${targetRoute}`);
            }
        } catch (err) {
            isDialogVisible.value = false
            console.error(err)
        }
    }

    const onSubmit = () => {
        refVForm.value?.validate().then(({ valid: isValid }) => {
            if (isValid)
            login()
        })
    }

    const fetchMe = async () => {
        try {
            isDialogVisible.value = true;
            const { data } = await useApi(`/me`);
            offices.value = data.value.user.offices;
            officesDetails.value = data.value.offices;
            useCookie('offices').value = data.value.user.offices;
            useCookie('userData').value = data.value.user.person;
            sessionStorage.setItem('offices', JSON.stringify(data.value.offices));
            isDialogVisible.value = false;

            if (offices.value.length === 1) {
                office.value = offices.value[0].id;

                const officeSelect = offices.value.find(item => item.id === office.value);
                const officeSelectDetail = officesDetails.value.find(item => item.id === office.value);

                useCookie('officeSelect').value = officeSelect;
                useCookie('roles').value = officeSelectDetail.roles;
                useCookie('permissions').value = officeSelectDetail.permissions;
                useCookie('modules').value = officeSelectDetail.modules;
                useCookie('routeInitial').value = officeSelectDetail.modules[0].to;
                const targetRoute = route.query.to ? String(route.query.to) : officeSelectDetail.modules[0].to;

                // Verificar si la ruta existe antes de redirigir
                if (router.hasRoute(targetRoute)) {
                    console.warn(`Ruta encontrada: ${targetRoute}`);

                    router.replace(targetRoute);
                } else {
                    console.warn(`Ruta no encontrada: ${targetRoute.value}`);
                }

            }
        } catch (error) {
            isDialogVisible.value = false;
            console.error("Error al cargar la configuración de la tabla:", error);
        }
    };

    onMounted(async () => {await fetchMe();});

</script>

<template>
    <RouterLink to="/">
        <div class="auth-logo d-flex align-center gap-x-3">
            <VNodeRenderer :nodes="themeConfig.app.logo" />
            <h1 class="auth-title">
                Tramite Documentario
            </h1>
        </div>
    </RouterLink>

    <VRow
        no-gutters
        class="auth-wrapper bg-surface"
    >
        <VCol
            md="8"
            class="d-none d-md-flex"
        >
            <div class="position-relative bg-background w-100 me-0">
                <div
                    class="d-flex align-center justify-center w-100 h-100"
                    style="padding-inline: 6.25rem;"
                >
                    <VImg
                        max-width="613"
                        :src="authThemeImg"
                        class="auth-illustration mt-16 mb-2"
                    />
                </div>

                <img
                    class="auth-footer-mask"
                    :src="authThemeMask"
                    alt="auth-footer-mask"
                    height="280"
                    width="100"
                >
            </div>
        </VCol>

        <VCol
            cols="12"
            md="4"
            class="auth-card-v2 d-flex align-center justify-center"
        >
            <VCard
                flat
                :max-width="500"
                class="mt-12 mt-sm-0 pa-4"
            >
                <VCardText>
                    <h4 class="text-h4 mb-1">
                        Selecciona tu Oficina<span class="text-capitalize"> </span>! 👋🏻
                    </h4>
                    <p class="mb-0">Seleccione la oficina para pdoer ingresar al sistema.</p>
                </VCardText>

                <VCardText>
                    <VForm
                        ref="refVForm"
                        @submit.prevent="onSubmit"
                    >
                        <VRow>
                            <VCol cols="12">

                                <AppSelect
                                    v-model="office"
                                    :items="offices"
                                    item-value="id"
                                    item-title="name"
                                    label="Oficina"
                                    placeholder="Oficina"
                                    :rules="[rules.required]"
                                />
                            </VCol>

                            <VCol cols="12">
                                <VBtn
                                    block
                                    type="submit"
                                >
                                    Ingresar
                                </VBtn>
                            </VCol>

                        </VRow>
                    </VForm>
                </VCardText>
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
                {{text_loading}}
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

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
