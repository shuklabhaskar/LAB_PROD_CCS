<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>CREATE </strong>OPERATORS PRIVILEGE</h3>
        </div>
    </div>

    <!--CARD WITH FORM AND BUTTONS-->
    <form @submit.prevent="storeOperatorsPrivilege">
        <div class="card">

            <div class="card-body">

                <!--FORM FOR OPERATORS PRIVILEGE INPUTS-->
                <!--FORM INPUTS-->
                <div class="row">

                    <!--OPERATOR-->
                    <div class="mb-3 col-md-2">
                        <label class="form-label">Select Operator <span class="text-danger"> *</span></label>
                        <select class="form-control form-select" v-model="form.operator_id">
                            <option value="null">Select Operator</option>
                            <option v-for="Operator in Operators" :value="Operator.operator_id">
                                {{ Operator.operator_name }}
                            </option>
                        </select>
                        <div class="text-danger" v-if="errors.operator_id">{{ errors.operator_id }}</div>
                    </div>


                    <!--SJT-->
                    <div class="mb-3 col-md-2" id="sjt">
                        <label class="form-label">SJT</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" v-model="form.sjt" @change="toggleSjtSelection">
                            </div>
                        </div>
                    </div>

                    <!--RJT-->
                    <div class="mb-3 col-md-2" id="rjt">
                        <label class="form-label">RJT</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" v-model="form.rjt" @change="toggleRjtSelection">
                            </div>
                        </div>
                    </div>

                    <!--TP-->
                    <div class="mb-3 col-md-2" id="tp">
                        <label class="form-label">TP</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" v-model="form.tp" @change="toggleTpSelection">
                            </div>
                        </div>
                    </div>

                    <!--SV-->
                    <div class="mb-3 col-md-2" id="sjt">
                        <label class="form-label">SV</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" v-model="form.sv" @change="toggleSvSelection">
                            </div>
                        </div>
                    </div>

                    <!--GRANT ALL API-->
                    <div class="mb-3 col-md-2" id="grant_all_api">
                        <label class="form-label">Grant All API</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" @change="toggleAllApiSelection" v-model="form.grant_all_api">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!--LIST OF ALL API TO PERMITTED -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="createOperatorPrivilege" class="table table-striped" style="width:100%">

                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>PRODUCT TYPE</th>
                                <th></th>
                            </tr>
                            </thead>
                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>API NAME</th>
                                <th>API DESCRIPTION</th>
                                <th>PRODUCT TYPE</th>
                                <th>SELECT</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="(api,ms_api_route_id) in apiList" :key="ms_api_route_id">
                                <td>{{ ms_api_route_id + 1 }}</td>
                                <td>{{ api.api_name.toUpperCase() }}</td>
                                <td>{{ api.api_description ? api.api_description.toUpperCase() : '' }}</td>
                                <td>{{ api.product_name.toUpperCase() }}</td>
                                <td>
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="api.ms_api_route_id"
                                        v-model="form.selected"
                                        @change="checkGrantAllApi"
                                    >
                                </td>
                            </tr>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!--BUTTONS-->
        <div class="row mb-2 mb-xl-3">

            <!--BACK BUTTON-->
            <div class="col-auto d-none d-sm-block">
                <Link :href="'/operators/privilege'" class="btn btn-outline-primary">
                    <font-awesome-icon icon="fa-solid fa-backward"/>
                    Back
                </Link>
            </div>

            <!--SAVE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                    <font-awesome-icon icon="fa-solid fa-save"/>
                    Save
                </button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-light">
                        <div class="modal-body m-3 text-center">
                            <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                            <h3 class="m-2">
                                <span>Are you sure! do you want to create New Operator Privilege ?</span></h3>
                            <a data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                            <button type="submit" class="btn btn-primary m-2 btn-lg" data-bs-dismiss="modal">
                                YES
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

</template>
<script>
import Layout from "../Base/Layout.vue";
import {Link, useForm} from "@inertiajs/inertia-vue3";

