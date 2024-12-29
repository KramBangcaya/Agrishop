    <template>
        <div>
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Orders </h1>
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
                                        <div class="input-group input-group-sm">
                                            <input
                                                v-model="search"
                                                type="text"
                                                @keyup="getData"
                                                name="table_search"
                                                class="form-control float-right"
                                                placeholder="Search"
                                            />
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="getData">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Buyer's Name</th>
                                                <th>Product Name</th>
                                                <th>Ordered Quantity</th>
                                                <th>Total Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(order, index) in orders" :key="index">
                                                <td>{{ order.buyer_name }}</td>
                                                <td>{{ order.product_name }}</td>
                                                <td>{{ order.product_quantity }}</td>
                                                <td>{{ order.totalPayment }}</td>
                                                <td class="text-right">
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary btn-sm"
                                                        @click="confirmOrder(order.id)">
                                                        <i class="fas fa-edit"></i> Confirm Order
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger btn-sm"
                                                        @click="cancelOrder(order.id)">
                                                        <i class="fas fa-trash-alt"></i> Cancel Order
                                                    </button>
                                                </td>
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
        </div>
    </template>

    <script>

    export default {
        data() {
            return {
                orders: [],       // Holds fetched orders data
                search: '',       // Search input field
                userID: null,
            };
        },
        created() {
            this.getUserID(); // Fetch data when component is created
        },
        methods: {

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
                    const response = await fetch(`http://192.168.1.101:8080/buyer/get-orders.php?seller_id=${this.userID}`);
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.orders = data.data;
                    }
                } catch (error) {
                    console.error('Error fetching orders:', error);
                }
            },
            // Handle Confirm Order
            confirmOrder(id) {
                alert(`Order with ID ${id} confirmed.`);
            },
            // Handle Cancel Order
            cancelOrder(id) {
                alert(`Order with ID ${id} canceled.`);
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
    </style>
