<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10 px-4">
                <div class="card">
                    <div class="card-header">Notes
                      <b-button v-b-modal.note-modal size="sm" variant="primary" v-bind:style="{float:'right'}">Add new note</b-button>

                    </div>

                    <div class="card-body">
                        <b-alert v-model="formStatus" v-bind:variant="formStatus ? 'success' : 'danger'" dismissible>
                            {{ successMessage }}
                        </b-alert>

                        <b-table id="notes-table"  striped responsive="sm" :fields="tableFields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" sort-icon-left :items="notes" :per-page="perPage" :current-page="currentPage">
                            <template v-slot:cell(actions)="row">
                                
                                <b-button size="sm" class="mr-2" variant="info" pill @click="showModal(row.item.title, row.item.note_details , row.item.id)">Edit</b-button>
                                <b-button size="sm" class="mr-2" variant="warning" pill @click="removeNote(row.item.id)">Delete</b-button>
                            </template>
                        </b-table>

                    </div>
                    <div class="card-footer text-muted">
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="rows"
                            :per-page="perPage"
                            aria-controls="notes-table"
                            ></b-pagination>
                    </div>
                </div>
            </div>
        </div>
        

        <b-modal id="note-modal"  ref="modal" title="Note Form"
            @show="resetModal"        
            @ok="handleOk"
            >
            <form ref="note-form" @submit.stop.prevent="handleSubmit">
                <b-form-group :state="titleState" label="Title" label-for="title-input"  invalid-feedback="Title is required">
                    <b-form-input id="title-input" v-model="title" :state="titleState" required></b-form-input>
                </b-form-group>
                <b-form-group :state="detailState" label="Detail" label-for="detail-input" invalid-feedback="Note Detail is required">
                    <b-form-textarea id="detail-input" v-model="detail" placeholder="Enter your note details..." :state="detailState" required></b-form-textarea>
                </b-form-group>
            </form>
        </b-modal>

    </div>

    
</template>

<script>
    export default {
        data() {
            return {
                title: '',
                titleState: null,
                detail: '',
                detailState: null,
                formStatus: false,
                successMessage: "",
                formState: "Add",
                
                notes: [],
                perPage: 10,
                currentPage: 1,
                tableFields: [
                        { key: 'title', sortable: true },
                        { key: 'note_details', sortable: true },
                        { key: 'actions', sortable: false },
                ],
                sortBy: 'title',
                sortDesc: false,
                noteId: null,
            }
        },
        methods: {
            checkFormValidity() {
                
                const valid = this.$refs['note-form'].checkValidity()

                this.titleState = this.title ? true : false;
                this.detailState = this.detail ? true : false;
    
                return valid;
            },
            resetModal() {
                this.title = ''
                this.titleState = null
                this.detail = ''
                this.detailState = null
                this.formStatus = false
                this.successMessage = []
                this.noteId = null;
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
                    axios.post('/notes', {
                        title: this.title,
                        note_details: this.detail
                    })
                    .then((response) => {
                        this.formStatus = true;
                        this.successMessage= 'Successfully added new note :'+ this.title;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }else{

                    axios({
                        method: 'put',
                        url: '/notes/'+this.noteId,
                        data: {
                            title: this.title,
                            note_details: this.detail
                        }
                    })
                    .then((response) => {
                        this.formStatus = true;
                        this.successMessage= 'Successfully updated note :'+ this.title;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
                

                // Hide the modal manually
                this.$nextTick(() => {
                    this.$refs.modal.hide()
                    this.fetchNotes();
                })
            },

            fetchNotes(){
                axios({
                    method: 'get',
                    url: '/notes',
                    })
                    .then((response) => {
                        this.notes = response.data;
                        
                    });
            },

            showModal(title, details, id) {
                this.$refs['modal'].show()
                this.title = title;
                this.detail = details;
                this.noteId = id;
                this.formState = "Edit";

            },

            removeNote(id){

                this.$bvModal.msgBoxConfirm('Are you sure you want to delete this note?')
                .then(value => {
                    if(value == true){
                        axios({
                            method: 'delete',
                            url: '/notes/'+id,
                        })
                        .then((response) => {
                            this.formStatus = true;
                            this.successMessage= 'Successfully deleted note';
                        });

                        this.fetchNotes(); 
                    }
                })
                .catch(error => {
                   console.log(error);
                   
                })
            }
        },
         mounted() {
             this.fetchNotes();
         },
        computed: {
            rows() {
                return this.notes.length
            }
        }
    }
</script>
