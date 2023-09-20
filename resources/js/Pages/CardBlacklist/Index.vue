<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CARD</strong> BLACKLIST</h3>
            </div>

        </div>

        <!--CARD WITH FORM AND BUTTONS-->
        <div class="card">

            <div class="card-body">

                <!--FORM FOR STATION INPUTS-->
                <form @submit.prevent="cardBlacklist">

                    <!--FORM INPUTS-->
                    <div class="row">

                        <!--MEDIA TYPE-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Media Type <span class="text-danger"> *</span></label>
                            <select class="form-control form-select" v-model="form.media_type_id">
                                <option value="null">Select Media Type</option>
                                <option v-for="MediaType in MediaTypes" :value="MediaType.media_type_id">
                                    {{MediaType.media_name}}
                                </option>
                            </select>
                            <div class="text-danger" v-if="errors.media_type_id">{{errors.media_type_id }}</div>
                        </div>

                        <!--BLACKLIST REASON-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Blacklist Reason <span class="text-danger"> *</span></label>
                            <select class="form-control form-select" v-model="form.ms_blk_reason_id">
                                <option value="null">Select Blacklist Reason</option>
                                <option v-for="BlacklistReason in BlacklistReasons" :value="BlacklistReason.ms_blk_reason_id">{{BlacklistReason.reason}}
                                </option>
                            </select>
                            <div class="text-danger" v-if="errors.ms_blk_reason_id">
                                {{errors.ms_blk_reason_id }}
                            </div>
                        </div>

                        <div class="mb-3 col-md-4"></div>

                        <!--START SERIAL NUMBER USING AS ENGRAVED ID-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Start Serial <span class="text-danger"> *</span></label>
                            <input type="number" class="form-control" v-model="form.engraved_id" placeholder="Enter Start Serial Number"/>
                            <div class="text-danger" v-if="errors.engraved_id">{{ errors.engraved_id }}</div>
                        </div>

                        <!--END SERIAL NUMBER-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">End Serial</label>
                            <input type="number" class="form-control"  v-model="form.end_serial" placeholder="Enter End Serial Number"/>
                            <div class="text-danger" v-if="errors.end_serial">{{ errors.end_serial }}</div>
                        </div>

                    </div>

                    <!-- SAVING DATA -->
                    <!--BUTTONS-->
                    <div class="row mb-2 mb-xl-3">

                        <!--SAVE BUTTON-->
                        <div class="col-auto ms-auto text-end mt-n1">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <font-awesome-icon icon="fa-solid fa-save"/>&nbsp;Save</button>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-light">
                                    <div class="modal-body m-3 text-center">
                                        <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                        <h3 class="m-2"><span>Are you sure! Do you want to add new cards for card Blacklist ?</span></h3>
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

        <!--BLACKLIST CARD TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form @submit.prevent="deleteCard">
                        <div class="card-body">
                            <table id="cardBlacklist" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th>Media Type</th>
                                        <th>Serial Number</th>
                                        <th>Blacklist Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr v-for="(blacklistedCard,cl_blk_id) in blacklistedCards" :key="cl_blk_id">

                                        <td>{{cl_blk_id+1}}</td>
                                        <td>Close Loop</td>
                                        <td>{{blacklistedCard.engraved_id}}</td>
                                        <td>{{blacklistedCard.reason}}</td>
                                        <td>
                                            <div class="col-auto ms-auto text-end mt-n1">
                                                <button v-on:click="getDeleteItemId(blacklistedCard.cl_blk_id)" type="button" class="btn btn-sm btn-icon btn-danger rounded"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can"/>&nbsp;Delete</button>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                             </table>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-light">
                                    <div class="modal-body m-3 text-center">
                                        <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                        <h3 class="m-2">
                                            <span>Are you sure! do you want to Delete this Card from Card Blacklist ?</span></h3>
                                        <a data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                                        <button type="submit" class="btn btn-danger m-2 btn-lg" data-bs-dismiss="modal">YES</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</template>

<script>

    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";

    export default {
        name: "Index",
        layout: Layout,

        components: {
            Link
        },
        props: {
            MediaTypes: Array,
            BlacklistReasons: Array,
            blacklistedCards:Array,
            errors: Object,
        },
        data() {
            return {
                form: useForm({
                    media_type_id: null,
                    ms_blk_reason_id: null,
                    engraved_id: null,
                    end_serial: null,
                    deleteItemId: Number
                })
            }
        },
        mounted() {

            $("#cardBlacklist").DataTable({
                responsive: true,
                "paging": true,
                'columnDefs': [
                    {
                        "targets": 4,
                        "className": "text-center",
                        "width": "4%",
                    },
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "4%",
                    }]
            });
        },

        methods: {

            cardBlacklist() {
                this.$inertia.post('/card/blacklist', this.form)
            },

            deleteCard: function () {
                this.form.post('/card/blacklist/delete/'+ this.deleteItemId)
                console.log("API")
            },

            getDeleteItemId: function (id) {
                console.log("ID -" + id)
                this.deleteItemId = id
                console.log(this.deleteItemId)
            },
        },

    }

</script>
