<script setup>
import { VForm } from 'vuetify/components/VForm'

const totalPages = ref(0)

// Formatear la fecha actual en 'Y-m-d h:i A'
const formatDate = (date) => {
    const pad = (num) => num.toString().padStart(2, '0');

    let year = date.getFullYear();
    let month = pad(date.getMonth() + 1);
    let day = pad(date.getDate());
    let hours = date.getHours();
    let minutes = pad(date.getMinutes());
    let ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12 || 12; // Convierte a formato 12h

    return `${year}-${month}-${day} ${pad(hours)}:${minutes} ${ampm}`;
};

// Definir la fecha por defecto
const date = ref(formatDate(new Date()));

</script>

<template>
    <div>
    <div class="d-flex flex-wrap justify-start justify-sm-space-between gap-y-4 gap-x-6 mb-6">
      <div class="d-flex flex-column justify-center">
        <h4 class="text-h4 font-weight-medium">
          Crear Nuevo Expediente
        </h4>
        <!-- <div class="text-body-1">
          Orders placed across your store
        </div> -->
      </div>

      <div class="d-flex gap-4 align-center flex-wrap">
        <!-- <VBtn
          variant="tonal"
          color="secondary"
        >
          Discard
        </VBtn>
        <VBtn
          variant="tonal"
          color="primary"
        >
          Save Draft
        </VBtn> -->
        <VBtn>Crear Expediente</VBtn>
      </div>
    </div>

    <VRow>
      <VCol md="12" cols="12">
        <!-- 👉 Product Information -->
        <VCard
          class="mb-6"
          title="Informacion de Expediente"
        >
          <VCardText>
            <VRow>
              <VCol cols="12" md="4">
                <AppTextField
                  label="Expediente"
                  value="123456"
                  placeholder="Expediente"
                  disabled
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppSelect
                    placeholder="Tipo Expediente"
                    label="Tipo Expediente"
                    :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppDateTimePicker
                    v-model="date"
                    label="Fecha"
                    placeholder="Fecha de Ingreso de Expediente"
                    :config="{ enableTime: true, dateFormat: 'Y-m-d h:i K' }"
                    disabled
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppSelect
                    placeholder="Prioridad"
                    label="Prioridad"
                    :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppSelect
                    placeholder="Tipo Documento"
                    label="Tipo Documento"
                    :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
                />
              </VCol>

              <VCol cols="12" md="4">
                <AppTextField
                    v-model="totalPages"
                  label="Folios"
                  placeholder="Folios"
                  disabled
                  type="number"
                />
              </VCol>
              <!-- daaaaa -->
            </VRow>
          </VCardText>
        </VCard>

        <!-- 👉 Media -->
        <VCard class="mb-6">
          <VCardItem>
            <template #title>
              Documentos
            </template>
            <template #append>
              <span class="text-primary font-weight-medium text-sm cursor-pointer">Agregue los documentos necesarios</span>
            </template>
          </VCardItem>

          <VCardText>
            <DropZone  v-model:totalPages="totalPages"/>
          </VCardText>
        </VCard>

        <!-- 👉 Product Information -->
        <VCard
          class="mb-6"
          title="Informacion de Expediente"
        >
          <VCardText>
            <VRow>
              <VCol cols="12" md="4">
                <AppTextField
                  label="Expediente"
                  value="123456"
                  placeholder="Expediente"
                  disabled
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppSelect
                    placeholder="Tipo Expediente"
                    label="Tipo Expediente"
                    :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
                />
              </VCol>
              <VCol cols="12" md="4">
                <AppDateTimePicker
                    v-model="date"
                    label="Fecha"
                    placeholder="Fecha de Ingreso de Expediente"
                    :config="{ enableTime: true, dateFormat: 'Y-m-d h:i K' }"
                    disabled
                />
              </VCol>
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  label="SKU"
                  placeholder="FXSK123U"
                />
              </VCol>
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  label="Barcode"
                  placeholder="0123-4567"
                />
              </VCol>
              <VCol>
                <span class="mb-1">Description (optional)</span>
                <ProductDescriptionEditor
                  v-model="content"
                  placeholder="Product Description"
                  class="border rounded"
                />
              </VCol>
            </VRow>
          </VCardText>
        </VCard>



        <!-- 👉 Variants -->
        <VCard
          title="Variants"
          class="mb-6"
        >
          <VCardText>
            <template
              v-for="i in optionCounter"
              :key="i"
            >
              <VRow>
                <VCol
                  cols="12"
                  md="4"
                >
                  <AppSelect
                    :items="['Size', 'Color', 'Weight']"
                    placeholder="Select Variant"
                    label="Options"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="8"
                  class="d-flex align-self-end"
                >
                  <AppTextField
                    placeholder="38"
                    type="number"
                  />
                </VCol>
              </VRow>
            </template>

            <VBtn
              class="mt-6"
              prepend-icon="tabler-plus"
              @click="optionCounter++"
            >
              Add another option
            </VBtn>
          </VCardText>
        </VCard>

        <!-- 👉 Inventory -->
        <VCard
          title="Inventory"
          class="inventory-card"
        >
          <VCardText>
            <VRow>
              <VCol
                cols="12"
                md="4"
              >
                <div class="pe-3">
                  <VTabs
                    v-model="activeTab"
                    direction="vertical"
                    color="primary"
                    class="v-tabs-pill"
                  >
                    <VTab
                      v-for="(tab, index) in inventoryTabsData"
                      :key="index"
                    >
                      <VIcon
                        :icon="tab.icon"
                        class="me-2"
                      />
                      <div class="text-truncate font-weight-medium text-start">
                        {{ tab.title }}
                      </div>
                    </VTab>
                  </VTabs>
                </div>
              </VCol>

              <VDivider :vertical="!$vuetify.display.smAndDown" />

              <VCol
                cols="12"
                md="8"
              >
                <VWindow
                  v-model="activeTab"
                  class="w-100"
                  :touch="false"
                >
                  <VWindowItem value="Restock">
                    <div class="d-flex flex-column gap-y-4 ps-3">
                      <p class="mb-0">
                        Options
                      </p>

                      <div class="d-flex gap-x-4 align-center">
                        <AppTextField
                          label="Add to Stock"
                          placeholder="Quantity"
                        />
                        <VBtn class="align-self-end">
                          Confirm
                        </VBtn>
                      </div>

                      <div>
                        <div class="text-base text-high-emphasis pb-2">
                          Product in stock now: 54
                        </div>
                        <div class="text-base text-high-emphasis pb-2">
                          Product in transit: 390
                        </div>
                        <div class="text-base text-high-emphasis pb-2">
                          Last time restocked: 24th June, 2022
                        </div>
                        <div class="text-base text-high-emphasis pb-2">
                          Total stock over lifetime: 2,430
                        </div>
                      </div>
                    </div>
                  </VWindowItem>

                  <VWindowItem value="Shipping">
                    <VRadioGroup
                      v-model="shippingType"
                      label="Shipping Type"
                      class="ms-3"
                    >
                      <VRadio
                        v-for="item in shippingList"
                        :key="item.value"
                        :value="item.value"
                        class="mb-4"
                      >
                        <template #label>
                          <div>
                            <div class="text-high-emphasis font-weight-medium mb-1">
                              {{ item.title }}
                            </div>
                            <div class="text-sm">
                              {{ item.desc }}
                            </div>
                          </div>
                        </template>
                      </VRadio>
                    </VRadioGroup>
                  </VWindowItem>

                  <VWindowItem value="Global Delivery">
                    <div class="ps-3">
                      <h5 class="text-h5 mb-6">
                        Global Delivery
                      </h5>

                      <VRadioGroup
                        v-model="deliveryType"
                        label="Global Delivery"
                      >
                        <VRadio
                          value="Worldwide delivery"
                          class="mb-4"
                        >
                          <template #label>
                            <div>
                              <div class="text-high-emphasis font-weight-medium mb-1">
                                Worldwide delivery
                              </div>
                              <div class="text-sm">
                                Only available with Shipping method:
                                <span class="text-primary">
                                  Fulfilled by Company name
                                </span>
                              </div>
                            </div>
                          </template>
                        </VRadio>

                        <VRadio
                          value="Selected Countries"
                          class="mb-4"
                        >
                          <template #label>
                            <div>
                              <div class="text-high-emphasis font-weight-medium mb-1">
                                Selected Countries
                              </div>
                              <VTextField
                                placeholder="USA"
                                style="min-inline-size: 200px;"
                              />
                            </div>
                          </template>
                        </VRadio>

                        <VRadio>
                          <template #label>
                            <div>
                              <div class="text-high-emphasis font-weight-medium mb-1">
                                Local delivery
                              </div>
                              <div class="text-sm">
                                Deliver to your country of residence
                                <span class="text-primary">
                                  Change profile address
                                </span>
                              </div>
                            </div>
                          </template>
                        </VRadio>
                      </VRadioGroup>
                    </div>
                  </VWindowItem>

                  <VWindowItem value="Attributes">
                    <div class="ps-3">
                      <div class="mb-6 text-h6">
                        Attributes
                      </div>
                      <div class="d-flex flex-column gap-y-1">
                        <VCheckbox
                          v-model="selectedAttrs"
                          label="Fragile Product"
                          value="Fragile Product"
                        />
                        <VCheckbox
                          v-model="selectedAttrs"
                          value="Biodegradable"
                          label="Biodegradable"
                        />
                        <VCheckbox
                          v-model="selectedAttrs"
                          value="Frozen Product"
                        >
                          <template #label>
                            <div class="d-flex flex-column mb-1">
                              <div>Frozen Product</div>
                              <VTextField
                                placeholder="40 C"
                                type="number"
                              />
                            </div>
                          </template>
                        </VCheckbox>
                        <VCheckbox
                          v-model="selectedAttrs"
                          value="Expiry Date"
                        >
                          <template #label>
                            <div class="d-flex flex-column mb-1">
                              <div>Expiry Date of Product</div>
                              <AppDateTimePicker
                                model-value="2025-06-14"
                                placeholder="Select a Date"
                              />
                            </div>
                          </template>
                        </VCheckbox>
                      </div>
                    </div>
                  </VWindowItem>

                  <VWindowItem value="Advanced">
                    <div class="ps-3">
                      <h5 class="text-h5 mb-6">
                        Advanced
                      </h5>
                      <div class="d-flex flex-sm-row flex-column flex-wrap justify-space-between gap-x-6 gap-y-4">
                        <AppSelect
                          label="Product ID Type"
                          placeholder="Select Product Type"
                          :items="['ISBN', 'UPC', 'EAN', 'JAN']"
                        />
                        <AppTextField
                          label="Product Id"
                          placeholder="100023"
                        />
                      </div>
                    </div>
                  </VWindowItem>
                </VWindow>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        md="4"
        cols="12"
      >
        <!-- 👉 Pricing -->
        <VCard
          title="Pricing"
          class="mb-6"
        >
          <VCardText>
            <AppTextField
              label="Best Price"
              placeholder="Price"
              class="mb-6"
            />
            <AppTextField
              label="Discounted Price"
              placeholder="$499"
              class="mb-6"
            />

            <VCheckbox
              v-model="isTaxChargeToProduct"
              label="Charge Tax on this product"
            />

            <VDivider class="my-2" />

            <div class="d-flex flex-raw align-center justify-space-between ">
              <span>In stock</span>
              <VSwitch density="compact" />
            </div>
          </VCardText>
        </VCard>

        <!-- 👉 Organize -->
        <VCard title="Organize">
          <VCardText>
            <div class="d-flex flex-column gap-y-4">
              <AppSelect
                placeholder="Select Vendor"
                label="Vendor"
                :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
              />
              <div>
                <VLabel class="d-flex">
                  <div class="d-flex text-sm justify-space-between w-100">
                    <div class="text-high-emphasis">
                      Category
                    </div>
                  </div>
                </VLabel>

                <div class="d-flex gap-x-4">
                  <AppSelect
                    placeholder="Select Category"
                    :items="['Household', 'Office', 'Electronics', 'Management', 'Automotive']"
                  />
                  <VBtn
                    rounded
                    icon="tabler-plus"
                    variant="tonal"
                  />
                </div>
              </div>
              <AppSelect
                placeholder="Select Collection"
                label="Collection"
                :items="['Men\'s Clothing', 'Women\'s Clothing', 'Kid\'s Clothing']"
              />
              <AppSelect
                placeholder="Select Status"
                label="Status"
                :items="['Published', 'Inactive', 'Scheduled']"
              />
              <AppTextField
                label="Tags"
                placeholder="Fashion, Trending, Summer"
              />
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style lang="scss" scoped>
  .drop-zone {
    border: 2px dashed rgba(var(--v-theme-on-surface), 0.12);
    border-radius: 6px;
  }
</style>

<style lang="scss">
.inventory-card {
  .v-tabs.v-tabs-pill {
    .v-slide-group-item--active.v-tab--selected.text-primary {
      h6 {
        color: #fff !important;
      }
    }
  }

  .v-radio-group,
  .v-checkbox {
    .v-selection-control {
      align-items: start !important;
    }

    .v-label.custom-input {
      border: none !important;
    }
  }
}

.ProseMirror {
  p {
    margin-block-end: 0;
  }

  padding: 0.5rem;
  outline: none;

  p.is-editor-empty:first-child::before {
    block-size: 0;
    color: #adb5bd;
    content: attr(data-placeholder);
    float: inline-start;
    pointer-events: none;
  }
}
</style>
