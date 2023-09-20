<template>
    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>TID</strong> INVENTORY</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'tid/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New TID</Link>
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
                                <th>TID</th>
                                <th>DEVICE ID</th>
                                <th>BOX SERIAL NO</th>
                                <th>EMV TYPE</th>
                                <th>ACQUIRER</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(Tid,tid) in Tids" :key="tid">
                                <td>{{tid+1}}</td>
                                <td>{{Tid.emv_tid}}</td>
                                <td>{{Tid.emv_box_serial_no}}</td>
                                <td>{{Tid.emv_serial_no}}</td>
                                <td>{{Tid.emv_reader_name}}</td>
                                <td>{{Tid.acq_name}}</td>
                                <td>
                                    <Link type="button"  :href="'/tid/edit/' + Tid.tid_inv_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
        props: {
            Tids: Array,
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
