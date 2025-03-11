<script setup>
import { useSkins } from "@core/composable/useSkins";
import { useThemeConfig } from "@core/composable/useThemeConfig";
import AppLayout from "./AppLayout.vue";

// @layouts plugin
import { AppContentLayoutNav } from "@layouts/enums";

import DefaultLayoutWithHorizontalNav from "@/layouts/components/DefaultLayoutWithHorizontalNav.vue";
import DefaultLayoutWithVerticalNav from "@/layouts/components/DefaultLayoutWithVerticalNav.vue";
const { width: windowWidth } = useWindowSize();
const { appContentLayoutNav, switchToVerticalNavOnLtOverlayNavBreakpoint } =
    useThemeConfig();

// Remove below composable usage if you are not using horizontal nav layout in your app
switchToVerticalNavOnLtOverlayNavBreakpoint(windowWidth);

const { layoutAttrs, injectSkinClasses } = useSkins();

injectSkinClasses();

let props = defineProps(["current_user_role", "user"]);
</script>
<template>
    <AppLayout>
        <template v-if="appContentLayoutNav === AppContentLayoutNav.Vertical">
            <DefaultLayoutWithVerticalNav v-bind="layoutAttrs">
                <slot />
            </DefaultLayoutWithVerticalNav>
        </template>
        <template v-else>
            <DefaultLayoutWithHorizontalNav v-bind="layoutAttrs">
                <slot />
            </DefaultLayoutWithHorizontalNav>
        </template>
    </AppLayout>
</template>

<style lang="scss">
// As we are using `layouts` plugin we need its styles to be imported
@use "@layouts/styles/default-layout";
</style>
