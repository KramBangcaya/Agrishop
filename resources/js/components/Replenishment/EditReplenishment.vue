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
export default {
    props: {
        row: { required: true },
        page: { required: true },
    },
    data() {
        return {
            form: new Form({
                id: '',
                Product_Name: '',
                price: 0,
                Quantity: 0,
                Description: '',
                measurement_id: null,
            }),
            option_measurement: [],
        }
    },
    methods: {
        // selectRole(){
        //     this.form.permissions = this.form.roles.permissions;
        // },
        update() {

            // const measurementIds = this.form.measurement_id.map(measurement => measurement.id);

            // this.form.measurement_id = measurementIds;
            this.form.post('api/replenishment/update/' + this.form.id).then(() => {
                toast.fire({
                    icon: 'success',
                    text: 'Data Saved.',
                })
                //"page" maintain selected page in the parent page
                this.$emit('getData', this.page);// call method from parent (reload data table)
                $('#edit-user').modal('hide');
            }).catch(error => {
                toast.fire({
                    icon: 'error',
                    text: error.message,
                })
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
        row: function () {
            this.form.fill(this.row);
            console.log('Form data filled from row:', this.form);
        }
    },
    mounted() {
        this.loadMeasurement();
    }
}
</script>
