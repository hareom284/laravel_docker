<template>
  <v-list-item
    :prepend-icon="item.icon.icon"
    :title="item.title"
    :value="item.title"
    :class="isLinkActive(item.route_name) ? 'bg-primary' : ''"
    :color="isLinkActive(item.route_name) ? '#fff' : ''"
    @click="goLink(item.url)"
    :hidden="
      !auth?.data?.permissions?.includes(item?.access_module) &&
      item?.access_module != 'access_dashboard'
        ? true
        : false
    "
  ></v-list-item>
</template>
<script setup>
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
const auth = computed(() => usePage().props.auth);
defineProps(["item"]);
let isLinkActive = (currentRoute) => {
  return route().current().includes(currentRoute);
};
let goLink = (url) => {
  router.get(url);
};
</script>