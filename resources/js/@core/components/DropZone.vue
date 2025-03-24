<script setup>
import {
  useDropZone,
  useFileDialog,
  useObjectUrl,
} from '@vueuse/core'

import * as pdfjsLib from 'pdfjs-dist'
import pdfWorker from 'pdfjs-dist/build/pdf.worker?url'

// Configurar worker de PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker

const dropZoneRef = ref()
const fileData = ref([])
//const totalPages = ref(0) // Contador total de páginas

const totalPages = computed(() =>
  fileData.value.reduce((sum, file) => sum + (file.pages || 0), 0)
)

const emit = defineEmits(['update:totalPages'])

const { open, onChange } = useFileDialog({ accept: 'application/pdf', multiple: true });
//const { open, onChange } = useFileDialog({ accept: 'image/*' })

// Emitir la suma de páginas al padre cada vez que cambie la lista de archivos
const updateTotalPages = () => {
    console.log('va actualizar mas: ',totalPages.value);
  emit('update:totalPages', totalPages.value)
}

async function getPdfPageCount(file) {
  const url = URL.createObjectURL(file)
  const loadingTask = pdfjsLib.getDocument(url)
  const pdf = await loadingTask.promise
  return pdf.numPages
}

async function processPdf(file) {
  const numPages = await getPdfPageCount(file)
  //totalPages.value += numPages // Sumar páginas al total
  console.log('paginas totales: ', numPages);
  return {
    file,
    url: useObjectUrl(file).value ?? '',
    pages: numPages, // Guardamos el número de páginas del archivo
  }
}

async function onDrop(DroppedFiles)  {
  await DroppedFiles?.forEach(async(file )=> {
    if (file.type === 'application/pdf') {
      const fileInfo = await processPdf(file)
      fileData.value.push(fileInfo)
      updateTotalPages() // Emitir la suma actualizada
    }
    /* if (file.type.slice(0, 6) !== 'image/') {

      // eslint-disable-next-line no-alert
      alert('Only image files are allowed')

      return
    }
    fileData.value.push({
      file,
      url: useObjectUrl(file).value ?? '',
    }) */
  })
}
onChange(async (selectedFiles) => {
    console.log('entra al change');
  if (!selectedFiles)
    return
  for (const file of selectedFiles) {
    if (file.type === 'application/pdf') {
      const fileInfo = await processPdf(file)
      fileData.value.push(fileInfo)
      updateTotalPages() // Emitir la suma actualizada
    }
    /* fileData.value.push({
      file,
      url: useObjectUrl(file).value ?? '',
    }) */
  }
})
useDropZone(dropZoneRef, onDrop)
</script>

<template>
  <div class="flex">
    <div class="w-full h-auto relative">
      <div
        ref="dropZoneRef"
        class="cursor-pointer"
        @click="() => open()"
      >
        <div
          v-if="fileData.length === 0"
          class="d-flex flex-column justify-center align-center gap-y-2 pa-12 drop-zone rounded"
        >
          <IconBtn
            variant="tonal"
            class="rounded-sm"
          >
            <VIcon icon="tabler-upload" />
          </IconBtn>
          <h4 class="text-h4">
            Arrastra y suelta tu documento aquí.
          </h4>
          <span class="text-disabled">or</span>

          <VBtn
            variant="tonal"
            size="small"
          >
          Explorar docuemntos
          </VBtn>
        </div>

        <div
          v-else
          class="d-flex justify-center align-center gap-3 pa-8 drop-zone flex-wrap"
        >
          <VRow class="match-height w-100">
            <template
              v-for="(item, index) in fileData"
              :key="index"
            >
              <VCol
                cols="12"
                sm="4"
              >
                <VCard :ripple="false">
                  <VCardText
                    class="d-flex flex-column"
                    @click.stop
                  >
                    <!-- <VImg
                      :src="item.url"
                      width="200px"
                      height="150px"
                      class="w-100 mx-auto"
                    /> -->
                    <template v-if="item.file.type === 'application/pdf'">
                        <iframe
                        :src="item.url"
                        width="200px"
                        height="150px"
                        class="w-100 mx-auto"
                        style="border: 1px solid #ccc;"
                        ></iframe>
                    </template>

                    <template v-else>
                        <VImg
                        :src="item.url"
                        width="200px"
                        height="150px"
                        class="w-100 mx-auto"
                        />
                    </template>
                    <div class="mt-2">
                      <span class="clamp-text text-wrap">
                        {{ item.file.name }}
                      </span>
                      <span class="clamp-text text-wrap">
                        {{ item.file.size / 1000 }} KB
                      </span>
                      <span>
                        {{ item.pages }} paginas
                      </span>
                    </div>
                  </VCardText>
                  <VCardActions>
                    <VBtn
                      variant="text"
                      block
                      @click.stop="fileData.splice(index, 1)"
                    >
                      Eliminar Documento
                    </VBtn>
                  </VCardActions>
                </VCard>
              </VCol>
            </template>
          </VRow>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.drop-zone {
  border: 1px dashed rgba(var(--v-theme-on-surface), var(--v-border-opacity));
}
</style>
