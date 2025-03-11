/***
 *   @globale composable function to delet item int the database
 *
 */
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { computed} from "vue";
import { toastAlert } from "@Composables/useToastAlert";


let flash = computed(() => usePage().props.flash);

const deleteItem = (id,route_name) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`${route_name}/${id}`, {
                onSuccess: () => {
                    toastAlert({
                        title: flash?.value.successMessage,
                    });
                },
            });
        }
    });
}

export default deleteItem;

