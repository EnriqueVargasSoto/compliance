<script setup>
import LogisticsCardStatistics from '@/views/apps/logistics/LogisticsCardStatistics.vue'
import LogisticsDeliveryExpectations from '@/views/apps/logistics/LogisticsDeliveryExpectations.vue'
import LogisticsDeliveryPerformance from '@/views/apps/logistics/LogisticsDeliveryPerformance.vue'
import LogisticsOrderByCountries from '@/views/apps/logistics/LogisticsOrderByCountries.vue'
import LogisticsOverviewTable from '@/views/apps/logistics/LogisticsOverviewTable.vue'
import LogisticsShipmentStatistics from '@/views/apps/logistics/LogisticsShipmentStatistics.vue'
import LogisticsVehicleOverview from '@/views/apps/logistics/LogisticsVehicleOverview.vue'

    const isDialogVisibleLoading = ref(false);

    const cards = ref([]);
    const top5 = ref();

    const fetchCardsDocuments = async () => {

        try {
            const { data } = await useApi(createUrl(`/card-documents`));
            cards.value = data.value;



        } catch (error) {
            isDialogVisibleLoading.value = false;
            console.error("Error al cargar la configuración de la tabla:", error);
            //await logout();
        }
    };

    const fetchTop = async () => {

        try {
            const { data } = await useApi(createUrl(`/top-concepts-clients`));
            top5.value = data.value;



        } catch (error) {
            isDialogVisibleLoading.value = false;
            console.error("Error al cargar la configuración de la tabla:", error);
            //await logout();
        }
    };

    /* watch(() => cards, (newDato) => {

        cards.value = newDato;
    }, {  immediate: true }); */

    // Llamar funciones una vez al montar el componente
    onMounted(async () => {
        isDialogVisibleLoading.value = true;
        await fetchCardsDocuments();
        await fetchTop();
        isDialogVisibleLoading.value = false;
    });

</script>

<template>
  <VRow class="match-height">
    <VCol cols="12">
      <LogisticsCardStatistics :cards="cards"/>
    </VCol>
    <VCol cols="8">
      <LogisticsOverviewTable />
    </VCol>
    <VCol
      cols="12"
      md="4"
    >
      <LogisticsOrderByCountries :top="top5" />
    </VCol>
    <!-- <VCol
      cols="12"
      md="6"
    >
      <LogisticsVehicleOverview />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <LogisticsShipmentStatistics />
    </VCol> -->

    <!-- <VCol
      cols="12"
      md="4"
    >
      <LogisticsDeliveryPerformance />
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <LogisticsDeliveryExpectations />
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <LogisticsOrderByCountries />
    </VCol> -->

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
  </VRow>
</template>
