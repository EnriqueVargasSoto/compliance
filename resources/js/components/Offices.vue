<script setup>
const props = defineProps({
  languages: {
    type: Array,
    required: true,
  },
  location: {
    type: null,
    required: false,
    default: 'bottom end',
  },
})

const route = useRoute()
const router = useRouter()


const officeSelect = ref(useCookie('officeSelect'));
console.log('officeSelect:', officeSelect.value);
console.log('offices:', props.languages);

const offices = JSON.parse(sessionStorage.getItem('offices')) || [];

const selectOffice = (officeId) => {
    const selectedOffice = offices.find(office => office.id === officeId);
    officeSelect.value = props.languages.find(office => office.id === officeId);

    if (!selectedOffice) {
        console.error("Oficina no encontrada");
        return;
    }
    console.log('selectedOffice:', selectedOffice);
    // Guardar en Cookies
    useCookie('officeSelect').value = officeSelect.value;
    useCookie('roles').value = selectedOffice.roles;
    useCookie('permissions').value = selectedOffice.permissions;
    useCookie('modules').value = selectedOffice.modules;
    useCookie('routeInitial').value = selectedOffice.modules[0].to;
    location.reload();
};

</script>

<template>
  <IconBtn>
    <VIcon icon="tabler-refresh" />

    <!-- Menu -->
    <VMenu
      activator="parent"
      :location="props.location"
      offset="12px"
      width="175"
    >
      <!-- List -->
      <VList
        :selected="[officeSelect.id]"
        color="primary"
      >
        <!-- List item -->
        <VListItem
          v-for="lang in props.languages"
          :key="lang.id"
          :value="lang.id"
          @click="selectOffice(lang.id)"
        >
          <!-- Language label -->
          <VListItemTitle>
            {{ lang.name }}
          </VListItemTitle>
        </VListItem>
      </VList>
    </VMenu>
  </IconBtn>
</template>
