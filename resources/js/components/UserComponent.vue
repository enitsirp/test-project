<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10 px-4">
                <div class="card">
                    <div class="card-header">User
                      <b-button v-b-modal.user-modal size="sm" variant="primary" v-bind:style="{float:'right'}">Add new user</b-button>
                    </div>

                    <div class="card-body">
                        <b-alert v-model="formStatus" v-bind:variant="formStatus ? 'success' : 'danger'" dismissible>
                            {{ successMessage }}
                        </b-alert>

                        <b-table id="user-table"  striped responsive="sm" :fields="tableFields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" sort-icon-left :items="users" :per-page="perPage" :current-page="currentPage">
                            <template v-slot:cell(is_active)="row">
                                <b-badge variant="primary" v-if="row.item.is_active">Active</b-badge>
                                <b-badge variant="danger" v-else>Suspended</b-badge>
                            </template>
                            <template v-slot:cell(is_admin)="row">
                                <b-badge variant="primary" v-if="row.item.is_admin">Admin</b-badge>
                                <b-badge variant="danger" v-else></b-badge>
                            </template>
                            <template v-slot:cell(actions)="row">
                                <b-button size="sm" class="mr-2" variant="info" pill @click="showModal(row.item)">Edit</b-button>
                                <b-button size="sm" class="mr-2" variant="warning" pill @click="removeNote(row.item.id)">Delete</b-button>
                            </template>
                        </b-table>

                    </div>
                    <div class="card-footer text-muted">
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="rows"
                            :per-page="perPage"
                            aria-controls="user-table"
                            ></b-pagination>
                    </div>
                </div>
            </div>
        </div>
        

        <b-modal id="user-modal"  ref="modal" title="User Form"
            @show="resetModal"        
            @ok="handleOk"
            >
            <form ref="user-form" @submit.stop.prevent="handleSubmit">
                <b-form-group :state="firstNameState" label="Firstname" label-for="first-name-input"  invalid-feedback="Firstname is required">
                    <b-form-input id="first-name-input" v-model="firstName" :state="firstNameState" required></b-form-input>
                </b-form-group>
                <b-form-group :state="lastNameState" label="Lastname" label-for="last-name-input"  invalid-feedback="Lastname is required">
                    <b-form-input id="last-name-input" v-model="lastName" :state="lastNameState" required></b-form-input>
                </b-form-group>
                <b-form-group :state="emailState" label="Email" label-for="email-input"  invalid-feedback="Email is required and must be valid">
                    <b-form-input id="email-input" v-model="email" type="email" :state="emailState" required></b-form-input>
                </b-form-group>
                <b-form-group :state="passwordState" label="Password" label-for="password-input"  invalid-feedback="Password is required and must be at least 8 characters.">
                    <b-form-input v-bind:disabled="formState == 'Edit'" id="password-input" v-model="password" type="password" :state="passwordState" required></b-form-input>
                </b-form-group>
               <b-form-group :state="confirmPasswordState" label="Confirm Password" label-for="confirm-password-input"  invalid-feedback="The password confirmation does not match.">
                    <b-form-input v-bind:disabled="formState == 'Edit'" id="confirm-password-input" v-model="confirmPassword" type="password" :state="confirmPasswordState" required></b-form-input>
                </b-form-group>
                <b-form-group label="User status">
                    <b-form-radio v-model="isActive" name="is-active" value="1">Active</b-form-radio>
                    <b-form-radio v-model="isActive" name="is-active" value="0">Suspended</b-form-radio>
                </b-form-group>
                <b-form-group label="User Role">
                    <b-form-radio v-model="isAdmin" name="is-admin" value="1">Admin Role</b-form-radio>
                    <b-form-radio v-model="isAdmin" name="is-admin" value="0">User Role</b-form-radio>
                </b-form-group>
            </form>
        </b-modal>

    </div>

    
</template>

