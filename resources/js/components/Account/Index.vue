<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">User Account</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row mb-2 justify-content-center">
                    <div class="card card-primary card-outline col-4">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                    :src="'/storage/' + formatPhotoPath(user.user_photo)"
                                    onerror="this.src='/images/default_image.png'" alt="User profile picture">
                            </div>
                            <h3 class="profile-username text-center">{{ user.name + " " + user.middle_initial + "." + " " + user.lastname }}</h3>
                            <p class="text-muted text-center">{{ user.roles[0].name }}</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Date of Birth</b> <b class="float-right">{{ user.date_of_birth }}</b>
                                </li>
                                <li class="list-group-item">
                                    <b>Contact Number</b> <b class="float-right">{{ user.contact_number }}</b>
                                </li>
                                <li class="list-group-item">
                                    <b>Address</b> <b class="float-right">{{ user.address}}</b>
                                </li>
                                <li class="list-group-item">
                                    <b>Email</b> <b class="float-right">{{ user.email }}</b>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-primary btn-sm" @click="openEditModal"
                                v-if="can('edit user')"><i class="fas fa-edit"></i> Edit Account</button>
                            <button type="button" class="btn btn-danger btn-sm float-right" @click="remove"
                                v-if="can('delete user')"><i class="fas fa-ban"></i> Disable Account</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- declare the edit modal -->
            <edit-modal :row="selected_user" :page="current_page"></edit-modal>
        </div>
    </div>
</template>
<script>
import EditModal from "../Users/EditUser.vue";
export default {
    components: {
        EditModal,
    },
    data() {
        return {
            user: [],
            length: 10,
            search: '',
            showSchedule: false,
            is_searching: true,
            selected_user: [],
            current_page: [],
            form: new Form({
                id: '',
            }),
            error: '',
        }
    },
    methods: {
        formatPhotoPath(photoPath) {
            if (photoPath) {
                return photoPath.replace(/^\["(.+)"\]$/, '$1');
            } else {
                return '';
            }
        },
        openEditModal() {
            this.selected_user = this.user;
            $('#edit-user').modal('show');
        },
        getUserData() {
            this.timer = setTimeout(() => {
                axios.get('/api/user/show/')
                    .then(response => {
                        if (response.data.data) {
                            this.user = response.data.data[0];
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
        remove() {
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
                    axios.delete('/api/user/delete/' + this.user.id)
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
        this.getUserData();
    },
}
</script>
