<template>

        <div class="m-auto" v-if="message && showNotification">
            <div class="bg-success rounded-lg border-gray-300 border p-3 shadow-lg">
                <div class="flex flex-row">
                    <div class="px-2">
                        <svg width="24" height="24" viewBox="0 0 1792 1792" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1299 813l-422 422q-19 19-45 19t-45-19l-294-294q-19-19-19-45t19-45l102-102q19-19 45-19t45 19l147 147 275-275q19-19 45-19t45 19l102 102q19 19 19 45t-19 45zm141 83q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/>
                        </svg>&nbsp;
                        <span  class="font-semibold text-white">{{ message}}</span>

                    </div>
                    <!--<div class="ml-2 mr-6">
                        <span class="font-semibold">Successfully Created</span>
                    </div>-->
                </div>
            </div>
        </div>

    <!--<div class="m-auto" v-if="message && showNotification">
        <div class="alert alert-success alert-dismissible mt-4" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="alert-icon">
                <i class="far fa-fw fa-bell"></i>
            </div>
            <div class="alert-message">
                <strong>{{ message }}</strong>
            </div>
        </div>
    </div>-->


</template>

<script setup>

     import { defineProps, ref, onMounted} from 'vue';
     const props = defineProps ({
         message: String
     })

    const showNotification = ref(false)

     window.setTimeout(function() {
         $(".flash").fadeTo(500, 0).slideUp(500, function(){
             $(this).remove();
         });
     }, 5000);

    onMounted( () => {
        showNotification.value = true /* MAKE IT TRUE FOR RUNNABLE*/
        setTimeout(()=>(showNotification.value = false), 4000);
    } );

</script>

<style scoped>



</style>
