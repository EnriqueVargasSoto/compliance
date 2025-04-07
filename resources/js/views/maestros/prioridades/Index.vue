<template>
    <div class="pdf-signer">
      <!-- Contenedor del visor PDF -->
      <div class="pdf-container">
        <!-- Contenedor de la primera página del PDF -->
        <div class="pdf-wrapper">
          <!-- Visor del PDF -->
          <iframe ref="pdfIframe" :src="pdfSrc" class="pdf-iframe"></iframe>

          <!-- Imagen de la firma (Fija en la primera página) -->
          <img v-if="signatureImg" :src="signatureImg" alt="Firma" class="signature" />
        </div>
      </div>

      <!-- Input para cargar el PDF -->
      <input type="file" @change="onFileChange" accept="application/pdf" />

      <!-- Input para cargar la firma -->
      <input type="file" @change="onSignatureUpload" accept="image/*" />
    </div>
  </template>

  <script setup>
  import { ref, nextTick } from "vue";

  const pdfSrc = ref(""); // Fuente del PDF
  const signatureImg = ref(""); // Imagen de la firma

  // 📌 Cargar el PDF
  const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file && file.type === "application/pdf") {
      const reader = new FileReader();
      reader.onload = () => {
        const blob = new Blob([reader.result], { type: "application/pdf" });
        pdfSrc.value = URL.createObjectURL(blob);
      };
      reader.readAsArrayBuffer(file);
    }
  };

  // 📌 Cargar la imagen de firma
  const onSignatureUpload = async (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = async () => {
        signatureImg.value = reader.result;
        await nextTick();
      };
      reader.readAsDataURL(file);
    }
  };
  </script>

  <style scoped>
  .pdf-container {
    width: 500px;
    height: 600px;
    overflow: auto;
    border: 1px solid #ccc;
  }

  /* 📌 Contenedor solo para la primera página */
  .pdf-wrapper {
    position: relative;
    width: 100%;
    height: 793px; /* Altura aproximada de la primera página A4 */
  }

  /* 📌 Iframe del PDF */
  .pdf-iframe {
    width: 100%;
    height: 100%;
  }

  /* 📌 Firma en la primera página (superior izquierda) */
  .signature {
    width: 150px;
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
  }
  </style>
