<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CREATE</strong> FARE</h3>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-body">

                        <form @submit.prevent="storeFares">

                            <div class="row">

                                <!--FARE NAME-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Fare Name <span class="text-danger"> *</span></label>
                                    <input type="text" v-model="fareData.fare_name" class="form-control" placeholder="Fare Name">
                                    <div class="text-danger" v-if="errors.fare_name">{{ errors.fare_name }}</div>
                                </div>

                                <!--STATUS-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Status <span class="text-danger"> *</span></label>
                                    <select required v-model="fareData.status" class="form-control">
                                        <option value="null">Select Status</option>
                                        <option :value="true">ACTIVE</option>
                                        <option :value="false">INACTIVE</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.status">{{ errors.status }}</div>
                                </div>

                                <!--DESCRIPTION-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Description</label>
                                    <input v-model="fareData.description" class="form-control" placeholder="Description"/>
                                    <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                                </div>

                                <!--FARE VERSION-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Fare Version <span class="text-danger"> *</span></label>
                                    <input disabled type="number" v-model="fareData.fare_version" class="form-control" placeholder="Fare Version">
                                </div>

                                <!--START DATE-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="text" v-model="fareData.start_date" class="form-control flatpickr-minimum" placeholder="Enter Start Date">
                                    <div class="text-danger" v-if="errors.start_date">{{ errors.start_date }}</div>
                                </div>

                                <!--END DATE-->
                                <div class="mb-3  col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="text" v-model="fareData.end_date" class="form-control flatpickr-minimum" placeholder="Enter End Date"/>
                                </div>

                            </div>

                            <!--TABLE DATA-->
                            <table class="table table-responsive text-center w-100">
                                <tr>
                                    <td></td>
                                    <td v-for="Destination in Stations">
                                        <strong>{{ Destination.stn_short_name }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <table>
                                            <tr v-for="Source in Stations">
                                                <td style="padding: 7px">
                                                    <strong>{{ Source.stn_short_name }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td v-for="rowStation in Stations">
                                        <table>
                                            <tr v-for="colStation in Stations">
                                                <td>
                                                    <label>
                                                        <input type="number" min="0" class="form-control"
                                                               v-model="fareData.fare[rowStation.stn_id-1][colStation.stn_id-1]"
                                                               id="exampleInputEmail1" aria-describedby="emailHelp"
                                                               placeholder="Enter Fare">
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!--BUTTONS-->
                            <div class="row mb-2 mb-xl-3">

                                <!--BACK BUTTON-->
                                <div class="col-auto d-none d-sm-block">
                                    <Link :href="'/fares'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward"/> Back</Link>
                                </div>

                                <!--SAVE BUTTON-->
                                <div class="col-auto ms-auto text-end mt-n1">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-save"/> Save</button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-light">
                                            <div class="modal-body m-3 text-center">
                                                <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                                <h3 class="m-2">
                                                    <span>Are you sure! do you want to Create this Fare ?</span></h3>
                                                <a data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                                                <button type="submit" class="btn btn-primary m-2 btn-lg" data-bs-dismiss="modal">YES</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script>
    /*IMPORTS*/
    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";

    export default {

        props: {
            Stations: Array,
            Fare: Array,
            errors:Object
        },
        name: "Create",
        layout: Layout,
        components: {
            Link
        },

        data() {
            return {
                fareData: useForm({
                    fare: this.Fare,
                    fare_name: null,
                    description: null,
                    fare_version: 1,
                    status: null,
                    start_date: null,
                    end_date: null,
                })
            }
        },
        methods: {
            storeFares: function () {
                this.fareData.post('/fares')
            }
        },

        /*DATE CALENDER*/
        mounted() {
            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput:true
            });
        },

    }
</script>

<style scoped>

</style>
