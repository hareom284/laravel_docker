<template>
    <div class="h-screen messagelayout">
        <div class="layer">
            <div class="top">
                <div>
                    <div>
                        <h4 style="font-size: 16px; font-weight: bold;">TheWoodenPlatform</h4>
                        <span style="font-size: 14px;">Rose Wells</span>
                    </div>
                </div>
                <!-- <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="arrow">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>
                </div> -->
            </div>
            <div class="bodytitle">
                <div>
                    <!-- <span style="font-weight: 600;color: rgb(26, 26, 26);">Message</span> -->
                    <span style="font-weight: 600; color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity)) !important">Message</span>
                    <span
                        style="padding: 0px 5px;background-color: rgba(0, 128, 0, 0.123); margin-left: 5px; border-radius: 5px;">5</span>
                </div>
                <div>
                    <a href="#">View all</a>
                </div>
            </div>
            <div class="bodycontent">
                <div class="content">
                    <div class="contentcard">
                        <v-img :width="50" aspect-ratio="1/1" cover
                            src="https://cdn.vuetifyjs.com/images/parallax/material.jpg"></v-img>
                        <div>
                            <h5 style="font-size: 13px;">Teck Hiong</h5>
                            <span style="font-size: 12px;">Moreover the striking brillint and vivid coloes</span>
                        </div>
                        <span class="contenttime">10m</span>
                    </div>
                    <div class="contentcard">
                        <v-img :width="50" aspect-ratio="1/1" cover
                            src="https://cdn.vuetifyjs.com/images/parallax/material.jpg"></v-img>
                        <div>
                            <h5 style="font-size: 13px;">Teck Hiong</h5>
                            <span style="font-size: 12px;">Moreover the striking brillint and vivid coloes</span>
                        </div>
                        <span class="contenttime">10m</span>
                    </div>
                </div>
            </div>

            <div class="messagefooter">
                <div class="footercontent">
                    <div>
                        <h5 style="color: black;">Progress History</h5>
                        <span style="font-size: 13px;">Week to week performace</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="footericon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.38a48.474 48.474 0 00-6-.37c-2.032 0-4.034.125-6 .37m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.17c0 .62-.504 1.124-1.125 1.124H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265zm-3 0a.375.375 0 11-.53 0L9 2.845l.265.265zm6 0a.375.375 0 11-.53 0L15 2.845l.265.265z" />
                        </svg>
                    </div>
                </div>
                <img src="https://cdn.pixabay.com/photo/2018/03/27/12/16/analytics-3265840_1280.jpg" alt=""
                    style="width: 285px;border-radius: 10px;padding-top: 5px;">
            </div>
        </div>
    </div>
</template>

<style scoped>
.messagelayout {
    position: fixed;
    background-color: rgb(var(--v-theme-surface)) !important;
    z-index: 907;
    width: 350px;
    margin-left: 4%;
    box-shadow: 3px 3px 3px 2px rgba(0, 0, 0, 0.103);
}

.layer {
    padding: 0px;
}

.top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0px 3px 0px 0px rgba(0, 0, 0, 0.103);
    padding: 17px;
}

.arrow {
    width: 25px;
    height: 25px;
    color: black;
    background-color: rgb(219, 217, 217);
    padding: 5px;
    border-radius: 5px;
}

.bodytitle {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    font-size: 14px;
}

.content {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 10px;
}

.contentcard {
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: rgba(128, 128, 128, 0.123);
    padding: 10px;
    border-radius: 5px;
    position: relative;
    cursor: pointer;
}

.contentcard:hover {
    background-color: rgba(128, 128, 128, 0.226);
}

.contenttime {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 13px;
    padding: 5px;
    font-weight: 500;
}

.messagefooter {
    position: absolute;
    width: 94%;
    background-color: rgba(128, 128, 128, 0.123);
    bottom: 0;
    margin-left: 4%;
    margin-bottom: 2%;
    padding: 20px;
    border-radius: 10px;
}

.footercontent {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footericon {
    width: 30px;
    height: 40px;
    padding: 3px 3px;
    background-color: rgba(128, 128, 128, 0.233);
    border-radius: 5px;
}

</style>