<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Orders</h1>
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
                                <div class="card-tools">

                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Proof</th>
                                            <th>Buyer's Name</th>
                                            <th>Buyer Address</th>
                                            <th>Product Name</th>
                                            <th>Ordered Quantity</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Feedback</th>
                                            <th>Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(order, index) in orders" :key="index">
                                            <!-- <td>{{ order }}</td> -->
                                            <td>
                                                <img
                                                v-if="order.photo && order.photo.length"
                                                :src="'http://192.168.68.67:8080/buyer/'+order.photo"
                                                alt="Product Photo"
                                                style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                                @click="openImageModal('http://192.168.68.67:8080/buyer/' + order.photo)"
                                                />
                                            </td>
                                            <td>{{ order.buyer_name }}</td>
                                            <td>{{ order.buyer_address }}</td>
                                            <td>{{ order.product_name }}</td>
                                            <td>{{ order.product_quantity }}</td>
                                            <td>{{ order.totalPayment }}</td>
                                            <td>{{ order.order_status }}</td>
                                            <td>{{ order.feedback }}</td>
                                            <td>{{ order.rating }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
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

export default {
    data() {
        return {
            API_BASE: 'http://192.168.68.67:8080',
            orders: [],       // Holds fetched orders data
            search: '',       // Search input field
            userID: null,
            showImageModal: false,
            zoomedImage: '',
        };
    },
    created() {
        this.getUserID(); // Fetch data when component is created
    },
    methods: {
        openImageModal(imageSrc) {
        this.zoomedImage = imageSrc;
        this.showImageModal = true;
    },
    closeImageModal() {
        this.showImageModal = false;
    },
        async getUserID() {
        try {
            const response = await fetch('/user', { method: 'GET' }); // Call your Laravel route here
            const data = await response.json();
            if (data.userID) {
                this.userID = data.userID; // Store the userID
                this.getData(); // Now fetch orders after userID is fetched
            }
        } catch (error) {
            console.error('Error fetching userID:', error);
        }
    },
        // Fetch Orders from APIs
        async getData() {

            if (!this.userID) {
            console.error("UserID is missing");
            return;
        }
            try {
                const response = await fetch(this.API_BASE + `/buyer/delivered-orders.php?seller_id=${this.userID}`);
                const data = await response.json();
                if (data.status === 'success') {
                    this.orders = data.data;
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        },

        // Handle Cancel Order
        cancelOrder(id) {
            alert('Order with ID ${id} canceled.');
        },

    },
};
</script>

<style scoped>
.nav-link.btn {
    background: none;
    border: 1px solid #ccc;
    padding: 5px 10px;
    font: inherit;
    color: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    border-radius: 4px;
    text-align: left;
}

.nav-link.btn p {
    margin: 0;
    padding-left: 5px;
}

.nav-link.btn:hover {
    background-color: #f0f0f0;
}

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
