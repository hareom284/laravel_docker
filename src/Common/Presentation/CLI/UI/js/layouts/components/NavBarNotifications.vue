<script setup>
import Notifications from "@core/components/Notifications.vue";

// Images
import avatar3 from "@images/avatars/avatar-3.png";
import avatar4 from "@images/avatars/avatar-4.png";
import avatar5 from "@images/avatars/avatar-5.png";
import paypal from "@images/svg/paypal.svg";
// import { computed, ref, onMounted, defineProps } from "vue";
// import { Link, usePage } from "@inertiajs/vue3";
// import { router } from "@inertiajs/vue3";
// import { Inertia } from "@inertiajs/inertia";
// import { useInfiniteScroll } from "@vueuse/core";
// import axios from "axios";

// let notifications = computed(() => usePage().props.notifications?.data);
// let unread_notifications_count = computed(
//   () => usePage().props.unreadNotificationsCount
// );
// let allNotifications = notifications.value;
// const items = ref([1]);
// let current_page = ref(usePage().props.notifications?.current_page);
// let last_page = ref(usePage().props.notifications?.last_page);
// let isLoading = ref(false);

// const scroll_el = ref("");
// useInfiniteScroll(
//   scroll_el,
//   () => {
//     current_page.value = current_page.value + 1;
//     if (current_page.value > last_page.value) {
//       return;
//     }
//     isLoading.value = true;
//     axios
//       .get(
//         route("notifications", {
//           page: current_page.value,
//         })
//       )
//       .then((resp) => {
//         last_page.value = resp.data.notifications.last_page;
//         allNotifications = [
//           ...allNotifications,
//           ...resp.data.notifications.data,
//         ];
//         isLoading.value = false;
//       });
//   },
//   { distance: 10 }
// );
// const notifications = ref([
//   {
//     id: 1,
//     img: avatar4,
//     title: 'Congratulation Flora! 🎉',
//     subtitle: 'Won the monthly best seller badge',
//     time: 'Today',
//     isRead: false,
//   },
//   {
//     id: 2,
//     text: 'Tom Holland',
//     title: 'New user registered.',
//     subtitle: '5 hours ago',
//     time: 'Yesterday',
//     isRead: true,
//   },
//   {
//     id: 3,
//     img: avatar5,
//     title: 'New message received 👋🏻',
//     subtitle: 'You have 10 unread messages',
//     time: '11 Aug',
//     isRead: false,
//   },
//   {
//     id: 4,
//     img: paypal,
//     title: 'Paypal',
//     subtitle: 'Received Payment',
//     time: '25 May',
//     isRead: true,
//     color: 'error',
//   },
//   {
//     id: 5,
//     img: avatar3,
//     title: 'Received Order 📦',
//     subtitle: 'New order received from john',
//     time: '19 Mar',
//     isRead: false,
//   },
// ])

// const removeNotification = (notificationId) => {
//   // notifications.value.forEach((item, index) => {
//   //   if (notificationId === item.id) notifications.value.splice(index, 1);
//   // });
//   router.post(route("markAsRead", { id: noti_id }), {
//     onSuccess: () => {
//       console.log("deleted");
//     },
//   });
// };
// const removeAllNotification = () => {
//   router.post(route("markAsReadAll"), {
//     onSuccess: () => {
//       console.log("deleted all");
//     },
//   });
// };

// const markRead = (notificationId) => {
//   notifications.value.forEach((item) => {
//     notificationId.forEach((id) => {
//       if (id === item.id) item.isRead = true;
//     });
//   });
// };

// const markUnRead = (notificationId) => {
//   notifications.value.forEach((item) => {
//     notificationId.forEach((id) => {
//       if (id === item.id) item.isRead = false;
//     });
//   });
// };
</script>

<template>
  <Notifications />
</template>
