<template>
  <v-app-bar-nav-icon
    variant="text"
    @click.stop="drawer = !drawer"
    class="d-flex d-md-none"
  ></v-app-bar-nav-icon>
  <v-navigation-drawer v-model="drawer" temporary>
    {{ item }}
  </v-navigation-drawer>
</template>
<script setup>
import { ref, defineProps } from "vue";
let drawer = ref(false);
defineProps(["items"]);
</script>
