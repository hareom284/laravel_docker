<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  togglerIcon: {
    type: String,
    required: false,
    default: 'mdi-view-grid-plus-outline',
  },
  shortcuts: {
    type: Array,
    required: true,
  },
})


</script>

<template>
  <IconBtn>
    <VIcon :icon="props.togglerIcon" />

    <VMenu
      activator="parent"
      offset="14px"
      location="bottom end"
    >
      <VCard
        width="340"
        max-height="560"
        class="d-flex flex-column"
      >
        <VCardItem class="py-4">
          <VCardTitle>Shortcuts</VCardTitle>

          <template #append>
            <IconBtn>
              <VIcon icon="mdi-view-grid-plus-outline" />
            </IconBtn>
          </template>
        </VCardItem>

        <VDivider />

        <PerfectScrollbar :options="{ wheelPropagation: false }">
          <VRow class="ma-0 mt-n1">
            <VCol
              v-for="(shortcut, index) in props.shortcuts"
              :key="shortcut.title"
              cols="6"
              class="text-center border-t cursor-pointer pa-4"
              :class="(index + 1) % 2 ? 'border-e' : ''"
             
            >
              <VAvatar
                variant="tonal"
                size="48"
              >
                <VIcon :icon="shortcut.icon" />
              </VAvatar>

              <h6 class="text-base font-weight-semibold mt-2 mb-0">
                {{ shortcut.title }}
              </h6>
              <span class="text-sm">{{ shortcut.subtitle }}</span>
            </VCol>
          </VRow>
        </PerfectScrollbar>
      </VCard>
    </VMenu>
  </IconBtn>
</template>
