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
    const isAlertVisible = ref(false);

    definePage({
        meta: {
            layout: 'blank',
            unauthenticatedOnly: true,
        },
    })

    const isPasswordVisible = ref(false)
    const route = useRoute()
    const router = useRouter()
    const ability = useAbility()

    const error_auth = ref('');

    const errors = ref({
        email: undefined,
        password: undefined,
    })

    const refVForm = ref()

    const credentials = ref({
        email: 'admin@gmail.com',
        password: '123456',
    })

    const rememberMe = ref(false)

    const login = async () => {
        try {

            isDialogVisible.value = true
            const res = await $api('/login', {
                method: 'POST',
                body: {
                    email: credentials.value.email,
                    password: credentials.value.password,
                },
                async onResponseError({ response }) {
                    error_auth.value = response._data.error;
                    isAlertVisible.value = true;
                    isDialogVisible.value = false;
                    errors.value.email = response._data.message
                },

                async onResponse({ response }) {
                    if (response.status === 200 || response._data.access_token){
                        error_auth.value = '';
                        isAlertVisible.value = false;


                        const userAbilityRules = [
                            {
                                action: 'manage',
                                subject: 'all',
                            },
                        ];
                        useCookie('userAbilityRules').value = userAbilityRules
                        ability.update(userAbilityRules)
                        useCookie('accessToken').value = response._data.access_token//accessToken
                        useCookie('userData').value = {}

                        /* await nextTick(() => {
                            router.replace(route.query.to ? String(route.query.to) : 'offices')
                        }) */
                        const targetRoute = route.query.to ? String(route.query.to) : 'offices';
                        isDialogVisible.value = false;
                        // Verificar si la ruta existe antes de redirigir
                        if (router.hasRoute(targetRoute)) {
                            console.warn(`Ruta encontrada: ${targetRoute}`);

                            router.replace(targetRoute);
                        } else {
                            console.warn(`Ruta no encontrada: ${targetRoute}`);
                        }
                    }

                }

            })

        } catch (err) {
            console.error(err)
        }
    }

    const onSubmit = () => {
        refVForm.value?.validate().then(({ valid: isValid }) => {
            if (isValid)
            login()
        })
    }

    const getFirstChildRoute = (menu) => {
        // Función recursiva para obtener el primer hijo con ruta en el último nivel
        const findDeepestRoute = (modulo) => {
            if (modulo.submodulos && modulo.submodulos.length > 0) {
                return findDeepestRoute(modulo.submodulos[0]); // Explora el primer submódulo
            }
            return modulo.ruta; // Retorna la ruta cuando ya no tiene más hijos
        };

        // Toma el primer módulo del menú
        if (menu.length > 0) {
            return findDeepestRoute(menu[0]);
        }
        return null; // Si el menú está vacío, retorna null
    };

    /* watch(isDialogVisible, value => {
        if (!value)
            return

        setTimeout(() => {
            isDialogVisible.value = false
        }, 4000)
    }) */
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
                        Bienvenido a<span class="text-capitalize"> Sistema de Tramite Documentario </span>! 👋🏻
                    </h4>
                    <p class="mb-0">Inicie sesión en su cuenta y comience la aventura.</p>
                </VCardText>
                <div class="alert-demo-v-model-wrapper">
                    <VAlert
                        v-model="isAlertVisible"
                        color="error"
                        type="error"
                        variant="tonal"
                    >
                        {{error_auth}}
                    </VAlert>
                </div>
                <VCardText>
                    <VForm
                        ref="refVForm"
                        @submit.prevent="onSubmit"
                    >
                        <VRow>
                            <!-- email -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="credentials.email"
                                    label="Email"
                                    placeholder="johndoe@email.com"
                                    type="email"
                                    autofocus
                                    :rules="[requiredValidator, emailValidator]"
                                    :error-messages="errors.email"
                                />
                            </VCol>

                            <!-- password -->
                            <VCol cols="12">
                                <AppTextField
                                    v-model="credentials.password"
                                    label="Password"
                                    placeholder="············"
                                    :rules="[requiredValidator]"
                                    :type="isPasswordVisible ? 'text' : 'password'"
                                    autocomplete="password"
                                    :error-messages="errors.password"
                                    :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                                />

                                <div class="d-flex align-center flex-wrap justify-space-between my-6">
                                    <VCheckbox
                                        v-model="rememberMe"
                                        label="Recuerdame"
                                    />
                                <!--  <RouterLink
                                    class="text-primary ms-2 mb-1"
                                    :to="{ name: 'forgot-password' }"
                                >
                                    Forgot Password?
                                </RouterLink> -->
                                </div>

                                <VBtn
                                    block
                                    type="submit"
                                >
                                    Login
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
                Verificando credenciales...
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