export default {
    name: 'Create',
    layout: Layout,
    components: {
        Link
    },
    props: {
        Operators: Array,
        apiList: Array,
        errors: Object
    },
    mounted() {

        $("#createOperatorPrivilege").DataTable({
            responsive: true,
            "paging": true,
            "ordering": false,
            scrollY: 400,
            deferRender: true,
            scroller: true,
            columnDefs: [
                { className: "text-justify", targets: [] },
                { width: "10%", targets: 0 }, // S.NO
                { width: "20%", targets: 1 }, // API NAME
                { width: "40%", targets: 2 }, // PRODUCT TYPE
                { width: "20%", targets: 3 }, // OPERATOR NAME
                { width: "10%", targets: 4 }, // ACTION
            ],
            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#createOperatorPrivilege th").eq([d]).text(); /*USE THIS TO SPECIFY TABLE NAME*/
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
            },

        });
    },

    data() {
        return {
            form: useForm({
                selected: [],
                grant_all_api: false,
                isSelected: false,
                operator_id: null,
                sjt: null,
                rjt: null,
                tp: null,
                sv: null,
            })
        }
    },

    methods: {
        /*SELECT ALL API IN ONE ATTEMPT*/
        toggleAllApiSelection() {
            if (this.form.grant_all_api) {
                this.form.selected = this.apiList.map(api => api.ms_api_route_id);
                this.form.sjt = true;
                this.form.rjt = true;
                this.form.tp = true;
                this.form.sv = true;
            } else {
                this.form.selected = [];
                this.form.sjt = false;
                this.form.rjt = false;
                this.form.tp = false;
                this.form.sv = false;
            }
        },

        toggleSjtSelection() {
            if (this.form.sjt) {
                /*ON SJT CHECKBOX SELECT , SELECT ALL SJT API IN TABLE LIST*/
                this.apiList.forEach(api => {
                    if (api.product_name.toUpperCase() === 'SJT' && !this.form.selected.includes(api.ms_api_route_id)) {
                        this.form.selected.push(api.ms_api_route_id);
                    }
                });
            } else {
                /* DESELECT ALL SJT API IN TABLE LIST*/
                this.form.selected = this.form.selected.filter(apiId => {
                    return this.apiList.find(api => api.ms_api_route_id === apiId).product_name.toUpperCase() !== 'SJT';
                });
                this.checkGrantAllApi();

            }
        },

        toggleRjtSelection() {
            if (this.form.rjt) {
                /*ON RJT CHECKBOX SELECT , SELECT ALL RJT API IN TABLE LIST*/
                this.apiList.forEach(api => {
                    if (api.product_name.toUpperCase() === 'RJT' && !this.form.selected.includes(api.ms_api_route_id)) {
                        this.form.selected.push(api.ms_api_route_id);
                    }
                });
            } else {
                /* DESELECT ALL RJT API IN TABLE LIST*/
                this.form.selected = this.form.selected.filter(apiId => {
                    return this.apiList.find(api => api.ms_api_route_id === apiId).product_name.toUpperCase() !== 'RJT';
                });
                this.checkGrantAllApi();
            }
        },

        toggleTpSelection() {
            if (this.form.tp) {
                /*ON TP CHECKBOX SELECT , SELECT ALL TP API IN TABLE LIST*/
                this.apiList.forEach(api => {
                    if (api.product_name.toUpperCase() === 'TP' && !this.form.selected.includes(api.ms_api_route_id)) {
                        this.form.selected.push(api.ms_api_route_id);
                    }
                });
            } else {
                /* DESELECT ALL TP API IN TABLE LIST*/
                this.form.selected = this.form.selected.filter(apiId => {
                    return this.apiList.find(api => api.ms_api_route_id === apiId).product_name.toUpperCase() !== 'TP';
                });
                this.checkGrantAllApi();
            }
        },

        toggleSvSelection() {
            if (this.form.sv) {
                /*ON SV CHECKBOX SELECT , SELECT ALL TP API IN TABLE LIST*/
                this.apiList.forEach(api => {
                    if (api.product_name.toUpperCase() === 'SV' && !this.form.selected.includes(api.ms_api_route_id)) {
                        this.form.selected.push(api.ms_api_route_id);
                    }
                });
            } else {
                /* DESELECT ALL SV API IN TABLE LIST*/
                this.form.selected = this.form.selected.filter(apiId => {
                    return this.apiList.find(api => api.ms_api_route_id === apiId).product_name.toUpperCase() !== 'SV';
                });
                this.checkGrantAllApi();
            }
        },

        checkGrantAllApi() {
            this.form.grant_all_api = this.form.selected.length === this.apiList.length;
        },

        storeOperatorsPrivilege() {
            this.form.post('/operators/privilege', this.form);
        }
    }
}

</script>
