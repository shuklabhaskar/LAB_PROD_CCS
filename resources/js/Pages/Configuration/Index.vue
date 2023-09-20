<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">
                <h3><strong>GENERATE</strong> CONFIGURATION</h3>
            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">

                        <table id="example" class="table table-striped" style="width:100%">

                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>CONFIG TYPE</th>
                                <th>CURRENT VERSION</th>
                                <th>FUTURE VERSION</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="(Config,id) in Configs" :key="id">

                                <td>{{id+1}}</td>
                                <td>{{Config.config_name}}</td>
                                <td>{{Config.config_version == null ? 0 : Config.config_version }}</td>
                                <td>{{ Config.config_version == null ? 1 : Config.config_version + 1 }}</td>
                                <td>
                                    <Link :href="'config/create/' + Config.config_id" class="btn btn-outline-primary">
                                        <font-awesome-icon icon="fa-solid fa-check-circle" />&nbsp;GENERATE
                                    </Link>
                                </td>

                            </tr>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Layout from "../Base/Layout";
import {Link} from "@inertiajs/inertia-vue3";

export default {
    props:{
        Configs:Array
    },
    name: "Index",
    layout: Layout,
    components:{
        Link
    },

    mounted() {
        $("#example").DataTable({
            responsive  : true,
            "paging"    : true,
            "ordering"  : false,
            columnDefs: [
                {
                    targets: 0,
                    "className": "text-center",
                },
                {
                    targets: 1,
                    "className": "text-start",
                },
                {
                    targets: 2,
                    "className": "text-center",
                },
                {
                    targets: 3,
                    "className": "text-center",
                },
                {
                    targets: 4,
                    "className": "text-center",
                }

            ]
        });
    }

}
</script>

<style scoped>

</style>
