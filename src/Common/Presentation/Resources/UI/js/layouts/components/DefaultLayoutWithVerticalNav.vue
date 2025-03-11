<script setup>
import { VNodeRenderer } from "@layouts/components/VNodeRenderer";
import { Link,usePage } from "@inertiajs/inertia-vue3";
import { themeConfig } from "@themeConfig";
import Footer from "@/layouts/components/Footer.vue";
import navItems from "@/navigation/horizontal";
import TheCustomizer from "@core/components/TheCustomizer.vue";
import VerticalNavLink from "@layouts/components/VerticalNavLink.vue";
import VerticalNavGroup from "@layouts/components/VerticalNavGroup.vue";
import NavBarNotifications from "@/layouts/components/NavBarNotifications.vue";
import NavbarShortcuts from "@/layouts/components/NavbarShortcuts.vue";
import NavbarThemeSwitcher from "@/layouts/components/NavbarThemeSwitcher.vue";
import NavSearchBar from "@/layouts/components/NavSearchBar.vue";
import UserProfile from "@/layouts/components/UserProfile.vue";
import NavigationDrawer from "@/layouts/components/NavigationDrawer.vue";
import NavigationBar from "@/layouts/components/NavigationBar.vue";
import MessageBox from "@/layouts/components/MessageBox.vue";

let drawer = ref(true);
let layout = ref('vertical');
const customStyleWithDraWerTrue = ref({
  width: `calc((100% - 420px) - 0px)`,
  left: '420px'
});
const customStyleWithDraWerFalse = ref({
  width: `calc((100% - 256px) - 0px)`,
  left: '256px'
});

const toggleDrawer = t => {
  drawer.value = t;
};


</script>
<template>
  <v-layout>
    <NavigationDrawer :drawer="drawer"/>
    <MessageBox v-show="drawer"/>
    <NavigationBar @toggle_drawer="toggleDrawer" :checkLayout="layout" :style="drawer ? customStyleWithDraWerTrue : customStyleWithDraWerFalse"/>
    <!-- 👉 Customizer -->
    <TheCustomizer />
    <!-- <v-main style="min-height: 100vh; --v-layout-left: 420px;"> -->
    <v-main style="min-height: 100vh;" :style="{ '--v-layout-left' : drawer ? '420px' : '256px' }">
      <v-container>
        <slot />
      </v-container>
    </v-main>
  </v-layout>
</template>
