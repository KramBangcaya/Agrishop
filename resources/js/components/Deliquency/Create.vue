<template>
    <div class="modal fade" id="response">
        <div class="modal-dialog model-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <alert-error :form="form"></alert-error>
                    <div class="form-group">
                                <label>Response</label>
                                <textarea v-model="form.response" type="text" class="form-control"></textarea>
                                <has-error :form="form" field="response" />
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
        row: { required: true },
        page: { required: true },
    },
    data() {
        return {
            form: new Form({
                id: '',
                response: '',

            }),
            options: {
                toolbar: true,
                url: 'data-source',
                toolbar: true,
                title: true
            },
        }
    },
    methods: {
        update() {
            console.log(this.form);
            const formData = new FormData();
            formData.append('response', this.form.response);

            this.form.put(`/api/Report/response`, formData, {
                headers: {
                        'Content-Type': 'multipart/form-data',
                    },
            }).then(() => {
                toast.fire({
                    icon: 'success',
                    text: 'Data Saved.',
                });
                this.form.reset();
                this.$emit('getData', this.page);
                $('#response').modal('hide');
            }).catch(error => {
                console.error('Error during submission:', error);
            });
        },
    },

    watch: {
        row: function () {
            this.form.fill(this.row);
            // console.log(this.row);
        }
    },
    mounted() {
    }
}
</script>
