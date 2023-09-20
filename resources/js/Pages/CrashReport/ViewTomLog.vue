<template>
    <div class="card">

        <div class="card-body">
            <p>{{tom_log}}</p>
        </div>

    </div>

    <div class="row mb-2 mb-xl-3">

        <div class="col-auto d-none d-sm-block">
            <Link :href="'/crashReport'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
        </div>
    </div>
</template>

<script>
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        name: "ViewTomLog",
        layout:Layout,
        components:{
            Link
        },
        props: {
            tom_log:Object
        }
    }
</script>

<style scoped>

</style>
