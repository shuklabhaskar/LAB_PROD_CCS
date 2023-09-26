<template>

    <div>

        <h1> <strong>LAB PRODUCTION CCS </strong> DASHBOARD</h1>

        <div class="row mt-5">
            <div class="col-xl-6 col-xxl-5 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Total No of Stations</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-train" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{Stations}}</h1>
                                    <div class="mb-0">
                                        <span class="badge badge-primary-light"> <i class="mdi mdi-arrow-bottom-right"></i> {{Stations-Station_inactive}} </span>
                                        <span class="text-muted">Stations are in working</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Total No of Equipments</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fa fa-toolbox" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{Equipments}}</h1>
                                    <div class="mb-0">
                                        <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i> {{Equipments-Equipments_inactive}} </span>
                                        <span class="text-muted">Equipments are in working</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>


</template>

<script>

    import Layout from "./Base/Layout";

    export default {
        name: "Dashboard",
        layout:Layout,
        props:{
            Equipments:Array,
            Stations:Array,
            Station_inactive:Array,
            Equipments_inactive:Array,
        },
    }
</script>

