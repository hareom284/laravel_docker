<script setup>
const props = defineProps({
  selectedRadio: {
    type: String,
    required: true,
  },
  radioContent: {
    type: Array,
    required: true,
  },
  gridColumn: {
    type: null,
    required: false,
  },
})

const emit = defineEmits(['update:selectedRadio'])

const selectedOption = ref(structuredClone(toRaw(props.selectedRadio)))

watch(selectedOption, () => {
  emit('update:selectedRadio', selectedOption.value)
})
</script>

<template>
  <VRadioGroup
    v-if="props.radioContent"
    v-model="selectedOption"
  >
    <VRow>
      <VCol
        v-for="item in props.radioContent"
        :key="item.title"
        v-bind="gridColumn"
      >
        <VLabel
          class="custom-input custom-radio rounded cursor-pointer"
          :class="selectedOption === item.value ? 'active' : ''"
        >
          <div class="d-flex flex-column align-center text-center gap-2">
            <VIcon
              size="32"
              :icon="item.icon"
              class="text-high-emphasis"
            />
            <h6 class="cr-title text-base">
              {{ item.title }}
            </h6>

            <p class="text-sm mb-0">
              {{ item.desc }}
            </p>
          </div>

          <div>
            <VRadio :value="item.value" />
          </div>
        </VLabel>
      </VCol>
    </VRow>
  </VRadioGroup>
</template>

<style lang="scss" scoped>
.custom-radio {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;

  .v-radio {
    margin-block-end: -0.25rem;
  }

  .cr-title {
    font-weight: 500;
  }
}
</style>
