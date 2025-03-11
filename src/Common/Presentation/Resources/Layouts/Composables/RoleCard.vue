<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    avatars: {
        type: Array,
        default: [],
    },
    totalText: {
        type: String,
        default: "",
    },
    btnText: {
        type: String,
        default: "",
    },
    url: {
        type: String,
        default: "#",
    },
});
</script>
<template>
    <!-- 👉 Robert Meyer -->
    <VCol cols="12" sm="6" md="4">
        <VCard>
            <VCardText class="position-relative">
                <!--  Mutual Friends -->
                <div class="d-flex justify-space-between align-center mb-3">
                    <span class="font-weight-medium text-xs">{{
                        totalText
                    }}</span>

                    <div class="v-avatar-group">
                        <VAvatar
                            v-for="avatar in avatars"
                            :key="avatar"
                            :image="avatar"
                            size="45"
                        />
                    </div>
                </div>
                <div class="d-flex justify-space-between align-center">
                    <div>
                        <p class="text-xl mb-1">{{ title }}</p>
                        <Link :href="url">{{ btnText }}</Link>
                    </div>
                    <div>
                        <v-btn
                            density="compact"
                            icon="mdi-content-copy"
                            variant="text"
                            color="secondary"
                        ></v-btn>
                    </div>
                </div>
            </VCardText>
        </VCard>
    </VCol>
</template>
