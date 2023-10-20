<template>

    <div class="container-fluid p-0">

        <form @submit.prevent="publishFirmware">

            <div class="row mb-2 mb-xl-3">

                <div class="col-auto d-none d-sm-block">
                    <h3><strong>PUBLISH</strong> FIRMWARE</h3>
                </div>

                <!-- MODAL FOR AG -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Select AG Equipment for Firmware publish </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="example" class="table table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th><input class="form-check-input" type="checkbox" v-model="form.AG_select_all" id="AG_select_all" v-on:click="AGSelect"></th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    <tr v-for="(Equipment,id) in AGEquipments" :key="id">
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
                                            <input class="form-check-input" type="checkbox" v-model="form.AG_selected" :value="Equipment.eq_id" id="version_checkbox">
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><font-awesome-icon icon="fa-solid fa-save"/>&nbsp; Publish Firmware</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL FOR TOM -->
                <div class="modal fade" id="TOM" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="TOMFirmware">Select TOM Equipment for Firmware publish </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="TOMTable" class="table table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th><input class="form-check-input" type="checkbox" v-model="form.TOM_select_all" id="tom_select_all" v-on:click="TOMSelect"></th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    <tr v-for="(Equipment,id) in TOMEquipments" :key="id">
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
                                            <input class="form-check-input" type="checkbox" v-model="form.TOM_selected" :value="Equipment.eq_id" id="tom_version_checkbox">
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><font-awesome-icon icon="fa-solid fa-save"/>&nbsp; Publish Firmware</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL FOR EDC -->
                <div class="modal fade" id="EDC" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="EDCFirmware">Select EDC Equipment for Firmware publish </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="EDCTable" class="table table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th><input class="form-check-input" type="checkbox" v-model="form.EDC_select_all" id="EDC_select_all" v-on:click="EDCSelect"></th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    <tr v-for="(Equipment,id) in EDCEquipments" :key="id">
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
                                            <input class="form-check-input" type="checkbox" v-model="form.EDC_selected" :value="Equipment.eq_id" id="EDC_version_checkbox">
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><font-awesome-icon icon="fa-solid fa-save"/>&nbsp; Publish Firmware</button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- MODAL FOR SCS -->
                <div class="modal fade" id="SCS" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="SCSFirmware">Select SCS Equipment for Firmware publish </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="SCSTable" class="table table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th><input class="form-check-input" type="checkbox" v-model="form.SCS_select_all" id="SCS_select_all" v-on:click="SCSSelect"></th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    <tr v-for="(Equipment,id) in SCSEquipments" :key="id">
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
                                            <input class="form-check-input" type="checkbox" v-model="form.SCS_selected" :value="Equipment.eq_id" id="SCC_version_checkbox">
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><font-awesome-icon icon="fa-solid fa-save"/>&nbsp; Publish Firmware</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="row">

                <div class="col-12">

                    <div class="card">

                        <div class="card-body">

                            <table id="generate" class="table table-striped" style="width:100%">

                                <thead>

                                <tr>
                                    <th>S.NO</th>
                                    <th>FIRMWARE DEVICE</th>
                                    <th>SELECT VERSION </th>
                                    <th>DESCRIPTION </th>
                                    <th>PUBLISH</th>
                                </tr>

                                </thead>

                                <tbody>

                                <!--FOR AG -->
                                <tr>
                                    <td>1</td>
                                    <td>AG FIRMWARE</td>
                                    <td><select id="ag_version" class="form-control"  v-model="form.AG.AGVersion">
                                        <option value="null">Select Version of AG Jar</option>
                                        <option v-for="AGVersion in AGVersions" :value="AGVersion.firmware_version">
                                            {{AGVersion.firmware_version}}
                                        </option>
                                    </select>
                                        <div class="text-danger" v-if="errors.AGVersion">{{ errors.AGVersion }}</div>

                                    </td>
                                    <td>
                                        <input  v-model="form.AG.AG_description" class="form-control" placeholder="Enter Description For AG"/>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-paper-plane"/>&nbsp;AG Firmware</button>
                                    </td>
                                </tr>

                                <!--FOR TOM-->
                                <tr>
                                    <td>2</td>
                                    <td>TOM FIRMWARE</td>
                                    <td>
                                        <select  class="form-control" v-model="form.TOM.TOMVersion">
                                            <option value="null">Select Version of Tom Apk</option>
                                            <option v-for="TOMVersion in TOMVersions" :value="TOMVersion.firmware_version">{{TOMVersion.firmware_version}}</option>
                                        </select>
                                        <div class="text-danger" v-if="errors.TOMVersion">{{ errors.TOMVersion }}</div>

                                    </td>
                                    <td>
                                        <input  v-model="form.TOM.TOM_description" class="form-control" placeholder="Enter Description For TOM"/>
                                    </td>
                                    <td><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#TOM"><font-awesome-icon icon="fa-solid fa-paper-plane"/>&nbsp;TOM Firmware</button></td>
                                </tr>

                                <!--FOR EDC-->
                                <tr>
                                    <td>3</td>
                                    <td>EDC FIRMWARE</td>
                                    <td>
                                        <select  class="form-control" v-model="form.EDC.EDCVersion">
                                            <option value="null">Select Version of EDC Apk</option>
                                            <option v-for="EDCVersion in EDCVersions" :value="EDCVersion.firmware_version">{{EDCVersion.firmware_version}}</option>
                                        </select>
                                        <div class="text-danger" v-if="errors.EDCVersion">{{ errors.EDCVersion }}</div>

                                    </td>
                                    <td>
                                        <input  v-model="form.EDC.EDC_description" class="form-control" placeholder="Enter Description For EDC"/>
                                    </td>
                                    <td><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#EDC"><font-awesome-icon icon="fa-solid fa-paper-plane"/>&nbsp;EDC Firmware</button></td>
                                </tr>

                                <!--FOR SCS-->
                                <tr>
                                    <td>4</td>
                                    <td>SCS FIRMWARE</td>
                                    <td>
                                        <select  disabled class="form-control" v-model="form.SCS.SCSVersion">
                                            <option value="null">Select Version of SCS Apk</option>
                                            <option v-for="SCSVersion in SCSVersions" :value="SCSVersion.firmware_version">{{SCSVersion.firmware_version}}</option>
                                        </select>
                                        <div class="text-danger" v-if="errors.SCSVersion">{{ errors.SCSVersion }}</div>

                                    </td>
                                    <td>
                                        <input  v-model="form.SCS.SCS_description" class="form-control" placeholder="Enter Description For SCS"/>
                                    </td>
                                    <td><button type="button" disabled class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#SCS"><font-awesome-icon icon="fa-solid fa-paper-plane"/>&nbsp;SCS Firmware</button></td>
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
    name: "FirmwarePublish",
    layout: Layout,
    components: {
        Link
    },

    props:{
        AGEquipments: Array,
        TOMEquipments: Array,
        EDCEquipments: Array,
        SCSEquipments: Array,
        AGVersions: Array,
        TOMVersions: Array,
        EDCVersions: Array,
        SCSVersions: Array,
        errors: Object,
    },

    data(){
        return {

            form: useForm({
                AG_selected: [],
                AG_select_all: false,
                TOM_selected: [],
                TOM_select_all: false,
                EDC_selected: [],
                EDC_select_all: false,
                SCS_selected: [],
                SCS_select_all: false,

                AG: {
                    firmware_id: 1,
                    AGVersion:null,
                    AG_description:'',
                    isSelected: false,
                    AGValue: null
                },

                TOM: {
                    firmware_id: 1,
                    TOMVersion:null,
                    TOM_description:'',
                    isSelected: false,
                    TOMValue: null
                },

                EDC: {
                    firmware_id: 1,
                    EDCVersion:null,
                    EDC_description:'',
                    isSelected: false,
                    EDCValue: null
                },

                SCS: {
                    firmware_id: 1,
                    SCSVersion:null,
                    SCS_description:'',
                    isSelected: false,
                    SCSValue: null
                },
            }),
            value: null,
        }
    },


    methods:{

        AGSelect: function () {
            this.form.AG_selected = [];
            if (!this.form.AG_select_all) {
                for (let i in this.AGEquipments) {
                    this.form.AG_selected.push(this.AGEquipments[i].eq_id);
                }
            }
        },

        TOMSelect: function () {
            this.form.TOM_selected = [];
            if (!this.form.TOM_select_all) {
                for (let i in this.TOMEquipments) {
                    this.form.TOM_selected.push(this.TOMEquipments[i].eq_id);
                }
            }
        },

        EDCSelect: function () {
            this.form.EDC_selected = [];
            if (!this.form.EDC_select_all) {
                for (let i in this.EDCEquipments) {
                    this.form.EDC_selected.push(this.EDCEquipments[i].eq_id);
                }
            }
        },

        SCSSelect: function () {
            this.form.SCS_selected = [];
            if (!this.form.SCS_select_all) {
                for (let i in this.SCSEquipments) {
                    this.form.SCS_selected.push(this.SCSEquipments[i].eq_id);
                }
            }
        },

        publishFirmware: function () {
            this.form.post('/firmwarePublish')
        },
    },

    mounted() {

        $("#example").DataTable({
            "bSort": false,

            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#example th").eq([d]).text(); // used this specify table name and head
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

        $("#TOMTable").DataTable({
            "bSort": false,

            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#TOMTable th").eq([d]).text(); // used this specify table name and head
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

        $("#EDCTable").DataTable({
            "bSort": false,

            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#EDCTable th").eq([d]).text(); // used this specify table name and head
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

        $("#SCSTABLE").DataTable({
            "bSort": false,

            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#SCSTABLE th").eq([d]).text(); // used this specify table name and head
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

        $("#generate").DataTable({
            responsive  : true,
            "paging"    : true,
            "ordering"    : false,
            columnDefs: [
                {
                    targets: 0,
                    "className": "text-center",
                },
                {
                    targets: 1,
                    "className": "text-start",
                },
                {
                    targets: 2,
                    "className": "text-center",
                },
                {
                    targets: 3,
                    "className": "text-center",
                },

                {
                    targets: 4,
                    "className": "text-center",
                },
            ]
        });
    }
}
</script>

<style scoped>

</style>
