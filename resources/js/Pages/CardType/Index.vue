<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CARD</strong> TYPE</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'cardType/create'" class="btn btn-outline-primary">
                    <font-awesome-icon icon="fa-solid fa-plus"/>
                    Create New Card Type
                </Link>
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
                                <th>CARD NAME</th>
                                <th>STATUS</th>
                                <th>DESCRIPTION</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(cardType,card_id) in cardTypes" :key="card_id">
                                <td>{{card_id+1}}</td>
                                <td>{{cardType.card_name}}</td>
                                <td v-if="cardType.status === true ">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>{{cardType.description}}</td>
                                <td>
                                    <Link type="button" :href="'/cardType/edit/' + cardType.card_type_id"
                                          class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip"
                                          data-bs-placement="top" title="Edit">
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

    /*IMPORTS*/
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {

        props: {
            cardTypes: Array,
        },
        name: "index",
        layout: Layout,

        components: {
            Link
        },

        mounted() {

            $("#example").DataTable({
                responsive: true,
                "paging": true,
            });

        }


    }

</script>
