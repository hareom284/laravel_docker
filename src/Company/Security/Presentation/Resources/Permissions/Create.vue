<script setup>
// component
import ImageUpload from "@Composables/ImageUpload.vue";
import { toastAlert } from "@Composables/useToastAlert";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";
import { emailValidator, requiredValidator } from "@validators";

const isFormValid = ref(false);
const refForm = ref();
const isDialogVisible = ref(false);

let form = useForm({
    name: "",
    description: "",
});
let props = defineProps(["flash"]);

// submit create form
let handleSubmit = () => {
    refForm.value?.validate().then(({ valid }) => {
        if (valid) {
            form.post(route("permissions.store"), {
                onSuccess: () => {
                    toastAlert({
                        title: props.flash?.successMessage,
                    });
                    form.reset();
                    refForm.value?.reset();
                    refForm.value?.resetValidation();
                    isDialogVisible.value = false;
                },
                onError: (error) => {},
            });
        }
    });
};
</script>

<template>
    <VDialog v-model="isDialogVisible" max-width="500" persistent>
        <!-- Dialog Activator -->
        <template #activator="{ props }">
            <VBtn v-bind="props"> Add New </VBtn>
        </template>

        <!-- Dialog Content -->
        <VCard>
            <VCardTitle>
                <span class="text-xl">Permission Details</span>
            </VCardTitle>
            <VCardSubtitle> </VCardSubtitle>
            <VForm
                ref="refForm"
                v-model="isFormValid"
                @submit.prevent="handleSubmit"
            >
                <DialogCloseBtn
                    variant="text"
                    size="small"
                    @click="isDialogVisible = false"
                />
                <VCardText>
                    <VRow>
                        <!-- 👉  name -->
                        <VCol cols="12">
                            <VTextField
                                :error-messages="form.errors?.name"
                                v-model="form.name"
                                :rules="[requiredValidator]"
                                label="Permission Name"
                                density="compact"
                            />
                        </VCol>
                        <!-- 👉  name -->
                        <VCol cols="12">
                            <VTextarea
                                :error-messages="form.errors?.description"
                                v-model="form.description"
                                label="Description"
                            />
                        </VCol>
                        <!-- 👉 Submit and Cancel -->
                        <VCol cols="12" class="d-flex justify-center">
                            <VBtn type="submit" class="me-3"> Submit </VBtn>
                            <VBtn
                                type="reset"
                                variant="outlined"
                                color="secondary"
                                @click="isDialogVisible = false"
                            >
                                Cancel
                            </VBtn>
                        </VCol>
                    </VRow>
                </VCardText>
            </VForm>
        </VCard>
    </VDialog>
</template>
