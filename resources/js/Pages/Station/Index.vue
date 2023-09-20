<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>STATION</strong> INVENTORY</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'stations/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New Station</Link>
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
                                <th>STATION NAME</th>
                                <th>REGIONAL NAME</th>
                                <th>LINE</th>
                                <th>COMPANY</th>
                                <th>DESCRIPTION</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(Station,stn_id) in Stations" :key="stn_id">
                                <td>{{stn_id+1}}</td>
                                <td>{{Station.stn_name}}</td>
                                <td>{{Station.stn_regional_lang}}</td>
                                <td>{{Station.line_name}}</td>
                                <td>{{Station.company_name}}</td>
                                <td>{{Station.description}}</td>
                                <td v-if="Station.status === true ">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>
                                    <Link type="button" :href="'/stations/edit/' + Station.stn_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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

    props: {
        Stations: Array,
    },
    name: "index",
    layout: Layout,

    components: {
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
