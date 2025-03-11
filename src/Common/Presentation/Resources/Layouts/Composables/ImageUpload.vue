<script setup>
import { ref } from "vue";
let file = ref(null);
// for check image exisit  or not
let hasImage = ref(false);
function SelectImage() {
    //select image from hidden field
    file.value.click();
}

let emit = defineEmits("update:modelValue");
let props = defineProps({
    old_img: {
        type: String,
        default:
            "https://getstamped.co.uk/wp-content/uploads/WebsiteAssets/Placeholder.jpg",
    },
});
function fileData(event) {
    let image = event.target.files[0];

    var parent = event.target.closest(".dropzone");

    if (parent) {
        var imgArea = parent.querySelector(".img-area");
    }

    // file is less than
    if (image.size < 5000000) {
        let reader = new FileReader();
        reader.onload = () => {
            let allImg = imgArea.querySelectorAll("img");
            allImg.forEach((item) => item.remove());
            let imgUrl = reader.result;
            let img = document.createElement("img");
            img.src = imgUrl;
            // form.image = event.target.files[0];
            emit("update:modelValue", event.target.files[0]);
            hasImage.value = true;
            imgArea.appendChild(img);
            imgArea.classList.add("active");
            imgArea.dataset.img = image.name;
        };
        reader.readAsDataURL(image);
    } else {
        alert("Image size more than 5MB");
    }
}

// handle Remove Image
function handleRemoveImage() {
    emit("update:modelValue", null);
    let imgArea = document.querySelector(".img-area");
    let allImg = imgArea.querySelectorAll("img");
    allImg.forEach((item) => item.remove());
    imgArea.dataset.img = "";
    let img = document.createElement("img");
    img.src =
        "https://getstamped.co.uk/wp-content/uploads/WebsiteAssets/Placeholder.jpg";
    imgArea.appendChild(img);
    hasImage.value = false;
}
</script>

<template>
    <div class="flex flex-wrap justify-center">
        <div class="w-6/12 sm:w-4/12 pa-0 pb-4 dropzone">
            <div @click="SelectImage">
                <input
                    type="file"
                    ref="file"
                    :value="modelValue"
                    @change="fileData"
                    accept="image/*"
                    hidden
                />
                <div class="img-area" data-img="">
                    <img
                        :src="
                            old_img
                                ? old_img
                                : 'https://getstamped.co.uk/wp-content/uploads/WebsiteAssets/Placeholder.jpg'
                        "
                        class=""
                    />
                    <h3 :class="hasImage ? '' : 'd-none'">Upload Image</h3>
                    <p :class="hasImage ? '' : 'd-none'">
                        Image size must be less than
                        <span>2MB</span>
                    </p>
                </div>
            </div>
            <div class="d-flex justify-end w-100">
                <VBtn
                    size="small"
                    variant="outlined"
                    color="error"
                    v-if="hasImage"
                    @click="handleRemoveImage"
                >
                    <i class="pi pi-delete-left pt-1"></i>
                    Remove Image
                </VBtn>
            </div>
        </div>
    </div>
</template>

<style>
.dropzone {
    max-width: 400px;
    width: 100%;
    background: #fff;
    padding: 30px;
    border-radius: 30px;
}
.img-area {
    position: relative;
    width: 100%;
    height: 270px;
    background: var(--grey);
    margin-bottom: 30px;
    border-radius: 15px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.img-area .icon {
    font-size: 100px;
}
.img-area h3 {
    font-size: 20px;
    font-weight: 500;
    margin-bottom: 6px;
}
.img-area p {
    color: #999;
}
.img-area p span {
    font-weight: 600;
}
.img-area img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    z-index: 100;
}
.img-area::before {
    content: attr(data-img);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    font-weight: 500;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 200;
}
.img-area.active:hover::before {
    opacity: 1;
}
.select-image {
    display: block;
    width: 100%;
    padding: 6px 0;
    border-radius: 15px;
    background: rgb(197, 79, 79) !important;
    color: #fff;
    font-weight: 500;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.select-image:hover {
    background: rgb(205, 11, 11) !important;
}
.img-area img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
</style>
