<template>

    <div class="container-fluid p-0">

        <!--MAIN HEADING-->
        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CREATE </strong> EQUIPMENT</h3>
            </div>
        </div>

        <!--FORM WITH BUTTONS-->
        <div class="row">

            <!--FORM FOR EQUIPMENT CREATION-->
            <form @submit.prevent="storeEquipments">

                <!--BASIC DETAILS-->
                <div class="card">

                    <div class="card-body">

                        <div class="row">

                            <h5 class="pb-2"><strong>BASIC </strong> DETAILS</h5>

                            <!--ATEK ID-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Atek Equipment ID<span class="text-danger"> *</span></label>
                                <input disabled v-model="form.atek_eq_id" type="text" class="form-control"/>
                                <div class="text-danger" v-if="errors.atek_eq_id">{{ errors.atek_eq_id}}</div>
                            </div>

                            <!--STATION ID-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Select Station <span class="text-danger">*</span></label>
                                <select v-model="form.stn_id" class="form-control form-select" name="line_id" v-on:change="updateText(false)">
                                    <option selected value="null">Select Station</option>
                                    <option v-for="station in Stations" :value="station.stn_id">{{ station.stn_name }}
                                    </option>
                                </select>
                                <div class="text-danger" v-if="errors.stn_id">{{ errors.stn_id}}</div>
                            </div>

                            <!--TYPE OF EQUIPMENTS-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="eq_type_id">Equipment Type<span class="text-danger"> *</span></label>
                                <select id="eq_type_id" v-model="form.eq_type_id" class="form-control form-select"  v-on:change="updateText(true)" name="eq_type_id">
                                    <option selected value="null">Select Type</option>
                                    <option v-for="EquipmentType in EquipmentTypes" :value="EquipmentType.eq_type_id">{{EquipmentType.eq_type_name }}
                                    </option>
                                </select>
                                <div class="text-danger" v-if="errors.eq_type_id">{{ errors.eq_type_id}}</div>
                            </div>

                            <!--EQ NUMBER-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="eq_num">Equipment Number<span class="text-danger"> *</span></label>
                                <input type="number" min="1" v-on:change="updateText(false)" v-model="form.eq_num" name="eq_num" class="form-control" id="eq_num" placeholder="Enter Number">
                                <div class="text-danger" v-if="errors.eq_num">{{ errors.eq_num}}</div>

                            </div>

                            <template v-if="form.eq_type_id===null || form.eq_type_id===2  || form.eq_type_id===3  ||form.eq_type_id===4">

                            </template>
                            <template v-else>
                                <!--EQUIPMENT MODE -->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label" for="on_eq_mode_id">Equipment Mode<span class="text-danger"> *</span></label>
                                    <select id="on_eq_mode_id" v-model="form.eq_mode_id" class="form-control form-select" name="eq_mode_id" v-on:change="getRoles">
                                        <option selected value="null">Select Mode</option>
                                        <option v-for="EquipmentMode in EquipmentModes" :value="EquipmentMode.eq_mode_id">{{ EquipmentMode.eq_mode_name }}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.eq_mode_id">{{ errors.eq_mode_id}}</div>
                                </div>
                            </template>

                            <template
                                v-if="form.eq_type_id===null  || form.eq_type_id=== 6 || form.eq_type_id===4 || form.eq_type_id===5">
                            </template>

                            <template v-else>
                                <!--EQUIPMENT ROLE-->
                                <div class="mb-3 col-md-3">
                                    <label class="form-label" for="on_eq_role_id">Equipment Role <span class="text-danger"> *</span></label>
                                    <select id="on_eq_role_id" v-model="form.eq_role_id" class="form-control form-select" name="eq_role_id">
                                        <option selected value="null">Select Role</option>
                                        <option v-for="EquipmentRole in EquipmentRoles" :value="EquipmentRole.eq_role_id">{{EquipmentRole.eq_role_name}}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.eq_role_id">{{ errors.eq_role_id}}</div>
                                </div>
                            </template>

                            <!--EQUIPMENT ID-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Equipment ID<span class="text-danger"> *</span></label>
                                <input disabled v-model="form.eq_id" type="number" name="eq_id" class="form-control"/>
                                <div class="text-danger" v-if="errors.eq_id">{{ errors.eq_id}}</div>
                            </div>

                            <!--EQUIPMENT LOCATION-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Equipment Location<span class="text-danger"> *</span></label>
                                <select  v-model="form.eq_loc_id" class="form-control form-select">
                                    <option  selected value="null">Select Location</option>
                                    <option :value="location.equipment_location_id" v-for="location in EndLocation">{{location.location_name.toUpperCase()}}</option>
                                </select>
                                <div class="text-danger" v-if="errors.eq_loc_id">{{ errors.eq_loc_id}}</div>
                            </div>

                            <!--EQUIPMENT DESCRIPTION-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="description">Description</label>
                                <input id="description" v-model="form.description" name="description" class="form-control" placeholder="Enter Description"/>
                                <div class="text-danger" v-if="errors.description">{{ errors.description}}</div>
                            </div>

                            <!--COORDINATE X-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="cord_x">X-Coordinate<span class="text-danger"> *</span></label>
                                <input type="number" v-model="form.cord_x" id="cord_x" name="cord_x" min="0" class="form-control" placeholder="X-Coordinate"/>
                                <div class="text-danger" v-if="errors.cord_x">{{ errors.cord_x}}</div>

                            </div>

                            <!--COORDINATE Y-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="cord_y">Y-Coordinate<span class="text-danger"> *</span></label>
                                <input type="number" min="0" v-model="form.cord_y" id="cord_y" name="cord_y" class="form-control" placeholder="Y-Coordinate"/>
                                <div class="text-danger" v-if="errors.cord_y">{{ errors.cord_y}}</div>

                            </div>

                            <!--STATUS-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" v-model="form.status" class="form-control form-select" name="status">
                                    <option value="null">Select Status</option>
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                                <div class="text-danger" v-if="errors.status">{{ errors.status}}</div>
                            </div>

                            <!--START DATE-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input v-model="form.start_date" type="text" class="form-control flatpickr-minimum" placeholder="Enter Start Date">
                                <div class="text-danger" v-if="errors.start_date">{{ errors.start_date}}</div>
                            </div>

                            <!--END DATE-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label">End Date</label>
                                <input v-model="form.end_date" type="text" class="form-control flatpickr-minimum" placeholder="Enter End Date">
                            </div>

                        </div>

                    </div>

                </div>

                <!--NETWORK CONFIGURATION-->
                <div class="card">

                    <div class="card-body">

                        <div class="row">

                            <h5 class="pb-2"><strong>NETWORK </strong> DETAILS</h5>

                            <!--IP ADDRESS-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="ip_address">IP Address<span class="text-danger"> *</span></label>
                                <input type="text" v-model="form.ip_address" id="ip_address" name="ip_address" class="form-control" placeholder="IP Address" v-on:change="edcReaderParams"/>
                                <div class="text-danger" v-if="errors.ip_address">{{ errors.ip_address}}</div>
                            </div>

                            <!-- PRIMARY SSID-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="primary_ssid"> Primary SSID<span class="text-danger"> *</span></label>
                                <input type="text" v-model="form.primary_ssid" id="primary_ssid" name="primary_ssid" class="form-control" placeholder=" Primary SSID"/>
                                <div class="text-danger" v-if="errors.primary_ssid">{{ errors.primary_ssid}}</div>
                            </div>

                            <!-- PRIMARY SSID PWD-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="primary_ssid_pwd"> Primary SSID Password<span class="text-danger"> *</span></label>
                                <input type="text" v-model="form.primary_ssid_pwd" id="primary_ssid_pwd" name="primary_ssid_pwd" class="form-control" placeholder=" Primary SSID Password"/>
                                <div class="text-danger" v-if="errors.primary_ssid_pwd">{{ errors.primary_ssid_pwd}}</div>
                            </div>

                            <!-- SECONDARY SSID-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="backup_ssid">Backup SSID</label>
                                <input type="text" v-model="form.backup_ssid" id="backup_ssid" name="backup_ssid" class="form-control" placeholder="Backup SSID"/>
                            </div>

                            <!-- SECONDARY SSID PWD-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="backup_ssid_pwd">Backup SSID Password</label>
                                <input type="text" v-model="form.backup_ssid_pwd" id="backup_ssid_pwd" name="backup_ssid_pwd" class="form-control" placeholder="Backup SSID Password"/>
                            </div>

                            <!--GATEWAY-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="gateway">Gateway<span class="text-danger"> *</span></label>
                                <input type="text" v-model="form.gateway" id="gateway" name="gateway" class="form-control" placeholder="Gateway"/>
                                <div class="text-danger" v-if="errors.gateway">{{ errors.gateway}}</div>
                            </div>

                            <!--SUBNET MASK-->
                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="subnetmask">Subnetmask<span class="text-danger"> *</span></label>
                                <input type="text" v-model="form.subnetmask" id="subnetmask" name="subnetmask" class="form-control" placeholder="Subnetmask"/>
                                <div class="text-danger" v-if="errors.subnetmask">{{ errors.subnetmask}}</div>
                            </div>

                        </div>

                    </div>

                </div>

                <!--BUTTONS-->
                <div class="row mb-2 mb-xl-3">

                    <!--FOR BACK BUTTON-->
                    <div class="col-auto d-none d-sm-block">
                        <Link :href="'/equipments'" class="btn btn-outline-primary"><i class="fas fa-backward me-2"></i>Back
                        </Link>
                    </div>

                    <!--SAVE BUTTON-->
                    <div class="col-auto ms-auto text-end mt-n1">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-save me-2"></i>Save</button>
                    </div>

                    <!-- MODAL -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-light">
                                <div class="modal-body m-3 text-center">
                                    <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                    <h3 class="m-2"><span>Are you sure! do you want to Create this Equipment ?</span></h3>
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

