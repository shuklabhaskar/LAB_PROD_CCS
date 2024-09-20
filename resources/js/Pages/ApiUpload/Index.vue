<template>

    <div class="container-fluid p-0">

        <!--HEADING-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>API</strong> UPLOAD</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'/api/endPoint/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New API</Link>
            </div>

        </div>

        <!--CARD WITH FORM AND BUTTONS-->
        <!--API LIST TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="ApiList" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>PRODUCT ID</th>
                                <th>API TYPE</th>
                                <th>API NAME</th>
                                <th>API DESCRIPTION</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="(api,ms_api_route_id) in apis" :key="ms_api_route_id">

                                <td>{{ ms_api_route_id + 1 }}</td>
                                <td>{{ api.product_name.toUpperCase() }}</td>
                                <td v-if="api.api_request_type === 1">
                                    <span class="badge bg-success">GET</span>
                                </td>
                                <td v-else-if="api.api_request_type === 2">
                                    <span class="badge bg-warning">POST</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">DELETE</span>
                                </td>
                                <td>{{ api.api_name.toUpperCase() }}</td>
                                <td>{{ api.api_description }}</td>
                                <td>
                                    <Link type="button" :href="'/api/endPoint/edit/' + api.ms_api_route_id"
                                          class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
                                        <font-awesome-icon icon="fa-solid fa-edit"/>
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
    name: "Index",
    layout: Layout,

    components: {
        Link
    },
    props: {
        apis: Array,
    },

    mounted() {

        $("#ApiList").DataTable({
            responsive: true,
            "paging": true,
            scrollY:        500,
            deferRender:    true,
            scroller:       true,
            'columnDefs': [
                {
                    "targets": [0,5],
                    "className": "text-center",
                    "width": "4%",
                },
            ]
        });
    }
}
</script>

<style scoped>

</style>
