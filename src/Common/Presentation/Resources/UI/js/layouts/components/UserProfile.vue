<script setup>
import { initialAbility } from "@/plugins/casl/ability";
import { useAppAbility } from "@/plugins/casl/useAppAbility";
import { usePage, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { router } from "@inertiajs/core";
const ability = useAppAbility();
const userData = computed(() => usePage().props.auth);

const logout = () => {

  localStorage.removeItem("menu_title");
  router.post('/logout');
};
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar class="cursor-pointer" color="primary" variant="tonal">
      <VImg
        v-if="userData?.data && userData?.data?.image"
        :src="userData?.data?.image"
      />
      <VIcon v-else icon="mdi-account-outline" />

      <!-- SECTION Menu -->
      <VMenu activator="parent" width="230" location="bottom end" offset="14px">
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar color="primary" variant="tonal">
                    <VImg
                      v-if="userData?.data && userData?.data?.image"
                      :src="userData?.data?.image"
                    />
                    <VIcon v-else icon="mdi-account-outline" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ userData?.data?.name }}
            </VListItemTitle>
            <VListItemSubtitle>{{
              userData?.data?.roles?.[0]?.name
            }}</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem @click="() => router.get('userprofile')">
            <template #prepend>
              <VIcon class="me-2" icon="mdi-account-outline" size="22" />
            </template>

            <VListItemTitle>Edit Profile</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem link @click="logout">
            <template #prepend>
              <VIcon class="me-2" icon="mdi-logout" size="22" />
            </template>

            <VListItemTitle>Sign Out</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
