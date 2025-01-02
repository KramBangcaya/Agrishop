<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Deliquency</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-3">
                                <h3 class="card-title"> </h3>
                                <div class="card-tools float-left">
                                    <div class="input-group input-group-sm">
                                        <select v-model="length" @change="getData" class="form-control form-control-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm">
                                        <input v-model="search" type="text" @keyup="getData" name="table_search"
                                            class="form-control float-right" placeholder="Search" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" @click="getData">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-head-fixed text-nowrap text-center">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Proof</th>
                                            <th style="width: 10%;">Reported Name</th>
                                            <th style="width: 30%;">Reason</th>
                                            <th style="width: 10%;">Reported By</th>
                                            <th style="width: 30%;">Reply</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in         option_users.data        " :key="index">
                                            <td>
                                                    <img
                                                    v-if="data.proof && data.proof.length"
                                                    :src="'/storage/'+formatPhotoPath(data.proof)"
                                                    alt="Product Photo"
                                                    style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                                    @click="openImageModal('/storage/' + formatPhotoPath(data.proof))"
                                                />

                                            </td>
                                            <td>{{ data.buyer_name }}</td>
                                            <td>{{ data.reason }}</td>
                                            <td>{{ data.user_name }}</td>
                                            <td>{{ data.reply }}</td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    @click="openEditModal(data)"><i class="fas fa-edit"></i>
                                                    Response</button>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                                <ul class="pagination pagination-sm m-1 float-right">
                                    <li class="page-item" v-for="(link, index) in option_users.links" :key="index">
                                        <button
                                            v-html="link.label"
                                            @click="getData(link.url)"
                                            class="page-link"
                                            :disabled="link.url == null || link.active"
                                            :class="{ 'text-muted': link.url == null || link.active }">
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- declare the edit modal -->
                    <edit-modal @getData="getData" :row="selected_user" :page="current_page"></edit-modal>

                </div>
            </div>

        </div>
        <div v-if="showImageModal" class="modal-overlay" @click="closeImageModal">
                        <div class="modal-content">
                            <img :src="zoomedImage" alt="Zoomed Image" />
                        </div>
                    </div>
    </div>

</template>
<script>
import EditModal from "./Create.vue";
export default {
    components: {
        EditModal,
    },
    data() {
        return {
            option_users: [],
            length: 10,
            search: '',
            filter: 'All',
            showSchedule: false,
            is_searching: true,
            selected_user: [],
            current_page: [],
            form: new Form({
                id: '',
            }),
            error: '',
            showImageModal: false,
            zoomedImage: '',
        }
    },
    methods: {
        openImageModal(imageSrc) {
            this.zoomedImage = imageSrc;
            this.showImageModal = true;
        },
        closeImageModal() {
            this.showImageModal = false;
        },
        formatPhotoPath(photoPath) {
            if (photoPath) {
                return photoPath.replace(/^\["(.+)"\]$/, '$1');
            } else {
                return '';
            }
        },
        openEditModal(data) {
            this.selected_user = data;
            $('#response').modal('show');
        },
        getData(page) {
            if (typeof page === 'undefined' || page.type == 'keyup' || page.type == 'change' || page.type == 'click') {
                page = '/api/deliquency/list/?page=1';
            }
            this.current_page = page;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                axios.get(page, {
                    params: {
                        search: this.search,
                        length: this.length,
                        filter: this.filter,
                        time_start: this.time_start,
                        time_end: this.time_end,
                        day: this.day,
                        section_id: this.section_id,
                    },
                })
                    .then(response => {
                        if (response.data.data) {
                            this.option_users = response.data.data;
                            console.log(this.option_users);
                        }
                    }).catch(error => {
                        this.error = error;
                        toast.fire({
                            icon: 'error',
                            text: error.response.data.message,
                        })
                    });
            }, 500);
        },
        remove(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Deactivate this account!',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/user/delete/' + id)
                        .then(response => {
                            Swal.fire(
                                'Disable!',
                                'Your file has been Deactivated.',
                                'success'
                            )
                            this.getData();
                        })
                }
            }).catch(() => {
                toast.fire({
                    icon: 'error',
                    text: 'Something went wrong!',
                })
            });
        },
    },
    created() {
        this.getData();
    },
}
</script>
<style>
.modal-overlay {
    position: fixed;
    top: 50%; /* Center vertically */
    left: 60%; /* Center horizontally */
    transform: translate(-50%, -50%); /* Center horizontally */
    width: 70%;
    height: 70%;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 500;
}


.modal-content {
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content img {
    max-width: 50%;
    max-height: 50%;
    border: 2px solid white;
    border-radius: 10px;
}
</style>
