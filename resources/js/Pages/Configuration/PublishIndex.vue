<template>

    <div class="container-fluid p-0">

        <form @submit.prevent="publishConfig">

            <div class="row mb-2 mb-xl-3">

                <div class="col-auto d-none d-sm-block">
                    <h3><strong>PUBLISH</strong> CONFIGURATION</h3>
                </div>

                <div class="col-auto ms-auto text-end mt-n1">
                    <button
                        type="button"
                        :disabled="!isAnyConfigSelected"
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#exampleModal">

                        <font-awesome-icon
                            icon="fa-solid fa-power-off"
                        />

                        Publish Configuration

                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Select Equipment for publish </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="example" class="table table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>TYPE</th>
                                        <th>STATION</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th>EQUIP ID</th>
                                        <th>TYPE</th>
                                        <th>STATION</th>
                                        <th>IP</th>
                                        <th>STATUS</th>
                                        <th><input disabled class="form-check-input" type="checkbox" v-model="form.select_all"
                                                   id="select_all" v-on:click="select"></th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    <tr v-for="(Equipment,id) in Equipments" :key="id">
                                        <td>{{id+1}}</td>
                                        <td>{{Equipment.eq_id}}</td>
                                        <td>{{Equipment.eq_type_name}}</td>
                                        <td>{{Equipment.stn_name}}</td>
                                        <td>{{Equipment.ip_address}}</td>
                                        <td v-if="Equipment.status === true">
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td v-else>
                                            <span class="badge bg-danger">Inactive</span>
                                        </td>

                                        <td>
                                            <input class="form-check-input" type="checkbox" v-model="form.selected"
                                                   :value="Equipment.eq_id" id="version_checkbox">
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>
                            </div>
                            <div class="modal-footer">

                                <button
                                    type="button"
                                    id="closeModalButton"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                    Close
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="!isallParametersSelected && !isSelectedConfigSelected"
                                    data-bs-dismiss="modal">
                                    <font-awesome-icon
                                        icon="fa-solid fa-save"
                                    />&nbsp;
                                    Publish
                                </button>

                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">

                            <table id="example2" class="table table-striped" style="width:100%">
                                <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>CONFIGURATION NAME</th>
                                    <th>SELECT VERSION</th>
                                    <th>ACTIVATION TIME</th>
                                    <th><input  class="form-check-input" type="checkbox" v-model="form.allParameters"
                                               id="allParameters" v-on:click="config_all"></th>
                                    </tr>
                                </thead>

                                <tbody>

                                <!--FOR EQUIPMENT-->
                                <tr>
                                    <td>1</td>
                                    <td>Equipment Config</td>
                                    <td>
                                        <select disabled id="Eq_config_version" class="form-control" name="config_version" v-model="form.Equipment.version">
                                            <option value="-1">Select Version</option>
                                            <option v-for="Eq in EqConfig" :value="Eq.config_version">{{Eq.config_version }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum input-sm" v-model="form.Equipment.activation_time" placeholder="Enter Activation Time"/>
                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.Equipment.isSelected" id="Eq_version_checkbox">
                                    </td>
                                </tr>

                                <!--FOR FARE-->
                                <tr>
                                    <td>2</td>
                                    <td>Fare Config</td>
                                    <td>
                                        <select id="config_version" class="form-control" name="config_version" v-model="form.Fare.version">
                                            <option value="-1">Select Version</option>
                                            <option v-for="Fare in FareConfig" :value="Fare.config_version">{{Fare.config_version }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum input-sm" v-model="form.Fare.activation_time" placeholder="Enter Activation Time"/>
                                        <div class="text-danger" v-if="errors.activation_time">{{ errors.activation_time }}</div>
                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.Fare.isSelected" :value="2" id="fare_version_checkbox">
                                        <input hidden class="form-check-input" type="checkbox" v-model="form.Fare.config_id" :value="2" id="fare_config_id">
                                    </td>
                                </tr>

                                <!--FOR STATION-->
                                <tr>
                                    <td>3</td>
                                    <td>Station Config</td>
                                    <td>
                                        <select disabled id="stn_config_version" class="form-control"
                                                name="config_version" v-model="form.Station.version">
                                            <option value="-1">Select Version</option>
                                            <option v-for="Station in StnConfig" :value="Station.config_version">{{
                                                Station.config_version }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum input-sm"
                                               v-model="form.Station.activation_time"
                                               placeholder="Enter Activation Time"/>
                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox"
                                               v-model="form.Station.isSelected" id="stn_version_checkbox">
                                    </td>
                                </tr>

                                <!--FOR PASS-->
                                <tr>
                                    <td>4</td>
                                    <td>Pass Config</td>
                                    <td>
                                        <select id="pass_config_version" class="form-control" name="config_version"
                                                v-model="form.Pass.version">
                                            <option value="-1">Select Version</option>
                                            <option v-for="Pass in PassConfig" :value="Pass.config_version">{{
                                                Pass.config_version }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum"
                                               v-model="form.Pass.activation_time" placeholder="Enter Activation Time"/>
                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.Pass.isSelected"
                                               id="pass_version_checkbox">
                                    </td>
                                </tr>

                                <!--FOR USER-->
                                <tr>
                                    <td>5</td>

                                    <td>User Config</td>

                                    <td>
                                        <select disabled id="user_config_version" class="form-control"
                                                name="config_version" v-model="form.User.version">
                                            <option value="1">Select Version</option>
                                            <option v-for="User in UserConfig" :value="User.config_version">{{
                                                User.config_version }}
                                            </option>
                                        </select>
                                    </td>

                                    <td>
                                        <input v-model="form.User.activation_time" type="text"
                                               class="form-control flatpickr-minimum"
                                               placeholder="Enter Activation Time">
                                    </td>

                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.User.isSelected"
                                               id="user_version_checkbox">
                                    </td>
                                </tr>

                                <!--FOR CARD BLACKLIST-->
                                <tr>
                                    <td>6</td>
                                    <td>Card Blacklist Config</td>
                                    <td>
                                        <select  id="card_blacklist_config_version" class="form-control" name="config_version" v-model="form.CardBlacklist.version">
                                            <option value="-1">Select Version</option>
                                            <option v-for="CardBl in CardBlConfig" :value="CardBl.config_version">{{CardBl.config_version }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum input-sm" v-model="form.CardBlacklist.activation_time" placeholder="Enter Activation Time"/>
                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.CardBlacklist.isSelected" id="cardBlacklist_version_checkbox">
                                    </td>
                                </tr>

                                <!-- FOR ACQ PARAM -->
                                <tr>
                                    <td>7</td>
                                    <td>Acquirer Config</td>

                                    <td>
                                        <select disabled id="acq_config_version" class="form-control" v-model="form.Acquirer.version">
                                            <option value="1">Select Version</option>
                                            <option v-for="AcqParamConfig in AcqParamConfig" :value="AcqParamConfig.config_version">{{AcqParamConfig.config_version }}</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control flatpickr-minimum input-sm" v-model="form.Acquirer.activation_time" placeholder="Enter Activation Time"/>
                                        <div class="text-danger" v-if="errors.activation_time">{{ errors.activation_time}}</div>
                                    </td>

                                    <td>
                                        <input class="form-check-input" type="checkbox" v-model="form.Acquirer.isSelected" :value="2" id="acq_params_checkbox">
                                        <input hidden class="form-check-input" type="checkbox" v-model="form.Acquirer.config_id" :value="2" id="acq_params_config_id">
                                    </td>

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
import 'vue3-treeselect/dist/vue3-treeselect.css'
import axios from 'axios'
import Swal from 'sweetalert2';

function  showSuccessSweetAlert() {
        Swal.fire({
            title: 'Configuration Published Successfully',
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'OKAY',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });

    }

    function  showFailureSweetAlert() {
        Swal.fire({
            title: 'Configuration Published Successfully',
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'OKAY',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });

    }

    export default {

        props: {
            errors: null,
            EqConfig: Array,
            FareConfig: Array,
            StnConfig: Array,
            PassConfig: Array,
            UserConfig: Array,
            EqBlConfig: Array,
            CardBlConfig: Array,
            scsConfig: Array,
            Equipments: Array,
            AcqParamConfig: Array,
            options: Array
        },
        name: "PublishIndex",
        layout: Layout,
        components: {
            Link,
        },

        data() {

            return {

                form: useForm({
                    selected: [],
                    select_all: false,
                    allParameters: false,
                    Equipment: {
                        config_id: 1,
                        version: -1,
                        activation_time: null,
                        isSelected: false
                    },
                    Station: {
                        config_id: 3,
                        version: -1,
                        activation_time: null,
                        isSelected: false
                    },
                    Pass: {
                        config_id: 4,
                        version: -1,
                        activation_time: null,
                        isSelected: false
                    },
                    Fare: {
                        config_id: 2,
                        version: -1,
                        activation_time: null,
                        isSelected: false
                    },
                    User: {
                        config_id: 5,
                        version: 1,
                        activation_time: null,
                        isSelected: false
                    },
                    CardBlacklist: {
                        config_id: 7,
                        version: -1,
                        activation_time: null,
                        isSelected: false
                    },
                    Scs: {
                        config_id: 9,
                        version: 1,
                        activation_time: null,
                        isSelected: false
                    },
                    Acquirer: {
                        config_id: 8,
                        version: 1,
                        activation_time: null,
                        isSelected: false
                    },
                }),

                value: null,
            }
        },

        methods: {

            publishConfig: function () {
                axios.post('/publish/create', this.form)
                    .then((response) => {
                        if (response.status === 200 && response.statusText === 'ok') {
                            showSuccessSweetAlert()
                        } else {
                            showFailureSweetAlert()
                            console.log(response)
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                    });
            },



            select: function () {
                this.form.selected = [];
                if (!this.form.select_all) {
                    for (let i in this.Equipments) {
                        this.form.selected.push(this.Equipments[i].eq_id);
                    }
                }
            }

        },

        mounted() {

            this.Equipments.forEach(function (equipmment) {})

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                altInput: true
            });

            $("#example").DataTable({
                "bSort": false,
                "ordering": true,
                initComplete: function () {
                    this.api().columns([2, 3]).every(function (d) {
                        var column = this;
                        var theadname = $("#example th").eq([d]).text(); //used this specify table name and head
                        var select = $('<select class="form-control form-select"><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on("change", function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column._isSelectMultipleElement = true
                                column
                                    .search(val ? "^" + val + "$" : "", true, false)
                                    .draw();
                            });
                        column.data().unique().sort().each(function (d, j) {
                            select.append("<option value=\"" + d + "\">" + d + "</option>")
                        });
                    });
                }
            });

            $("#example2").DataTable({
                responsive: true,
                "paging": true,
                "ordering": false,
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

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput: true
            });

            flatpickr(".flatpickr-dob", {
                enableTime: false,
                dateFormat: "Y-m-d",
                allowInput: true,
                maxDate: new Date()

            });

            document.getElementById('closeModalButton').addEventListener('click', () => {
                window.location.reload();
            });



        },

        computed: {

            config_all() {
                this.form.Equipment.isSelected      = !this.form.allParameters;
                this.form.Station.isSelected        = !this.form.allParameters;
                this.form.Pass.isSelected           = !this.form.allParameters;
                this.form.Fare.isSelected           = !this.form.allParameters;
                this.form.User.isSelected           = !this.form.allParameters;
                this.form.CardBlacklist.isSelected  = !this.form.allParameters;
                this.form.Scs.isSelected            = !this.form.allParameters;
                this.form.Acquirer.isSelected       = !this.form.allParameters;
            },

            isAnyConfigSelected() {
                return (
                    this.form.Equipment.isSelected ||
                    this.form.Station.isSelected ||
                    this.form.Pass.isSelected ||
                    this.form.Fare.isSelected ||
                    this.form.User.isSelected ||
                    this.form.CardBlacklist.isSelected ||
                    this.form.Scs.isSelected ||
                    this.form.Acquirer.isSelected
                );
            },

            isallParametersSelected() {
                return this.form.select_all;
            },

            isSelectedConfigSelected() {
                return this.form.selected.length > 0;
            },

        }

    }
</script>

<style scoped>

</style>
