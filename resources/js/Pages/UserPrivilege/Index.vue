<template>
    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>USER</strong> PRIVILEGE</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'userPrivilege/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New User Privilege</Link>
            </div>

        </div>

        <!--CARD AND TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>USER PRIVILEGE NAME</th>
                                <th>DESCRIPTION</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(userPrivilege,user_pri_id) in userPrivileges" :key="user_pri_id">
                                <td>{{user_pri_id+1}}</td>
                                <td>{{userPrivilege.user_pri_name}}</td>
                                <td>{{userPrivilege.description}}</td>
                                <td>
                                    <Link type="button" :href="'/userPrivilege/edit/'+userPrivilege.user_pri_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <font-awesome-icon icon="fa-solid fa-edit" />
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
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        name: "index",
        layout:Layout,
        components:{
            Link
        },
        props:{
            userPrivileges:Array
        },
        mounted() {

            $("#example").DataTable({
                responsive  : true,
                "paging"    : true,
            });
        }
    }
</script>

<style scoped>

</style>
