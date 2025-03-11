<script setup>
import Create from "./Create.vue";
import Edit from "./Edit.vue";
import AdminLayout from "@Layouts/Dashboard/AdminLayout.vue";
import {  usePage } from "@inertiajs/vue3";
import { computed, defineProps } from "vue";
import deleteItem from "@Composables/useDeleteItem.js";
import {
    serverParams,
    onColumnFilter,
    searchItems,
    onPageChange,
    onPerPageChange,
    serverPage,
    serverPerPage,
} from "@Composables/useServerSideDatable.js";
let props = defineProps(["users", "roles_name", "flash", "auth","organizations"]);
let flash = computed(() => usePage().props.flash);
let users = computed(() => usePage().props.auth.data.users);
let permissions = computed(() => usePage().props.auth.data.permissions);
let currentPermission = ref();
serverPage.value = ref(props.users.meta.current_page ?? 1);
serverPerPage.value = ref(10);
console.log(props.users,"hello testing")

const deleteUser = (id) => {
    deleteItem(id, "users");
};

let columns = [
    {
        label: "NAME",
        field: "name",
        sortable: false,
    },
    {
        label: "EMAIL",
        field: "email",
        sortable: false,
    },
    {
        label: "ROLES",
        field: "roles",
        sortable: false,
    },
    {
        label: "REGISTER AT",
        field: "created_at",
        sortable: false,
    },
    {
        label: "ACTION",
        field: "action",
        sortable: false,
    },
];
//## options for datatable
let options = ref({
    enabled: true,
    mode: "pages",
    perPage: props.users.meta.per_page,
    setCurrentPage: props.users.meta.current_page,
    perPageDropdown: [10, 20, 50, 100],
    dropdownAllowAll: false,
});
watch(serverPerPage, function (value) {
    onPerPageChange(value);
});
</script>


<template>
    <AdminLayout>
        <section>
            <VCard>
                <VCardText class="d-flex flex-wrap gap-4">
                    <!-- 👉 Export button -->
                    <VTextField
                        @keyup.enter="searchItems"
                        v-model="serverParams.search"
                        placeholder="Search Users"
                        density="compact"
                    />
                    <VSpacer />

                    <div
                        class="app-user-search-filter d-flex align-center justify-end"
                    >
                        <!-- 👉 Add User button -->
                        <Create
                            :organizations="organizations"
                            :roles="roles_name"
                            :flash="flash"
                            v-if="permissions.includes('create_user')"
                        />
                    </div>
                </VCardText>

                <VDivider />

                <vue-good-table
                    class="user-data-table"
                    mode="remote"
                    @column-filter="onColumnFilter"
                    :totalRows="props.users.meta.total"
                    styleClass="vgt-table "
                    :pagination-options="options"
                    :rows="props.users.data"
                    :columns="columns"
                >
                    <template #table-row="props">
                        <!-- <span>{{props.row}}</span> -->
                        <div
                            v-if="props.column.field == 'roles'"
                            class="flex flex-wrap"
                        >
                            <VChip
                                color="primary"
                                v-for="role in props?.row?.roles"
                                :key="role?.id"
                            >
                                {{ role?.name }}
                            </VChip>
                        </div>
                        <div
                            v-if="props.column.field == 'created_at'"
                            class="flex flex-wrap"
                        >
                            {{
                                moment(props.row.created_at).format(
                                    "DD-MM-YYYY h:mm A"
                                )
                            }}
                        </div>
                        <div v-if="props.column.field == 'action'">
                            <div class="d-flex">
                                <Edit
                                    :organizations="organizations"
                                    :user="props.row"
                                    :roles="roles_name"
                                    :flash="flash"
                                    v-if="permissions.includes('edit_user')"
                                />
                                <div v-if="permissions.includes('delete_user')">
                                    <VBtn
                                        density="compact"
                                        icon="mdi-trash"
                                        class="ml-2"
                                        color="secondary"
                                        variant="text"
                                        v-if="
                                            props.row.roles[0].name !==
                                            'BC Super Admin'
                                        "
                                        @click="deleteUser(props.row.id)"
                                    >
                                    </VBtn>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #pagination-bottom>
                        <VRow class="pa-4">
                            <VCol
                                cols="12"
                                class="d-flex justify-space-between"
                            >
                                <span
                                    >Showing {{ props.users.meta.from }} to
                                    {{ props.users.meta.to }} of
                                    {{ props.users.meta.total }} entries</span
                                >
                                <div>
                                    <div class="d-flex align-center">
                                        <span class="me-2">Show</span>
                                        <VSelect
                                            v-model="serverPerPage"
                                            density="compact"
                                            :items="options.perPageDropdown"
                                        ></VSelect>
                                        <VPagination
                                            v-model="serverPage"
                                            size="small"
                                            :total-visible="5"
                                            :length="props.users.meta.last_page"
                                            @next="onPageChange"
                                            @prev="onPageChange"
                                            @click="onPageChange"
                                        />
                                    </div>
                                </div>
                            </VCol>
                        </VRow>
                    </template>
                </vue-good-table>
                <VDivider />
            </VCard>
        </section>
    </AdminLayout>
</template>


<style lang="scss">
.app-user-search-filter {
    inline-size: 24.0625rem;
}

.text-capitalize {
    text-transform: capitalize;
}
.user-data-table table.vgt-table {
    background-color: rgb(var(--v-theme-surface));
    border-color: rgb(var(--v-theme-surface));
}
.user-data-table table.vgt-table td {
    color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.user-data-table table.vgt-table thead th {
    background: rgb(var(--v-theme-surface)) !important;
    color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.user-list-name:not(:hover) {
    color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
</style>
