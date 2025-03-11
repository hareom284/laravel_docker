<script setup>
import Create from "./Create.vue";
import AdminLayout from "@Layouts/Dashboard/AdminLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { router } from "@inertiajs/core";
import { toastAlert } from "@Composables/useToastAlert";
import { computed, defineProps } from "vue";
import deleteItem from "@Composables/useDeleteItem.js";
import {
  serverParams,
  updateParams,
  onColumnFilter,
  searchItems,
  truncatedText,
  onPageChange,
  onPerPageChange,
  serverPage,
  serverPerPage,
} from "@Composables/useServerSideDatable.js";

//## start variable section
let props = defineProps(["permissions", "flash", "auth"]);
let permissions = computed(() => usePage().props.auth.data.permissions);

let currentPermission = ref();
const isEditPermissionDrawerVisible = ref(false);
serverPage.value = ref(props.permissions.meta.current_page ?? 1);
serverPerPage.value = ref(10);
let serverError = ref({
  name: "",
});

//## end permission and save in database

//## start delete permission and delete in database
const deletePermission = (id) => {
  deleteItem(id, "permissions");
};
//## end delete permission and delete in database

//start datatable section
let columns = [
  {
    label: "PERMISSION NAME",
    field: "name",
    sortable: false,
  },
  {
    label: "DESCRIPTION",
    field: "description",
    sortable: false,
  },
  {
    label: "GUARD NAME",
    field: "guard_name",
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
  perPage: props.permissions.meta.per_page,
  setCurrentPage: props.permissions.meta.current_page,
  perPageDropdown: [10, 20, 50, 100],
  dropdownAllowAll: false,
});

//## watch per page change in datatable
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
          <!-- 👉 Search  -->
          <VTextField
            @keyup.enter="searchItems"
            v-model="serverParams.search"
            placeholder="Search Permission"
            density="compact"
          />
          <VSpacer />

          <div class="app-user-search-filter d-flex align-center justify-end">
            <!-- 👉 Add Permission button -->
            <Create v-if="permissions.includes('create_permission')" />
          </div>
        </VCardText>

        <VDivider />

        <vue-good-table
          class="permission-data-table"
          mode="remote"
          @column-filter="onColumnFilter"
          :totalRows="props.permissions.meta.total"
          styleClass="vgt-table "
          :pagination-options="options"
          :rows="props.permissions.data"
          :columns="columns"
        >
          <template #table-row="props">
            <div v-if="props.column.field == 'name'" class="flex flex-wrap">
              <span class="">{{ props.row.name }}</span>
            </div>
            <div
              v-if="props.column.field == 'description'"
              class="flex flex-wrap"
            >
              <span>{{ truncatedText(props.row.description) }}</span>
            </div>
            <div v-if="props.column.field == 'action'">
              <div class="d-flex">
                <VBtn
                  variant="text"
                  density="compact"
                  icon="mdi-trash"
                  class="ml-2"
                  color="secondary"
                  @click="deletePermission(props.row.id)"
                >
                </VBtn>
              </div>
            </div>
          </template>
          <template #pagination-bottom>
            <VRow class="pa-4">
              <VCol cols="12" class="d-flex justify-space-between">
                <span
                  >Showing {{ props.permissions.meta.from }} to
                  {{ props.permissions.meta.to }} of
                  {{ props.permissions.meta.total }}
                  entries</span
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
                      :length="props.permissions.meta.last_page"
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
//## style for darkmode
.permission-data-table table.vgt-table {
  background-color: rgb(var(--v-theme-surface));
  border-color: rgb(var(--v-theme-surface));
}
.permission-data-table table.vgt-table td {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.permission-data-table table.vgt-table thead th {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.app-user-search-filter {
  inline-size: 24.0625rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
</style>
