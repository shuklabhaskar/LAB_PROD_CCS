<template>
    <div class="container-fluid p-0">

        <form @submit.prevent="storeParams">

            <div class="row mb-2 mb-xl-3">

                <div class="col-auto d-none d-sm-block">
                    <h3><strong>PASS</strong> PARAMETERS</h3>
                </div>

                <div class="col-auto ms-auto text-end mt-n1">
                    <button type="submit" class="btn btn-success"><font-awesome-icon icon="fa-solid fa-refresh" />&nbsp;Update Pass Parameters
                    </button>
                </div>

            </div>

            <div class="row">

                <div class="col-md-12">

                    <div class="card">

                        <div class="card-body">


                            <table id = "passParameters" class="table table-responsive">

                                <!--FOR SHOWING HEADING -->
                                <thead>
                                <tr>
                                    <th v-for="col in columns">{{col.toUpperCase().replaceAll('_', ' ')}}</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for="param in Parameters">
                                    <template v-for="col in columns">
                                        <td v-if="col == 'params'" >{{param.params.toUpperCase().replaceAll('_', ' ')}}</td>
                                        <td v-else-if="col == 'id'" >{{param[col]}}</td>
                                        <td v-else >
                                            <label>
                                                <input class="form-check-input" v-if="param[col]==1" checked type="checkbox"/>
                                                <input class="form-check-input" type="checkbox" v-else value="off"/>
                                            </label>
                                        </td>
                                    </template>
                                </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>

        </form>

    </div>

</template>

<script>
import Layout from "../Base/Layout";
import {Link, useForm} from "@inertiajs/inertia-vue3";

export default {
    props:{
        Parameters:Array,
        columns:Array,
        Params:Object,
    },
    name: "Parameters",
    layout: Layout,
    components: {
        Link
    },
    data() {
        return {
            form: useForm({
                param: [],
            })
        }
    },

    methods: {
        storeParams: function () {
            this.form.post('/pass/parameters')
        }
    },

    mounted() {
        $("#passParameters").DataTable({
            responsive: true,
            "paging": true,
            scrollY: 500,
            deferRender: true,
            scroller: true,
        });
    }
}
</script>

<style scoped>

</style>