<script>
    export default {
        data() {
            return {
                firstName: '',
                firstNameState: null,
                lastName: '',
                lastNameState: null,
                email: '',
                emailState: null,
                password: '',
                confirmPassword: '',
                confirmPasswordState: null,
                passwordState: null,
                isAdmin:"0",
                isActive:"1",
                formStatus: false,
                successMessage: "",
                formState: "Add",
                
                users: [],
                perPage: 10,
                currentPage: 1,
                tableFields: [
                        { key: 'first_name', sortable: true },
                        { key: 'last_name', sortable: true },
                        { key: 'email', sortable: true },
                        { key: 'is_active', sortable: true },
                        { key: 'is_admin', sortable: false },
                        { key: 'actions', sortable: false },
                ],
                sortBy: 'first_name',
                sortDesc: false,
                userId: null,
            }
        },
        methods: {
            checkFormValidity() {
                
                const valid = this.$refs['user-form'].checkValidity()

                this.firstNameState = this.firstName ? true : false;
                this.lastNameState = this.lastName ? true : false;
                this.emailState = this.email ? true : false;
                this.passwordState = this.password ? true : false;
                this.confirmPasswordState = this.confirmPasswordState ? true : false;
                return valid;
            },
            resetModal() {
                this.firstName = '';
                this.firstNameState = null;
                this.lastName = '';
                this.lastNameState = null;
                this.email = '';
                this.emailState = null;
                this.password = '';
                this.passwordState = null;
                this.confirmPassword = '';
                this.confirmPasswordState = null;
                this.isAdmin = "0";
                this.isActive = "1";
                this.formStatus = false;
                this.successMessage = [];
                this.userId = null;
                this.formState = "Add";
            },
             handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault()
                // Trigger submit handler
                this.handleSubmit()
            },
            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                return
                }

                if (this.formState == 'Add') {
                    axios.post('/users', {
                        first_name: this.firstName,
                        last_name: this.lastName,
                        email: this.email,
                        password:this.password,
                        is_admin: this.isAdmin,
                        is_active: this.isActive
                    })
                    .then((response) => {
                        this.formStatus = true;
                        this.successMessage= 'Successfully added new user : '+ this.firstName + ' ' + this.lastName;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }else{

                    axios({
                        method: 'put',
                        url: '/users/'+this.userId,
                        data: {
                            first_name: this.firstName,
                            last_name: this.lastName,
                            email: this.email,
                            is_admin: this.isAdmin,
                            is_active: this.isActive
                        }
                    })
                    .then((response) => {
                        this.formStatus = true;
                        this.successMessage= 'Successfully updated User : '+ this.firstName + ' ' + this.lastName;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
                

                // Hide the modal manually
                this.$nextTick(() => {
                    this.$refs.modal.hide()
                    this.fetchUsers();
                })
            },

            fetchUsers(){
                axios({
                    method: 'get',
                    url: '/users',
                    })
                    .then((response) => {
                        this.users = response.data;
                        
                    });
            },

            showModal(user) {
                this.$refs['modal'].show()
                this.firstName = user.first_name;
                this.lastName = user.last_name;
                this.email = user.email;
                this.isAdmin = user.is_admin ? "1" : "0";
                this.isActive =  user.is_active ? "1" : "0";
                this.userId = user.id;
                this.formState = "Edit";

            },

            removeNote(id){

                this.$bvModal.msgBoxConfirm('Are you sure you want to delete this user?')
                .then(value => {
                    if(value == true){
                        axios({
                            method: 'delete',
                            url: '/users/'+id,
                        })
                        .then((response) => {
                            this.formStatus = true;
                            this.successMessage= 'Successfully deleted user';
                        });

                        this.fetchUsers(); 
                    }
                })
                .catch(error => {
                   console.log(error);
                   
                })
            }
        },
         mounted() {
             this.fetchUsers();
         },
        computed: {
            rows() {
                return this.users.length
            }
        }
    }
</script>
