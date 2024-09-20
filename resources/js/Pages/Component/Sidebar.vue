<template>

    <nav id="sidebar" class="sidebar js-sidebar">

        <div class="sidebar-content js-simplebar">

            <!--ATEK LOGO-->
            <div class="d-grid text-start">
                <img :src="logo" class="mt-3" style="width: 145px;margin-left: 30px" alt="logo">
            </div>

            <Link :href="'/dashboard'" class="sidebar-brand text-start">
                <span class="align-middle ">Mumbai Metro One</span>
            </Link>

            <ul class="sidebar-nav">

                <li class="sidebar-header">
                    DASHBOARD
                </li>

                <li class="sidebar-item ">
                    <Link :href="'/dashboard'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-sliders"/>
                        <span class="align-middle">DASHBOARD</span>
                    </Link>

                </li>


                <li class="sidebar-header">
                    TID INVENTORY
                </li>

                <!--FOR TID INVENTORY-->
                <li class="sidebar-item">
                    <Link :href="'/tids'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-barcode"/>
                        <span class="align-middle">TIDS</span>
                    </Link>
                </li>

                <li class="sidebar-header">
                    ACQUIRER PARAMETERS
                </li>

                <!--FOR TID INVENTORY-->
                <li class="sidebar-item">
                    <Link :href="'/acq'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-money-check"/>
                        <span class="align-middle">ACQ PARAM</span>
                    </Link>
                </li>

                <!--FOR CARD TYPE-->
                <li class="sidebar-header">
                    CARD TYPE
                </li>

                <!--FOR CARD TYPE-->
                <li class="sidebar-item">
                    <Link :href="'/cardType'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-credit-card"/>
                        <span class="align-middle">CARD TYPE</span>
                    </Link>
                </li>

                <!--FOR CARD SN MAPPING-->
                <li class="sidebar-item">
                    <Link :href="'/card/sn/mapping'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-paperclip"/>
                        <span class="align-middle">CARD MAPPING</span>
                    </Link>
                </li>

                <!--FOR CARD BLACKLIST-->
                <li class="sidebar-item">
                    <Link :href="'/card/blacklist'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-ban"/>
                        <span class="align-middle">CARD BLACKLIST</span>
                    </Link>
                </li>


                <li class="sidebar-header">
                    INVENTORIES
                </li>

                <!--FOR STATION INVENTORY-->
                <li class="sidebar-item ">
                    <Link :href="'/stations'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-train"/>
                        <span class="align-middle">Stations</span>
                    </Link>
                </li>

                <!--FOR EQUIPMENT INVENTORY-->
                <li class="sidebar-item ">
                    <Link :href="'/equipments'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-toolbox"/>
                        <span class="align-middle">Equipments</span>
                    </Link>
                </li>

                <!--FOR FARES-->
                <li class="sidebar-item ">
                    <Link :href="'/fares'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-inr"/>
                        <span class="align-middle">Fares</span>
                    </Link>
                </li>

                <!--FOR PRODUCTS-->
                <li class="sidebar-item ">
                    <Link :href="'/products'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-qrcode"/>
                        <span class="align-middle">Products</span>
                    </Link>
                </li>

                <!--FOR PASS PARAMETERS-->
                <li class="sidebar-item ">
                    <Link :href="'/pass/parameters'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-pencil"/>
                        <span class="align-middle">Pass Parameters</span>
                    </Link>
                </li>

                <!--FOR PASSES-->
                <li class="sidebar-item ">
                    <Link :href="'/pass'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-ticket"/>
                        <span class="align-middle">Passes</span>
                    </Link>
                </li>

                <!--FOR VENDORS-->
                <li class="sidebar-header">
                    VENDORS MODULE
                </li>

                <li class="sidebar-item ">
                    <Link :href="'/vendors'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-user"/>
                        <span class="align-middle">Vendors</span>
                    </Link>
                </li>

                <!--FOR OPERATORS-->
                <li class="sidebar-header">
                   API & OPERATORS MODULE
                </li>

                <li class="sidebar-item ">
                    <Link :href="'/operators'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-handshake"/>
                        <span class="align-middle">Operators</span>
                    </Link>
                </li>

                <li class="sidebar-item ">
                    <Link :href="'/api/endPoint'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-code-compare"/>
                        <span class="align-middle">API</span>
                    </Link>
                </li>

                <li class="sidebar-item ">
                    <Link :href="'/operators/privilege'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-lock"/>
                        <span class="align-middle">Operators Privilege</span>
                    </Link>
                </li>

                <li class="sidebar-header">
                    USER MODULE
                </li>

                <!--FOR USERS-->
                <li class="sidebar-item ">
                    <Link :href="'/users'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-user"/>
                        <span class="align-middle">Users</span>
                    </Link>
                </li>

                <!--FOR USER ROLE-->
                <li class="sidebar-item ">
                    <Link :href="'/userRole'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-users-gear"/>
                        <span class="align-middle">User Role</span>
                    </Link>
                </li>

                <!--FOR FIRMWARE PRIVILEGE-->
                <li class="sidebar-item ">
                    <Link :href="'/userPrivilege'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-user-secret"/>
                        <span class="align-middle">User Privilege</span>
                    </Link>
                </li>

                <!--FOR FIRMWARE-->
                <li class="sidebar-header">
                    FIRMWARE
                </li>

                <!--FOR FIRMWARE UPLOAD-->
                <li class="sidebar-item">
                    <Link :href="'/firmware'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-upload"/>
                        <span class="align-middle">Firmware Upload</span>
                    </Link>
                </li>

                <!--FOR FIRMWARE PUBLISH-->
                <li class="sidebar-item">
                    <Link :href="'/firmwarePublish'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-signal"/>
                        <span class="align-middle">Firmware Publish</span>
                    </Link>
                </li>

                <li class="sidebar-header">
                    CONFIGURATIONS
                </li>

                <!--FOR GENERATING CONFIGURATION-->
                <li class="sidebar-item">
                    <Link :href="'/config'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-clipboard-check"/>
                        <span class="align-middle">Generate Config</span>
                    </Link>
                </li>

                <!--FOR PUBLISH CONFIGURATION-->
                <li class="sidebar-item">
                    <Link :href="'/publish'" class="sidebar-link">
                        <font-awesome-icon icon="fa-solid fa-paper-plane"/>
                        <span class="align-middle">Publish Config</span>
                    </Link>
                </li>

                <div class="sidebar-cta">
                    <div class="sidebar-cta-content">
                        <div class="d-grid">
                            <a class="btn btn-outline-primary">Version 3.9.1</a>
                        </div>
                    </div>
                </div>

            </ul>

        </div>

    </nav>

</template>

<script>

import {Link} from "@inertiajs/inertia-vue3";

export default {
        name: "Sidebar",
        components: {
            Link
        },
        data() {
            return {
                logo: '/Img/logo.png',
                logo2: '/Img/Atek.png',
                logo3: '/Img/ateklogo.png',
            }
        },
    }

</script>

<style scoped>

</style>
