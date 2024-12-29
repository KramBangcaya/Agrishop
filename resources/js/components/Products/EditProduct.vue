<template>
    <div class="modal fade" id="edit-user">
        <div class="modal-dialog model-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <alert-error :form="form"></alert-error>
                    <div class="form-group">
                        <label>Product Name</label>
                        <input v-model="form.Product_Name" type="text" class="form-control">
                        <has-error :form="form" field="Product Name" />
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input v-model="form.price" type="text" class="form-control">
                        <has-error :form="form" field="Price" />
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input v-model="form.Quantity" type="text" class="form-control">
                        <has-error :form="form" field="Quantity" />
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="form.Description" type="text" class="form-control"></textarea>
                        <has-error :form="form" field="Description" />
                    </div>
                    <div class="form-group">
                        <label>Measurement</label>
                        <multiselect v-model="form.measurement_id" :options="option_measurement"
                            :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                            placeholder="Measurement" label="measurement" track-by="id" :preselect-first="true">
                        </multiselect>
                        <has-error :form="form" field="measurement" />
                    </div>

                    <div class="form-group">
                        <label>Upload Photos</label>
                        <input type="file" @change="onFileChange" multiple class="form-control">
                        <br>
                        <input type="file" @change="onFileChange1" multiple class="form-control">
                        <br>
                        <input type="file" @change="onFileChange2" multiple class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="update">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        row: {required: true},
        page: {required: true},
    },
    data(){
        return{
            form: new Form({
                id:'',
                Product_Name:'',
                price: 0,
                Quantity: 0,
                Description:'',
                measurement_id:null,
            }),
            option_measurement:[],
            photos: [],
            photos1: [],
            photos2: [],
        }
    },
    methods: {
        onFileChange(e) {
            this.photos = Array.from(e.target.files);
        },
        onFileChange1(e) {
            this.photos1 = Array.from(e.target.files);
        },
        onFileChange2(e) {
            this.photos2 = Array.from(e.target.files);
        },
        update() {
            const formData = new FormData();

            // Append the form fields
            Object.keys(this.form).forEach((key) => {
        // Only append measurement_id if it is an object and include only the `id`
            if (key === 'measurement_id' && this.form.measurement_id) {
                formData.append(key, this.form.measurement_id.id); // Accessing 'id' of the measurement object
            } else {
                formData.append(key, this.form[key]);
            }
        });

            // Append the photos arrays
            this.photos.forEach((photo, index) => {
                formData.append(`photos[${index}]`, photo);
            });

            this.photos1.forEach((photo, index) => {
                formData.append(`photos1[${index}]`, photo);
            });

            this.photos2.forEach((photo, index) => {
                formData.append(`photos2[${index}]`, photo);
            });


            // Send the request
            axios.post(`api/product/update/${this.form.id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                .then(() => {
                    toast.fire({
                        icon: 'success',
                        text: 'Data Saved.',
                    });
                    // Emit to reload data in the parent component
                    this.$emit('getData', this.page);
                    $('#edit-user').modal('hide');
                })
                .catch((error) => {
                    toast.fire({
                        icon: 'error',
                        text: error.message,
                    });
                });
        },
        loadMeasurement() {
            axios.get('/api/measurement/all')
                .then(response => {
                    this.option_measurement = response.data.data;
                    console.log('Loaded measurements:', this.option_measurement);
                });
        },

    },
    watch: {
        row: function(){
            this.form.fill(this.row);
            console.log('Form data filled from row:', this.form);
        }
    },
    mounted() {
        this.loadMeasurement();
    }
}
</script>
