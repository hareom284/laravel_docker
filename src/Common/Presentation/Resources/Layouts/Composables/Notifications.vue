<script setup>
import { computed, ref, onMounted, defineProps, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { useInfiniteScroll } from "@vueuse/core";
import axios from "axios";
let notifications = ref(() => usePage().props.notifications?.data);
let unread_notifications_count = computed(
    () => usePage().props.unreadNotificationsCount
);
let allNotifications = notifications.value;
const selectedCountry = ref();
let isLoading = ref(false);
const items = ref([1]);
let current_page = ref(usePage().props.notifications?.current_page);
let last_page = ref(usePage().props.notifications?.last_page);

const scroll_el = ref("");

const position = ref("center");

const visible = ref(false);

const openPosition = (pos) => {
    position.value = pos;
    visible.value = true;
};
useInfiniteScroll(
    scroll_el,
    () => {
        current_page.value = current_page.value + 1;
        if (current_page.value > last_page.value) {
            return;
        }
        isLoading.value = true;
        axios
            .get(
                route("notifications", {
                    page: current_page.value,
                })
            )
            .then((resp) => {
                last_page.value = resp.data.notifications.last_page;
                allNotifications = [
                    ...allNotifications,
                    ...resp.data.notifications.data,
                ];
                isLoading.value = false;
            });
    },
    { distance: 10 }
);
const markAsRead = (noti_id) => {
    router.post(route("markAsRead", { id: noti_id }), {
        onSuccess: () => {
            console.log("deleted");
        },
    });
};
const markAsReadAll = (noti_id) => {
    router.post(route("markAsReadAll"), {
        onSuccess: () => {
            console.log("deleted all");
        },
    });
};
</script>
<template>
    <div>
        <Button
            label=""
            style="padding: 7px"
            rounded
            class="bg-primary"
            @click="openPosition('topright')"
        >
            <i style="font-size: 1.3rem" class="pi pi-bell"></i>
        </Button>
        <Badge
            class="mb-4"
            :value="unread_notifications_count"
            severity="danger"
        ></Badge>
        <Dialog
            v-model:visible="visible"
            class="w-full md:w-1/2"
            :position="position"
            :modal="true"
            :draggable="false"
        >
            <template #header>
                <div class="flex items-center">
                    <span id="pv_id_3_header" class="p-dialog-title"
                        >Notifications</span
                    >
                    <Badge
                        class="ml-2"
                        :value="unread_notifications_count"
                        severity="danger"
                    ></Badge>
                </div>
            </template>
            <div
                v-if="notifications?.length > 0"
                class="flex flex-col justify-between"
                style="max-height: 400px"
            >
                <div
                    ref="scroll_el"
                    class="flex flex-col overflow-y-scroll bg-gray-500/5 rounded"
                >
                    <!-- <div
                    v-for="item in data"
                    :key="item"
                    class="h-30 bg-gray-500/5 rounded p-3"
                  >
                    {{ item }}
                  </div> -->
                    <Message
                        v-for="notifcation in allNotifications"
                        :key="notifcation.id"
                        severity="info"
                        icon="pi pi-bell"
                        @close.prevent="markAsRead(notifcation.id)"
                        >{{ notifcation.data.message }}</Message
                    >
                    <div>
                        <Skeleton
                            height="4.5rem"
                            class="my-4"
                            v-if="isLoading"
                        ></Skeleton>
                    </div>
                </div>

                <div class="d-flex text-center w-100 mt-5">
                    <Button
                        size="small"
                        class="w-1/3"
                        label="Clear All"
                        outlined
                        @click="markAsReadAll"
                    />
                </div>
            </div>
            <div v-else>
                <p>Empty Notifications...</p>
            </div>
        </Dialog>
    </div>
</template>
