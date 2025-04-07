<script setup>

    const props = defineProps({
        endpoint: String, // Ruta API
        isDialogVisible: {
            type: Boolean,
            required: true,
        },
        data: {
            type: Object,
            default: () => ({}),
        },
        title: {
            type: String,
        }
    })

    const emit = defineEmits([
        'update:isDialogVisible'
    ])

    const refVForm = ref()

    const batch = ref({
        batch: null,
        date_init: null,
        date_end: null,
        status: 0,
        signed: 0
    });

    const rules = {
        required: v => !!v || 'Este campo es obligatorio.',
        email: v => /.+@.+\..+/.test(v) || 'Correo electrónico inválido.',
        minLength: v => (v && v.length >= 3) || 'Debe tener al menos 3 caracteres.'
    };


    const onReset = () => {
        emit('update:isDialogVisible', false)
        batch.value = {
            batch: null,
            date_init: null,
            date_end: null,
            status: 0,
            signed: 0
        };

    }

    const onSubmit = async() => {
        refVForm.value?.validate().then(async ({ valid: isValid }) => {
            if (isValid)
            console.log('validado');
        });
    }

    watch(() => props.data, (item) => {
        console.log('item', item);
        if (item) {
            batch.value = { ...item };
        } else {
            batch.value = {
                batch: null,
                date_init: null,
                date_end: null,
                status: 0,
                signed: 0
            };
        }
    }, {  immediate: true });

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
                    {{ props.dato ? 'Editar' : 'Crear' }} {{props.title}}
                </h4>

                <!-- 👉 Form -->
                <VForm  ref="refVForm" @submit.prevent="onSubmit">
                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="batch.batch"
                            label = "Nombre del Lote"
                            placeholder="Nombre del Lote"
                            :rules="[rules.required]"
                        />
                    </div>

                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="batch.date_init"
                            label = "Fecha de Inicio"
                            placeholder="Fecha de inicio"
                            :rules="[rules.required]"
                        />
                    </div>

                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
                        <AppTextField
                            v-model="batch.date_init"
                            label = "Fecha de Fin"
                            placeholder="Fecha de Fin"
                            :rules="[rules.required]"
                        />
                    </div>

                    <!-- 👉 Role name -->
                    <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row" style="justify-content: flex-end;">
                        <VBtn type="submit">
                            {{ props.data ? 'Actualizar' : 'Agregar' }}
                        </VBtn>
                    </div>
                </VForm>

            </VCardText>
        </VCard>

    </VDialog>
</template>
<style scoped>

</style>