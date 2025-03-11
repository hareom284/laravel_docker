<script setup>
import { ref, onMounted } from "vue";
import { Link,usePage } from "@inertiajs/inertia-vue3";
import navItems from "@/navigation/horizontal";
import VerticalNavGroup from "@layouts/components/VerticalNavGroup.vue";
import VerticalNavLink from "@layouts/components/VerticalNavLink.vue";


const props = defineProps(['drawer']);
let page = usePage().props;
let open = ref([]);

onMounted(() => {
  let title = localStorage.getItem("menu_title");
  route().current() == "dashboard"
    ? (open.value = ["Dashboard"])
    : (open.value = [title]);
  // localStorage.removeItem("menu_title");
});

const openmenu = title => {
  localStorage.setItem("menu_title", title);
};

const resolveNavItemComponent = item => {
  if ("children" in item) return VerticalNavGroup;

  return VerticalNavLink;
};


</script>
<template>
    <!-- v-model="drawers" -->
    <VNavigation-drawer style="position: fixed;" :style="{ 'width' : drawer ? '70px' : '256px' }"> 
      <template v-slot:prepend>
        <Link to="/" class="d-flex align-start gap-x-2 pa-5">

          <img :src="$page?.props?.site_logo" width="40" height="40" />
          <h1 class="font-weight-bold leading-normal text-truncate text-xl">
           {{$page?.props?.site_settings?.site_name}}
          </h1>
        </Link>
  
      </template>

      <v-divider></v-divider>

      <v-list density="compact" nav v-model:opened="open">
        <Component
          :is="resolveNavItemComponent(item)"
          v-for="(item, index) in navItems"
          :key="index"
          :item="item"
          @open_menu="openmenu"
        />
      </v-list>
    </VNavigation-drawer>
</template>


<style scoped>
.v-list{
  padding: 5px;
}
</style>