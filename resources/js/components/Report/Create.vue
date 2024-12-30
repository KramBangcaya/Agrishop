<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">File Report</div>

                    <div class="card-body">
                        <alert-error :form="form"></alert-error>
                        <form method="POST">
                            <input type="hidden" required>
                            <input type="hidden" v-model="form.userID" class="form-control"
                                autocomplete="positionchrome-off">

                            <div class="form-group">
                                <label>Reason</label>
                                <input v-model="form.reason" type="text" class="form-control">
                                <has-error :form="form" field="Product Name" />
                            </div>

                            <div class="form-group required">
                                <label>Reported Name</label>
                                <multiselect v-model="form.reported_name" placeholder="Search Name" label="fullName"
                                    track-by="id" :options="option_users" :close-on-select="true"
                                    :clear-on-select="false"></multiselect>
                                <has-error :form="form" field="user_id" />
                            </div>

                            <div class="form-group">
                                <label>Upload Proof</label>
                                <input type="file" @change="onFileChange" multiple class="form-control">
                            </div>


                            <button type="button" class="btn btn-primary" @click="create">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
    components: {
        Multiselect
    },
    data() {
        return {
            form: new Form({
                userID: 0,
                reason: '',
                reported_name: '',
                user_id: null,
            }),
            option_users: [],
            proof: [],
        }
    },
    methods: {
        onFileChange(e) {
            this.proof = Array.from(e.target.files);
        },
        create() {
            console.log('Photos before posting:', this.photos);
            const formData = new FormData();
            formData.append('reason', this.form.reason);
            formData.append('reported_name', this.form.reported_name.fullName);


            // Append each selected photo file to the formData
            this.proof.forEach((photo, index) => {
                formData.append(`proof[${index}]`, photo);
            });



            axios.post('/api/Report/create', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(() => {
                toast.fire({
                    icon: 'success',
                    text: 'Data Saved.',
                })
                this.form.reset();
                this.photos = []; // Reset the photos array
            }).catch(() => {
                toast.fire({
                    icon: 'error',
                    text: 'Something went wrong!',
                })
            });
        },
        loadUsers() {
    axios.get('/api/user/all')
        .then(response => {
            this.option_users = response.data.data.map(user => ({
                id: user.user_id,
                fullName: `${user.user_name} ${user.user_lastname} - ${user.role_name}`,
                role: user.role_name // Use role_name for display
            }));
            console.log('Loaded Users with Roles:', this.option_users);
        })
        .catch(error => {
            console.error('Error loading users:', error);
        });
}
    },
    watch: {
        row: function () {
            this.form.fill(this.row);
            console.log('Form data filled from row:', this.form);
        }
    },
    mounted() {
        this.loadUsers();
    }
}
</script>
