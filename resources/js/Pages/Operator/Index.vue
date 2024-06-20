<template>

    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">
                <h3><strong>OPERATOR</strong> INVENTORY</h3>
            </div>

            <div class="col-auto ms-auto text-end mt-n1">
                <Link :href="'/operator/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create
                    Operator
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
                                <th>OPERATOR NAME</th>
                                <th>USER NAME</th>
                                <th>CREATED DATE</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(operator,id) in operators" :key="id">
                                <td>{{id+1}}</td>
                                <td>{{operator.operator_name.toUpperCase()}}</td>
                                <td>{{operator.user_name.toUpperCase()}}</td>
                                <td>{{operator.created_at}}</td>

                                <td>
                                    <Link type="button" :href="'//edit/' + operator.operators_id"  class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
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
    layout: Layout,
    components: {
        Link
    },
    props: {
        errors: Object,
        operators:Array
    }
}

</script>
