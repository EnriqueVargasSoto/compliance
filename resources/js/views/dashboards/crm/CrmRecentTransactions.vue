<script setup>
import aeIcon from '@images/icons/payments/ae-icon.png'
import mastercardIcon from '@images/icons/payments/mastercard-icon.png'
import visaIcon from '@images/icons/payments/visa-icon.png'

    const props = defineProps({
        documents:{
            type: Array,
            default: () => [],
        },

    })

    const documents = ref([]);

    watch(() => props.documents, (newDato) => {

        console.log("data en los cards:", newDato);
        documents.value = props.documents;

    }, {  immediate: true });

const lastTransitions = [
  {
    cardImg: visaIcon,
    lastDigit: '*4230',
    cardType: 'Credit',
    sentDate: '17 Mar 2022',
    status: 'Verified',
    trend: '+$1,678',
  },
  {
    cardImg: mastercardIcon,
    lastDigit: '*5578',
    cardType: 'Credit',
    sentDate: '12 Feb 2022',
    status: 'Rejected',
    trend: '-$839',
  },
  {
    cardImg: aeIcon,
    lastDigit: '*4567',
    cardType: 'Credit',
    sentDate: '28 Feb 2022',
    status: 'Verified',
    trend: '+$435',
  },
  {
    cardImg: visaIcon,
    lastDigit: '*5699',
    cardType: 'Credit',
    sentDate: '8 Jan 2022',
    status: 'Pending',
    trend: '+$2,345',
  },
  {
    cardImg: visaIcon,
    lastDigit: '*5699',
    cardType: 'Credit',
    sentDate: '8 Jan 2022',
    status: 'Rejected',
    trend: '-$234',
  },
]

const resolveStatus = {
  Verified: 'success',
  Rejected: 'error',
  Pending: 'secondary',
}

const moreList = [
  {
    title: 'Refresh',
    value: 'refresh',
  },
  {
    title: 'Download',
    value: 'Download',
  },
  {
    title: 'View All',
    value: 'View All',
  },
]

const getPaddingStyle = index => index ? 'padding-block-end: 1.25rem;' : 'padding-block: 1.25rem;'
</script>

<template>
  <VCard title="Últimos Documentos">
    <template #append>
      <div class="me-n2">
        <!-- <MoreBtn
          size="small"
          :menu-list="moreList"
        /> -->
      </div>
    </template>

    <VDivider />
    <VTable class="text-no-wrap transaction-table">
      <thead>
        <tr>
          <th>RUC</th>
          <th>Número de Documento</th>
          <th>Establecimiento</th>
          <th>Cliente</th>
          <th>Estado del Documento</th>
          <!-- <th>
            TREND
          </th> -->
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(transition, index) in documents"
          :key="index"
        >
          <td
            :style="getPaddingStyle(index)"
            style="padding-inline-end: 1.5rem;"
          >
            <div class="d-flex align-center">
              <!-- <div class="me-4">
                <VImg
                  :src="transition.cardImg"
                  width="50"
                />
              </div> -->
              <div>
                <p class="text-base mb-0 text-high-emphasis">
                  {{ transition.client_ruc }}
                </p>
                <!-- <p class="text-sm mb-0">
                  {{ transition.cardType }}
                </p> -->
              </div>
            </div>
          </td>
          <td
            :style="getPaddingStyle(index)"
            style="padding-inline-end: 1.5rem;"
          >
            <!-- <p class="text-high-emphasis text-base mb-0">
              Sent
            </p> -->
            <div class="text-sm">
              {{ transition.document_number }}
            </div>
          </td>
          <td
            :style="getPaddingStyle(index)"
            style="padding-inline-end: 1.5rem;"
            align="right"
          >
            <div class="text-high-emphasis text-base">
              {{ transition.document_location }}
            </div>
          </td>
          <td
            :style="getPaddingStyle(index)"
            style="padding-inline-end: 1.5rem;"
            align="right"
          >
            <div class="text-high-emphasis text-base">
              {{ transition.client_name }}
            </div>
          </td>
          <td
            :style="getPaddingStyle(index)"
            style="padding-inline-end: 1.5rem;"
          >
            <VChip
              label
              :color="resolveStatus['success']"
              size="small"
            >
              {{ transition.document_status }}
            </VChip>
          </td>

        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style lang="scss">
.transaction-table {
  &.v-table .v-table__wrapper > table > tbody > tr:not(:last-child) > td,
  &.v-table .v-table__wrapper > table > tbody > tr:not(:last-child) > th {
    border-block-end: none !important;
  }
}
</style>
