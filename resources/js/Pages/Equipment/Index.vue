<template>
    <div class="container-fluid p-0">

        <!--CARD WITH HEADING & BUTTON-->
        <div class="row mb-3 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>EQUIPMENTS</strong> INVENTORY</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <a :href="'/equipments/create'" class="btn btn-outline-primary">
                    <font-awesome-icon icon="fa-solid fa-plus"/>
                    Create New Equipment</a>
            </div>

        </div>

        <!--CARD WITH TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!--TABLE DATA-->
                        <table id="example" class="table table-striped" style="width:100%">
                            <!--FOR SORTING-->
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>TYPE</th>
                                <th>STATION / SCS</th>
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
                                <th>STATION / SCS</th>
                                <th>IP</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="(Equipment,id) in Equipments" :key="id">
                                <td>{{id+1}}</td>
                                <td>{{Equipment.eq_id}}</td>
                                <td>{{Equipment.eq_type_name}}</td>
                                <td>{{Equipment.stn_name.toUpperCase()}}</td>
                                <td>{{Equipment.ip_address}}</td>
                                <td v-if="Equipment.status === true">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>
                                    <Link type="button" :href="'/equipments/edit/' + Equipment.eq_inv_id"
                                          class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip"
                                          data-bs-placement="top" title="Edit">
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

    /*IMPORTS*/
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        props: {
            Equipments: Array
        },
        name: "Index",
        layout: Layout,
        components: {
            Link
        },


        mounted() {
            $("#example").DataTable({
                responsive: true,
                "paging": true,
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
                                ``
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
        }

    }

</script>
