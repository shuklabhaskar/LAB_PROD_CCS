<template>
    <nav class="navbar navbar-expand navbar-light navbar-bg">

        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>

        <a class="nav-icon pe-md-0 dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <img :src="logo3" style="width: 85px;margin-left: 5px"  alt="Atek" />
        </a>



        <div class="navbar-collapse collapse">
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <a class="nav-icon pe-md-0 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <font-awesome-icon icon="fa-solid fa-gear" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" ><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                        <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" ><i class="align-middle me-1" data-feather="settings"></i> Settings &
                            Privacy</a>
                        <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
                        <div class="dropdown-divider"></div>
                        <Link class="dropdown-item" :href="'/'"><font-awesome-icon class="me-1" icon="fa-solid fa-right-from-bracket" />Log out</Link>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        name: "Topbar",
        components:{
            Link
        },
        data() {
            return {
                logo3:'/Img/ateklogo.png'
            }
        },
    }
</script>

<style scoped>

</style>
