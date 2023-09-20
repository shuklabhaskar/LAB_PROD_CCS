<template>
    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>USER</strong> ROLES</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'userRole/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New User Role</Link>
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
                                <th>USER ROLE NAME</th>
                                <th>DESCRIPTION</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(userRole,user_role_id) in userRoles" :key="user_role_id">
                                <td>{{user_role_id+1}}</td>
                                <td>{{userRole.user_role_name}}</td>
                                <td>{{userRole.description}}</td>
                                <td>
                                    <Link type="button" :href="'/userRole/edit/'+userRole.user_role_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
        name: "Index",
        layout:Layout,
        components:{
            Link
        },
        props:{
            userRoles : Array,
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
