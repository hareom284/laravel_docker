import Swal  from "sweetalert2";

let toastAlert = ({
    title,
    icon = "success",
    bgColor = "green",
    textColor = "white",
    iconColor = "white",
}) => {
    let Toast = Swal.mixin({
        toast: true,
        icon: icon,
        position: "top-right",
        showConfirmButton: false,
        timer: 1500,
        title: title,
        background: bgColor,
        color: textColor,
        iconColor: iconColor,
        timerProgressBar: true,
    });
    Toast.fire();
};
export { toastAlert };
