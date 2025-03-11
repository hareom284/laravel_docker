<script setup>
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { avatarText } from "@core/utils/formatters";
import { computed, ref, onMounted, defineProps } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { useInfiniteScroll } from "@vueuse/core";
import axios from "axios";

// let notifications = ref([]);
// let unread_notifications_count = computed(
//   () => usePage().props.unreadNotificationsCount
// );
let allNotifications = ref([]);
let reactiveNoti = computed(() => usePage().props.notifications?.data);
let watchNoti = watch(reactiveNoti, (value) => {
  getNotifications();
});
// allNotifications.value = notifications.value;
const items = ref([1]);
let current_page = ref(usePage().props.notifications?.current_page);
let last_page = ref(usePage().props.notifications?.last_page);
let isLoading = ref(false);
const scroll_el = ref("");
const form = useForm({});
const getNotifications = () => {
  isLoading.value = true;
  axios
    .get(
      route("notifications", {
        page: 1,
      })
    )
    .then((resp) => {
      last_page.value = resp.data.notifications.last_page;
      allNotifications.value = resp.data.notifications.data;
      isLoading.value = false;
    });
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
        allNotifications.value = [
          ...allNotifications.value,
          ...resp.data.notifications.data,
        ];
        isLoading.value = false;
      });
  },
  { distance: 10 }
);
const removeNotification = (notificationId) => {
  form.post(route("markAsRead", { id: notificationId }), {
    onSuccess: () => {
      allNotifications.value = allNotifications.value.filter(
        (noti) => noti.id != notificationId
      );
    },
  });
};
const removeAllNotification = () => {
  form.post(route("markAsReadAll"));
  allNotifications.value = [];
};
// const isAllMarkRead = computed(() => {
//   return props.notifications.some((item) => item.isRead === true);
// });

// const markAllReadOrUnread = () => {
//   const allNotificationsIds = props.notifications.map((item) => item.id);
//   if (isAllMarkRead.value) emit("unread", allNotificationsIds);
//   else emit("read", allNotificationsIds);
// };
onMounted(() => {
  getNotifications();
});
</script>

<template>
  <IconBtn>
    <VBadge
      dot
      :model-value="!!allNotifications.length"
      color="error"
      bordered
      offset-x="1"
      offset-y="1"
    >
      <VIcon icon="mdi-bell-outline" />
    </VBadge>

    <VMenu
      activator="parent"
      width="380px"
      offset="14px"
      :close-on-content-click="false"
    >
      <VCard class="d-flex flex-column">
        <!-- 👉 Header -->
        <VCardItem class="notification-section">
          <VCardTitle class="text-lg"> Notifications </VCardTitle>

          <template #append>
            <IconBtn v-show="allNotifications.length">
              <VIcon :icon="'mdi-email-outline'" />

              <!-- <VTooltip activator="parent" location="start">
                  {{
                    isAllMarkRead ? "Mark all as read" : "Mark all as unread"
                  }}
                </VTooltip> -->
            </IconBtn>
          </template>
        </VCardItem>

        <VDivider />

        <!-- 👉 Notifications list -->
        <PerfectScrollbar
          :options="{ wheelPropagation: false }"
          ref="scroll_el"
        >
          <VList class="py-0">
            <template
              v-for="notification in allNotifications"
              :key="notification.id"
            >
              <VListItem
                link
                lines="one"
                min-height="66px"
                class="list-item-hover-class"
              >
                <!-- Slot: Prepend -->
                <!-- Handles Avatar: Image, Icon, Text -->
                <template #prepend>
                  <VListItemAction start>
                    <VAvatar size="40" variant="tonal"> </VAvatar>
                  </VListItemAction>
                </template>

                <VListItemTitle>{{ notification.data.message }}</VListItemTitle>
                <!-- <VListItemSubtitle>{{
                    notification.subtitle
                  }}</VListItemSubtitle> -->
                <!-- <span class="text-xs text-disabled">{{
                    notification.time
                  }}</span> -->

                <!-- Slot: Append -->
                <template #append>
                  <div class="d-flex flex-column align-center gap-4">
                    <!-- <VBadge
                        dot
                        :color="notification.isRead ? 'primary' : '#a8aaae'"
                        :class="`${
                          !notification.isRead ? 'visible-in-hover' : ''
                        } ms-1`"
                        @click.stop="
                          $emit(notification.isRead ? 'unread' : 'read', [
                            notification.id,
                          ])
                        "
                      /> -->

                    <div style="width: 28px; height: 28px">
                      <IconBtn
                        size="x-small"
                        class="visible-in-hover"
                        @click="removeNotification(notification.id)"
                      >
                        <VIcon size="20" icon="mdi-close" />
                      </IconBtn>
                    </div>
                  </div>
                </template>
              </VListItem>
              <VDivider />
            </template>

            <VListItem
              v-show="!allNotifications.length"
              class="text-center text-medium-emphasis"
            >
              <VListItemTitle>No Notification Found!</VListItemTitle>
            </VListItem>
          </VList>
        </PerfectScrollbar>

        <!-- 👉 Footer -->
        <VCardActions
          v-show="allNotifications.length"
          class="notification-footer"
        >
          <VBtn block @click="removeAllNotification">
            CLEAR ALL NOTIFICATIONS
          </VBtn>
        </VCardActions>
      </VCard>
    </VMenu>
  </IconBtn>
</template>

<style lang="scss">
.notification-section {
  padding: 14px !important;
}

.notification-footer {
  padding: 6px !important;
}

.list-item-hover-class {
  .visible-in-hover {
    display: none;
  }

  &:hover {
    .visible-in-hover {
      display: block;
    }
  }
}
</style>
