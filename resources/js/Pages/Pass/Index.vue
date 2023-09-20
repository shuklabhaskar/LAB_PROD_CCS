<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">
                <h3><strong>PASS</strong> INVENTORY</h3>
            </div>

            <div class="col-auto ms-auto text-end mt-n1">
                <Link :href="'/pass/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create  Pass</Link>
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
                                <th>PASS NAME</th>
                                <th>MEDIA TYPE</th>
                                <th>PASS ID</th>
                                <th>PRODUCT TYPE</th>
                                <th>DESCRIPTION</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="(Pass,id) in Passes" :key="id">
                                <td>{{id+1}}</td>
                                <td>{{Pass.pass_name.toUpperCase()}}</td>
                                <td>{{Pass.media_name.toUpperCase()}}</td>
                                <td>{{Pass.pass_id}}</td>
                                <td>{{Pass.product_name.toUpperCase()}}</td>
                                <td>{{Pass.description}}</td>
                                <td v-if="Pass.status === true">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>
                                    <Link type="button"  :href="'/pass/edit/' + Pass.pass_inv_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
    props:{
        Passes:Array,
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

<style scoped>

</style>
