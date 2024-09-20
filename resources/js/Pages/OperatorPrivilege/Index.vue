<template>
    <div class="container-fluid p-0">

        <!--HEADING-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>OPERATORS</strong> PRIVILEGE</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'/operator/privilege/create'" class="btn btn-outline-primary">
                    <font-awesome-icon icon="fa-solid fa-plus"/>
                    Create New Operator Privilege
                </Link>
            </div>

        </div>

        <!--CARD WITH FORM AND BUTTONS-->
        <!--API LIST TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="operatorPrivilegeList" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>OPERATOR NAME</th>
                                <th></th>
                            </tr>
                            </thead>
                            <thead>
                            <tr>
                                <th >S.NO</th>
                                <th >PRODUCT TYPE</th>
                                <th >API NAME</th>
                                <th >OPERATOR NAME</th>
                                <th >ACTION</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="(operatorsPrivilege,operator_id) in operatorsPrivilegeList" :key="operator_id">

                                <td>{{operator_id + 1}}</td>
                                <td>{{ operatorsPrivilege.product_name.toUpperCase()}}</td>
                                <td>{{ operatorsPrivilege.api_name.toUpperCase() }}</td>
                                <td>{{ operatorsPrivilege.operator_name.toUpperCase() }}</td>
                                <td>
                                    <Link type="button"
                                          :href="'/operator/privilege/edit/' + operatorsPrivilege.operators_api_prv_id"
                                          class="btn btn-sm btn-icon btn-primary rounded" title="Edit">
                                        <font-awesome-icon icon="fa-solid fa-edit"/>
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
import Layout from "../Base/Layout.vue";
import {Link} from "@inertiajs/inertia-vue3";

export default {
    name: "Index",
    components: {Link},
    layout: Layout,
    props: {
        operatorsPrivilegeList: Array
    },
    mounted() {

        $("#operatorPrivilegeList").DataTable({
            responsive: true,
            "paging": true,
            "ordering": false,
            scrollY: 500,
            deferRender: true,
            scroller: true,
            columnDefs: [
                { className: "text-justify", targets: [0] },
                { width: "10%", targets: 0 }, // S.NO
                { width: "20%", targets: 1 }, // API NAME
                { width: "30%", targets: 2 }, // PRODUCT TYPE
                { width: "20%", targets: 3 }, // OPERATOR NAME
                { width: "20%", targets: 4 }, // ACTION
            ],
            autoWidth: false,
            initComplete: function () {
                this.api().columns([3]).every(function (d) {
                    var column = this;
                    var theadname = $("#operatorPrivilegeList th").eq([d]).text(); /*USE THIS TO SPECIFY TABLE NAME*/
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

    }

}

</script>
