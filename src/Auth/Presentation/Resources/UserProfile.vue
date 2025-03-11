<script setup>
import { avatarText, kFormatter } from "@core/utils/formatters";
import AdminLayout from "@Layouts/Dashboard/AdminLayout.vue";
import { computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { toastAlert } from "@Composables/useToastAlert";
const page = usePage();
const user = computed(() => page.props.user_info);
const flash = "Password Updated Successfully";

const isUserInfoEditDialogVisible = ref(false);
let form = useForm({
    currentpassword: "",
    updatedpassword: "",
});
const hanleSubmit = (data) => {
    form.currentpassword = data.currentpassword;
    form.updatedpassword = data.updatedpassword;

    form.post(route("changepassword"), {
        onSuccess: (data) => {
            toastAlert({
                title: flash,
            });
            isUserInfoEditDialogVisible.value = false;
            console.log(flash.value);
        },
        onError: (error) => {
            isUserInfoEditDialogVisible.value = true;
        },
    });
};
</script>

<template>
    <AdminLayout>
        <VCard>
            <VRow class="d-flex w-auto">
                <Vcol cols="6">
                    <VCardText class="text-center pt-5">
                        <VAvatar
                            rounded
                            :size="120"
                            color="primary"
                            variant="tonal"
                        >
                            <VImg
                                v-if="user?.user_detail?.image[0]?.original_url"
                                :src="user?.user_detail?.image[0]?.original_url"
                            />
                            <span v-else class="text-5xl font-weight-semibold">
                                {{ avatarText(user?.user_detail?.name) }}
                            </span>
                        </VAvatar>

                        <!-- 👉 User fullName -->
                        <h6 class="text-h6 mt-4">
                            {{ user?.user_detail?.name }}
                        </h6>

                        <!-- 👉 Role chip -->
                        <VChip
                            label
                            size="small"
                            class="text-capitalize bg-info p-3 border-2 mt-4"
                        >
                            {{ user?.user_role?.name }}
                        </VChip>
                    </VCardText>
                    <!-- 👉 Edit and Suspend button -->
                    <VCardText class="d-flex justify-center">
                        <VBtn
                            variant="elevated"
                            @click="isUserInfoEditDialogVisible = true"
                        >
                            Change Password
                        </VBtn>
                    </VCardText>
                </Vcol>
                <VCol cols="6">
                    <VCardText class="text">
                        <h6 class="text-h4 mt-3 fw-bold px-3">Details</h6>

                        <!-- 👉 User Details list -->
                        <VList class="card-list mt-5">
                            <VListItem class="py-3">
                                <VListItemTitle class="text-sm">
                                    <span class="font-weight-medium px-3"
                                        >Username:</span
                                    >
                                    <span class="text-body-2">
                                        {{ user?.user_detail?.name }}
                                    </span>
                                </VListItemTitle>
                            </VListItem>

                            <VListItem class="pt-3">
                                <VListItemTitle class="text-sm">
                                    <span class="font-weight-medium px-3">
                                        Email:
                                    </span>
                                    <span class="text-body-2">{{
                                        user?.user_detail?.email
                                    }}</span>
                                </VListItemTitle>
                            </VListItem>

                            <VListItem>
                                <VListItemTitle class="text-sm">
                                    <span class="font-weight-medium px-3">
                                        Status:
                                    </span>
                                    <VChip
                                        label
                                        size="small"
                                        class="text-capitalize"
                                    >
                                        active
                                    </VChip>
                                </VListItemTitle>
                            </VListItem>

                            <VListItem>
                                <VListItemTitle class="text-sm">
                                    <span class="font-weight-medium px-3"
                                        >Role:
                                    </span>
                                    <span class="text-capitalize text-body-2">{{
                                        user?.user_role?.name
                                    }}</span>
                                </VListItemTitle>
                            </VListItem>
                            <VListItem>
                                <VListItemTitle class="text-sm">
                                    <span class="font-weight-medium px-3">
                                        Contact:
                                    </span>
                                    <span class="text-body-2">{{
                                        user?.user_detail?.contact_number
                                    }}</span>
                                </VListItemTitle>
                            </VListItem>
                        </VList>
                    </VCardText>
                </VCol>
            </VRow>
        </VCard>
    </AdminLayout>

    <!-- 👉 Edit user info dialog -->
    <UserInfoEditDialog
        v-model:isDialogVisible="isUserInfoEditDialogVisible"
        :user-data="user"
        :form="form"
        @submit="hanleSubmit"
    />
</template>

<style>
.card-list {
    --v-card-list-gap: 0.8rem;
}

.current-plan {
    border: 2px solid rgb(var(--v-theme-primary));
}

.text-capitalize {
    text-transform: capitalize !important;
}
</style>
