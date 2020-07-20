<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4 order-md-1 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Pool</span>
                    <span class="badge badge-secondary badge-pill">{{ raffle_names.length }}</span>
                </h4>
                <div>
                    <b-table striped hover :items="raffle_names" :fields="tableFields"></b-table>
                </div>
                <form ref="raffe-form" @submit.stop.prevent="handleSubmit"  class="card p-2" v-if="!poll_started" >
                    <b-input-group>
                        <b-form-input :state="validation" type="text" class="form-control" required  v-model="raffle_name" placeholder="Name"></b-form-input>
                        <b-input-group-append>
                            <b-button type="submit" class="btn btn-secondary">Save</b-button>
                        </b-input-group-append>
                        <b-form-invalid-feedback>
                        {{ validationMassage }}
                        </b-form-invalid-feedback>
                    </b-input-group>
                </form>
            </div>
            <div class="col-md-4 order-md-2">
                
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <b-button class="btn btn-primary btn-lg btn-block" :disabled="disabled" variant="primary" v-on:click="raffle" v-if="!disabled" >
                    <b-spinner v-if="loading"></b-spinner>
                    {{ raffle_button }}
                </b-button>

        
                <b-button class="btn btn-warning btn-lg btn-block" :disabled="!disabled" variant="warning" v-on:click="restart" v-if="disabled" >
                    Restart
                </b-button>
                <br>
            </div>
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Selected</span>
                    <span class="badge badge-secondary badge-pill">{{ selected.length }}</span>
                </h4>
                <div>
                    <b-table striped hover :items="selected" :fields="tableFields"></b-table>
                </div>
            </div>
        </div>
        <div> 
            <b-modal centered title="Start the Pool" ref="alert-modal" @ok="startPool">
                <p class="my-4">Are you sure you want to start the pool?</p>
            </b-modal>
        </div>
    </div>
    
</template>

<script>
    export default {
        data() {
            return {
                raffle_name: '',
                isSelected: false,
                successMessage: "",
                validationMassage: null,
                countName: 0,
                countSelected: 0,
                nameState: null,
                raffle_names:[],
                selected:[],
                tableFields: [
                        { key: 'raffle_name', sortable: true },
                ],
                sortBy: 'raffle_name',
                sortDesc: false,
                loading: false,
                raffle_button: 'Start',
                disabled: false,
                poll_started: false,
            }
        },
        methods: {
            checkFormValidity() {
                
                const valid = this.$refs['raffle-form'].checkValidity()

                this.nameState = this.raffle_name ? true : false;
                return valid;
            },

            resetForm() {
                this.raffle_name = ''
                this.nameState = null
            },

            handleSubmit() {
                // Exit when the form isn't valid

                axios.post('/raffle', {
                        raffle_name: this.raffle_name,
                    })
                    .then((response) => {
                        this.successMessage= 'Successfully added name :'+ this.raffle_name;
                        this.fetchNames();
                    })
                    .catch((err) => {
                        this.validationMassage = err.response.data.raffle_name[0];
                    });

            },

            fetchNames(){
                axios({
                    method: 'get',
                    url: '/list',
                    })
                    .then((response) => {
                        this.raffle_names = response.data;
                        this.countName = this.raffle_names.length;

                        if( this.raffle_names.length == 0){
                            this.disabled = true;
                            this.raffle_button = 'Finish';
                        }else{
                            this.disabled = false;
                             this.raffle_button = 'Start';
                        }
                    });                     
                    
            },
            fetchSelected(){
                axios({
                    method: 'get',
                    url: '/selected',
                    })
                    .then((response) => {
                        this.selected = response.data;
                        this.countSelected = this.selected.length;

                        if(this.selected.length > 0) {
                            this.poll_started = true;
                        }
                    });
            },
            raffle() {
                
                if(this.selected.length == 0){
                    this.$refs['alert-modal'].show()
                }else{
                    this.roll();
                }
            },
            startPool(){
                this.$refs['alert-modal'].hide();
                this.poll_started = true;
                //this.started = true;
                 this.roll();

            },
            roll(){
                let req = axios.post('/roll', {
                    raffle_name: this.raffle_name,
                });

                // start loadng
                this.loading = true;
                this.raffle_button = 'Selecting... '
                
                req.then((response) => {
   
                   setTimeout(() => {
                       this.loading = false;
                       this.raffle_button = 'Start';

                       // update table
                       this.fetchNames();
                       this.fetchSelected();


                   }, 1000);
                })
                .catch((err) => {
 
                    this.loading = false;
                    this.raffle_button = 'Start';
                });
            },
            restart(){
                axios({
                    method: 'get',
                    url: '/restart',
                    })
                    .then((response) => {
                        this.fetchNames();
                        this.fetchSelected();
                        this.poll_started = false;
                        this.disabled = false;
                    });
            },
        },
         mounted() {
             this.fetchNames();
             this.fetchSelected();
         },
         computed: {
            validation() {
                if(this.validationMassage != null){
                    return false;
                }

                return true
            }
        }
         
    }
</script>
