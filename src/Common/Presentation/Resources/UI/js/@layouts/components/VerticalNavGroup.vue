<template>
  <v-list-group :value="item.title">
    <template v-slot:activator="{ props }">
      <v-list-item
        v-bind="props"
        :prepend-icon="item.icon.icon"
        :title="item.title"
        :class="isParentActive(item.children) ? 'bg-primary' : ''"
        :color="isParentActive(item.children) ? '#fff' : ''"
        :hidden="
          !auth?.data?.permissions?.includes(item?.access_module) &&
          item?.access_module != 'access_dashboard'
            ? true
            : false
        "
      ></v-list-item>
    </template>
    <v-list-item
      v-for="(sitem, sindex) in item.children"
      :key="sindex"
      :value="sitem.title"
      prepend-icon="mdi-circle-small"
      :title="sitem.title"
      @click="goLink(sitem, item)"
      :variant="isLinkActive(sitem.route_name) ? 'tonal' : 'text'"
      :hidden="
        !auth?.data?.permissions?.includes(sitem?.access_module) &&
        item?.access_module != 'access_dashboard'
          ? true
          : false
      "
    ></v-list-item>
  </v-list-group>
</template>
<script setup>
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import { defineEmits } from "vue";

const auth = computed(() => usePage().props.auth);
defineProps(["item"]);
const emit = defineEmits(["open_menu"]);
let isLinkActive = (currentRoute) => {
  return route().current().includes(currentRoute);
};
const isParentActive = (routeList) => {
  return routeList.find((item) => route().current().includes(item.route_name));
};
const goLink = (item, pitem) => {
  emit("open_menu", pitem.title);
  if (item?.isNativeLink) {
    window.location.href = item.url;
  } else {
    router.get(item.url);
  }
};
</script>
<style scoped lang="scss">
.active-list {
  background-color: #ededff !important;
  color: #666cff !important;
}
.v-list-group--prepend {
  --parent-padding: var(--indent-padding) !important;
}
</style>
