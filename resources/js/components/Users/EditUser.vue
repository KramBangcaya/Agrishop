<template>
    <div class="modal fade" id="edit-user">
        <div class="modal-dialog model-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <alert-error :form="form"></alert-error>
                    <div class="form-group">
                        <label>Upload User photo</label>
                        <input type="file" @change="onFileChange" multiple class="form-control">
                        <!-- <has-error :form="form" field="user_photo" /> -->
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" class="form-control">
                        <has-error :form="form" field="name" />
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input v-model="form.lastname" type="text" class="form-control">
                        <has-error :form="form" field="lastname" />
                    </div>
                    <div class="form-group">
                        <label>Middle Initial</label>
                        <input v-model="form.middle_initial" type="text" class="form-control">
                        <has-error :form="form" field="middle_initial" />
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input v-model="form.date_of_birth" type="date" class="form-control">
                        <has-error :form="form" field="date_of_birth" />
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input v-model="form.contact_number" type="number" class="form-control">
                        <has-error :form="form" field="contact_number" />
                    </div>
                    <div class="form-group">
                        <label>Telephone Number</label>
                        <input v-model="form.telephone_number" type="number" class="form-control">
                        <has-error :form="form" field="telephone_number" />
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input v-model="form.address" type="text" class="form-control">
                        <has-error :form="form" field="address" />
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input v-model="form.email" type="email" class="form-control">
                        <has-error :form="form" field="email" />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input v-model="form.password" type="password" class="form-control">
                        <has-error :form="form" field="password" />
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <div id="map" style="height: 400px;"></div>
                    </div>

                    <!-- Add Latitude and Longitude inputs -->
                    <div class="form-group">
                        <label>Latitude</label>
                        <input v-model="form.latitude" type="text" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input v-model="form.longitude" type="text" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Re-upload QR Code</label>
                        <input type="file" @change="onFileChange1" multiple class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Upload Support Documents</label>
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
        row: { required: true },
        page: { required: true },
    },
    data() {
        return {
            form: new Form({
                id: '',
                name: '',
                lastname: '',
                middle_initial: '',
                date_of_birth: '',
                contact_number: '',
                telephone_number: '',
                address: '',
                email: '',
                password: '',
                latitude: null,
                longitude: null,
            }),
            user_photo: [],
            qrcode: [],
            photos: [],
            options: {
                toolbar: true,
                url: 'data-source',
                toolbar: true,
                title: true
            },
        }
    },
    methods: {

        loadGoogleMapsScript() {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBa72Eer6ilUkPDSQn4ENOACV_oDYIpkOk&libraries=places`;
                script.async = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        },
        onFileChange(e) {
            this.user_photo = Array.from(e.target.files);
            console.log('User photo selected:', this.user_photo);
        },
        onFileChange1(e) {
            this.qrcode = Array.from(e.target.files);
        },
        onFileChange2(e) {
            this.photos = Array.from(e.target.files);
        },
        update() {
            console.log(this.form);
            console.log('Photos before posting:', this.user_photo);
            const formData = new FormData();
            formData.append('id', this.form.id);
            formData.append('name', this.form.name);
            formData.append('lastname', this.form.lastname);
            formData.append('middle_initial', this.form.middle_initial);
            formData.append('date_of_birth', this.form.date_of_birth);
            formData.append('contact_number', this.form.contact_number);
            formData.append('telephone_number', this.form.telephone_number);
            formData.append('address', this.form.address);
            formData.append('email', this.form.email);
            formData.append('latitude', this.form.latitude); // Use default if undefined
            formData.append('longitude', this.form.longitude); // Use default if undefined

            if (this.form.password && this.form.password.trim() !== '') {
                formData.append('password', this.form.password);
            }
            // Append each selected photo file to the formData
            this.user_photo.forEach((photo, index) => {
                formData.append(`user_photo[${index}]`, photo);
            });

            this.qrcode.forEach((photo, index) => {
                formData.append(`qrcode[${index}]`, photo);
            });

            this.photos.forEach((photo, index) => {
                formData.append(`photos[${index}]`, photo);
            });

            axios.post(`/api/user/update`, formData, {
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
                $('#edit-user').modal('hide');
            }).catch(error => {
                console.error('Error during submission:', error);
            });
        },
        initMap() {
            // Initialize Google Map
            this.map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 7.448212, lng: 125.809425 }, // Default position (latitude, longitude)
        zoom: 13,
    });

    // Create a marker and set it as null initially
    this.marker = new google.maps.Marker({
        map: this.map,
        position: this.map.getCenter(),  // Start at the map's center
        draggable: true,
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                // If successful, set the position based on geolocation
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                const userLocation = new google.maps.LatLng(userLat, userLng);
                this.map.setCenter(userLocation);  // Move the map center to the user's location
                this.marker.setPosition(userLocation);  // Move the marker to the user's location
                this.form.latitude = userLat;  // Update the form with latitude
                this.form.longitude = userLng; // Update the form with longitude
            },
            (error) => {
                console.error('Geolocation error:', error);
                // In case of error, default to the initial center location
                console.log('Could not retrieve your location, using default.');
            }
        );
    } else {
        console.log('Geolocation is not supported by this browser.');
    }

    // Handle map clicks to update latitude and longitude
    google.maps.event.addListener(this.map, 'click', (event) => {
        this.form.latitude = event.latLng.lat();  // Update latitude
        this.form.longitude = event.latLng.lng(); // Update longitude

        // Move the marker to the clicked position
        this.marker.setPosition(event.latLng);
    });

    // If you already have a saved location (latitude, longitude)
    if (this.form.latitude && this.form.longitude) {
        const latLng = new google.maps.LatLng(this.form.latitude, this.form.longitude);
        this.marker.setPosition(latLng);
        this.map.setCenter(latLng);
    }
}
    },

    watch: {
        row: function () {
            this.form.fill(this.row);
            // console.log(this.row);
        }
    },
    mounted() {
        this.loadGoogleMapsScript().then(() => {
            this.initMap();
        }).catch((error) => {
            console.error("Google Maps script failed to load", error);
        });

    }
}
</script>
<style scoped>
  /* Add the CSS for the map */
  #map {
    height: 400px;
    width: 100%;
  }
</style>