</template>

<script>

    /*IMPORTS*/
    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";
    import axios from "axios"

    export default {

        props: {
            scs_id: Array,
            EquipmentTypes: Array,
            EquipmentRoles: null,
            EquipmentModes: Array,
            Stations: Array,
            EndLocation :Array,
            errors: Object,
        },
        name: "Create",
        layout: Layout,
        components: {
            Link
        },

        /*FORM DATA*/
        data() {
            return {
                form: useForm({
                        eq_type_id: null,
                        eq_mode_id: null,
                        eq_role_id: null,
                        eq_num: '',
                        atek_eq_id: null,
                        eq_id: null,
                        stn_id: null,
                        eq_loc_id: null,
                        description: null,
                        cord_x: null,
                        cord_y: null,
                        status: null,
                        start_date: null,
                        end_date: null,
                        ip_address: null,
                        primary_ssid: null,
                        primary_ssid_pwd: null,
                        backup_ssid: null,
                        backup_ssid_pwd: null,
                        gateway: null,
                        subnetmask: null
                })
            }
        },

        methods: {

            async scsCheck() {

                if (this.form.eq_type_id !== 4) {

                    const res = await axios.post("/api/checkSCS", {
                        "stn_id": this.form.stn_id
                    })

                    if (res.data.status === false) {
                        alert("First create SCS for this station")
                    }
                }

            },

            async updateText(isEqType) {

                if (isEqType) {
                    await this.scsCheck()
                }

                this.form.eq_id = ((this.form.stn_id.toString().length === 2) ? this.form.stn_id : "0" + this.form.stn_id)
                    + ((this.form.eq_type_id.toString().length === 2) ? this.form.eq_type_id : "0" + this.form.eq_type_id)
                    + ((this.form.eq_num.toString().length === 2) ? this.form.eq_num : "0" + this.form.eq_num);

                this.form.atek_eq_id = 'ATEK' +  this.form.eq_id;

            },

            storeEquipments: function () {
                this.form.post('/equipments')
            },

        },

        mounted() {

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput: true
            });

        },
    }

</script>
