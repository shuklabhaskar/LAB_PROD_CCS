<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>FARE</strong> INVENTORY</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <Link :href="'/fares/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New Fare</Link>
            </div>

        </div>

        <!--TABLE DATA AND BUTTON-->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">

                        <!--TABLE DATA-->
                        <table id="example" class="table table-striped w-100">
                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>NAME</th>
                                <th>DESCRIPTION</th>
                                <th>VERSION</th>
                                <th>STATUS</th>
                                <th>START DATE</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="Fare in Fares">
                                <td>{{Fare.fare_table_id}}</td>
                                <td>{{Fare.fare_name.toUpperCase()}}</td>
                                <td>{{Fare.description}}</td>
                                <td>{{Fare.fare_version}}</td>
                                <td v-if="Fare.status === true">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>{{Fare.start_date}}</td>
                                <td>
                                    <Link type="button"  :href="'/fares/edit/' + Fare.fare_table_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
/*IMPORTS*/
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";


export default {
    props:{
        Fares:Array,
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
        });
    }



}
</script>
