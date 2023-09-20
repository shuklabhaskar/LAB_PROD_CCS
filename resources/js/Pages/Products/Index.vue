<template>

    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">
                <h3><strong>PRODUCTS</strong> INVENTORY</h3>
            </div>

            <div class="col-auto ms-auto text-end mt-n1">
                <Link :href="'/products/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create
                    Product Type
                </Link>
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
                                <th>NAME</th>
                                <th>DESCRIPTION</th>
                                <th>CREATED DATE</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="Product in Products">
                                <td>{{Product.product_type_id}}</td>
                                <td>{{Product.product_name.toUpperCase()}}</td>
                                <td>{{Product.description}}</td>
                                <td>{{Product.created_date}}</td>

                                <td>
                                    <Link type="button" :href="'/products/edit/' + Product.product_type_id"
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

    </div>

</template>

<script>
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";
    export default {
        props: {
            Products: Array,
        },
        name: "Index",
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

<style scoped>

</style>
