<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">
                <h3><strong>VENDOR</strong> INVENTORY</h3>
            </div>

            <div class="col-auto ms-auto text-end mt-n1">
                <Link :href="'/vendors/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create
                    Vendor
                </Link>
            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">

                        <table id="vendor" class="table table-striped" style="width:100%">

                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>NAME</th>
                                <th>DESCRIPTION</th>
                                <th>CREATED DATE</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(Vendor,id) in Vendors" :key="id">
                                <td>{{id+1}}</td>
                                <td>{{Vendor.vendor_name.toUpperCase()}}</td>
                                <td>{{Vendor.description}}</td>
                                <td>{{Vendor.created_at}}</td>

                                <td>
                                    <Link type="button" :href="'/vendors/edit/' + Vendor.vendor_id"
                                          class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
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

    </div></template>

<script>
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        name: "Index",
        layout: Layout,
        props:{
            Vendors:Array
        },
        components: {
            Link
        },
        mounted() {
            $("#vendor").DataTable({
                responsive  : true,
                "paging"    : true,
            });
        }
    }
</script>

<style scoped>

</style>
