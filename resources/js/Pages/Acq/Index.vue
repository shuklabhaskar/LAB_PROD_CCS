<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>ACQUIRER</strong> PARAMETERS</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'acq/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New Acquirer</Link>
            </div>

        </div>

        <!--CARD AND TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>OPERATOR ID</th>
                                <th>ACQUIRER ID</th>
                                <th>ACQUIRER NAME</th>
                                <th>ACQUIRER MERCHANT ID</th>
                                <th>CLIENT ID</th>
                                <th>LINE NAME</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(acqParam,acq_param_id) in  acqParams" :key="acq_param_id">
                            <td>{{acq_param_id+1}}</td>
                            <td>{{acqParam.operator_id}}</td>
                            <td>{{acqParam.acq_id}}</td>
                            <td>{{acqParam.acq_name}}</td>
                            <td>{{acqParam.acq_mid}}</td>
                            <td>{{acqParam.client_id}}</td>
                            <td>{{acqParam.line_name}}</td>
                                <td>
                                    <Link type="button"  :href="'/acq/edit/' + acqParam.acq_param_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <font-awesome-icon icon="fa-solid fa-edit" />
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
        props:{
            acqParams:Array
        },



        mounted() {
            $("#example").DataTable({
                responsive  : true,
                "paging"    : true,
            });
        }

    }

</script>

<style scoped>

</style>
