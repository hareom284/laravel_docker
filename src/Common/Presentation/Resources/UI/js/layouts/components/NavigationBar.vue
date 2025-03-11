<script setup>
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";

const emit = defineEmits(['toggle_drawer']);
const props = defineProps(['checkLayout'])
const drawer = ref(props.checkLayout === 'vertical' ? true : false);
const page = usePage().props;

const toggle = () => {
    drawer.value = !drawer.value;
    emit('toggle_drawer', drawer.value);
}
</script>

<template>
    <!-- Vertical -->
    <v-app-bar v-if="checkLayout === 'vertical'" style="position: fixed;">
      <v-app-bar-nav-icon
        variant="text"
        @click="toggle"
        class="d-flex"
      ></v-app-bar-nav-icon>
      <VSpacer />
      <NavbarThemeSwitcher class="me-1" />
      <NavBarNotifications class="me-3" />
      <UserProfile class="pe-15" />
    </v-app-bar>

    <!-- Horizontal -->
    <v-app-bar v-else elevation="0" class="w-100">
        <!-- mobile side navigation -->
        <v-app-bar-nav-icon variant="text" @click="toggle" class="d-flex d-md-none">aaa</v-app-bar-nav-icon>

        <Link to="/" class="d-none d-md-flex align-start gap-x-2 ps-15">
        <img :src="$page?.props?.site_logo" width="40" height="40" />
        <h1 class="font-weight-bold leading-normal text-truncate text-xl">
          {{ $page?.props?.site_settings?.site_name }}
        </h1>
        </Link>

        <VSpacer />
        <NavbarThemeSwitcher class="me-1" />
        <!-- <NavbarShortcuts class="me-1" /> -->
        <NavBarNotifications class="me-3" />
        <UserProfile class="d-none d-md-flex pe-15" />
        <UserProfile class="d-flex d-md-none pe-3" />
      </v-app-bar>
</template>