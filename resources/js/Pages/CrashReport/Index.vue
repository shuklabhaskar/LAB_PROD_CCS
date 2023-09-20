<template>

    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">

            <div class="col-auto d-none d-sm-block">

                <h3>
                    <strong>CRASH</strong> REPORTS
                </h3>

            </div>

            <div class="accordion" id="accordionExample">

                <!--AG LOGS -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            GATE LOGS
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                         data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="card">

                                <div class="card-body">

                                    <table id="agLogs" class="table table-striped" style="width:100%">

                                        <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <th>DATE</th>
                                            <th>VIEW</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="(gate_log,id) in gate_logs" :key="id">
                                            <td>{{id+1}}</td>
                                            <td>{{gate_log.created_date}}</td>
                                            <td>
                                                <Link type="button" :href="'/gate/log/' + gate_log.gate_log_id"
                                                      class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
                                                    <font-awesome-icon icon="fa-solid fa-eye"/>
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

                <!--TOM LOGS -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            TOM LOGS
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                         data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="card">

                                <div class="card-body">

                                    <table id="tomLogs" class="table table-striped" style="width:100%">

                                        <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <th>DATE</th>
                                            <th>VIEW</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="(tom_log,id) in tom_logs" :key="id">
                                            <td>{{id+1}}</td>
                                            <td>{{tom_log.created_date}}</td>
                                            <td>
                                                <Link type="button" :href="'/tom/log/' + tom_log.tom_log_id"
                                                      class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
                                                    <font-awesome-icon icon="fa-solid fa-eye"/>
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

                <!--EDC LOGS-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            EDC LOGS
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                         data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="card">

                                <div class="card-body">

                                    <table id="edcLogs" class="table table-striped" style="width:100%">

                                        <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <th>DATE</th>
                                            <th>VIEW</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="(edc_log,id) in edc_logs" :key="id">
                                            <td>{{id+1}}</td>
                                            <td>{{edc_log.created_date}}</td>
                                            <td>
                                                <Link type="button" :href="'/edc/log/' + edc_log.edc_log_id"
                                                      class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
                                                    <font-awesome-icon icon="fa-solid fa-eye"/>
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
            gate_logs: Array,
            tom_logs: Array,
            edc_logs: Array,
        },

        mounted() {

            $("#agLogs").DataTable({
                responsive: true,
                "paging": true,
            });

            $("#tomLogs").DataTable({
                responsive: true,
                "paging": true,
            });

            $("#edcLogs").DataTable({
                responsive: true,
                "paging": true,
            });

        }
    }
</script>

<style scoped>

</style>
