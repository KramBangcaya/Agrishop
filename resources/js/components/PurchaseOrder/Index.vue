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
                                            <th>Reason</th>
                                            <th>Feedback</th>
                                            <th>Rating</th>
                                            <th>Actions</th>
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
                                            <td>{{ order.reason_cancel }}</td>
                                            <td>{{ order.feedback }}</td>
                                            <td>{{ order.rating }}</td>
                                            <td v-if="order.order_status !== 'Cancelled Order'" class="text-left">
                                                <button
                                                 v-if="order.order_status !== 'For Delivery'"
                                                    type="button"
                                                    class="btn btn-primary btn-sm"
                                                    @click="confirmOrder(order.id)">

                                                    <i class="fas fa-edit"></i> Confirm Order
                                                </button>
                                                <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                @click="openCancelModal(order.id)">
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
        <div v-if="showImageModal" class="modal-overlay" @click="closeImageModal">
                    <div class="modal-content">
                        <img :src="zoomedImage" alt="Zoomed Image" />
                    </div>
                </div>

                <div v-if="showCancelModal" class="modal-overlay">
<div class="modal-content">
    <h3>Cancel Order</h3>
    <p>Please provide a reason for canceling this order:</p>
    <textarea
        v-model="reason_cancel"
        class="form-control"
        rows="4"
        placeholder="Enter cancellation reason..."
    ></textarea>
    <div class="modal-actions">
        <button class="btn btn-secondary" @click="closeCancelModal">Close</button>
        <button class="btn btn-danger" @click="submitCancelOrder">Submit</button>
    </div>
</div>
</div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            API_BASE : 'http://192.168.68.67:8080',
            orders: [],       // Holds fetched orders data
            search: '',       // Search input field
            userID: null,
            showImageModal: false,
            zoomedImage: '',
            showCancelModal: false, // Tracks if the cancel modal is open
        reason_cancel: '',     // Stores the reason for cancellation
        cancelOrderId: null,
        productStocks: {},
        };
    },
    created() {
        this.getUserID(); // Fetch data when component is created

        this.getUserID().then(() => {
        // Only proceed if userID is available
        if (this.userID) {
            this.getData().then(() => {
                this.checkAndCancelPendingOrders();  // After fetching orders, check and cancel them
            });
        } else {
            console.error("No userID available, cannot fetch orders.");
        }
    });
    },
    async mounted() {
        // console.log("Mounted called");
        this.getData();
    // Call checkAndCancelPendingOrders after the page loads
    this.checkAndCancelPendingOrders();
    },

    methods: {
        openCancelModal(orderId) {
        this.cancelOrderId = orderId; // Store the order ID
        this.reason_cancel = '';       // Reset the reason
        this.showCancelModal = true; // Show the modal
    },
    // Closes the cancel modal
    closeCancelModal() {
        this.showCancelModal = false;
    },
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
                this.getData();
                // console.log(this.userID); // Now fetch orders after userID is fetched
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
        // console.log(this.API_BASE);
            try {
                const response = await fetch(this.API_BASE + `/buyer/get-orders.php?seller_id=${this.userID}`);
                const data = await response.json();
                console.log(response);
                if (data.status === 'success') {
                    this.orders = data.data;
                    await this.getProductStocks();
                    // console.log("Fetched orders:", this.orders);
                }

            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        },
                async getProductStocks() {
            // Create a Set to store unique product IDs
            const uniqueProductIds = [...new Set(this.orders.map(order => order.product_id))];

            for (const productId of uniqueProductIds) {
                try {
                    const response = await fetch(`/products/product_stock/${productId}`);


                    // Check if the response is valid
                    if (!response.ok) {
                        console.error(`Error: HTTP ${response.status} for product ID: ${productId}`);
                        continue;
                    }

                    // Check if the response is JSON
                    const contentType = response.headers.get("Content-Type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await response.text(); // Read raw response text for debugging
                        console.error(`Unexpected response for product ID: ${productId}:`, text);
                        continue;
                    }

                    // Parse and log JSON response
                    const data = await response.json();

                    // Check if the data contains the expected structure
                    if (data.data && data.data.Quantity !== undefined) {
                        console.log(`Stock for product ID ${productId}: ${data.data.Quantity}`);
                        this.$set(this.productStocks, productId, data.data.Quantity); // Update stock info
                    } else {
                        console.error(`Unexpected data format for product ID: ${productId}`, data);
                    }
                } catch (error) {
                    console.error(`Error fetching stock for product ID: ${productId}`, error);
                }
            }
        },


//         async checkAndCancelPendingOrders() {
//     console.log('Checking and canceling pending orders...');

//     // Filter out only pending orders
//     const pendingOrders = this.orders.filter(order => order.order_status === 'Pending');

//     if (pendingOrders.length === 0) {
//         console.log('No pending orders to check.');
//         return;
//     }

//     console.log("Pending Orders:", pendingOrders);

//     // Group pending orders by product ID
//     const pendingOrdersByProduct = pendingOrders.reduce((acc, order) => {
//         if (!acc[order.product_id]) acc[order.product_id] = [];
//         acc[order.product_id].push(order);
//         return acc;
//     }, {});

//     // Process orders for each product
//     for (const productId in pendingOrdersByProduct) {
//         try {
//             // Fetch the stock for this product
//             const stockResponse = await fetch(`/products/product_stock/${productId}`);
//             const stockData = await stockResponse.json();

//             if (!stockData || !stockData.data || stockData.data.Quantity === undefined) {
//                 console.error(`Invalid stock data for product ${productId}.`);
//                 continue;
//             }

//             let remainingStock = stockData.data.Quantity;
//             console.log(`Initial stock for product ${productId}: ${remainingStock}`);

//             // Process orders in sequence
//             for (const order of pendingOrdersByProduct[productId]) {
//                 const orderedQuantity = order.product_quantity;

//                 if (remainingStock <= 0) {
//                     console.log(`Stock for product ${productId} is depleted. Cancelling order ${order.id}...`);
//                     await this.cancelPendingOrders(order.id, "Stock is out of order");
//                 } else if (orderedQuantity > remainingStock) {
//                     console.log(`Ordered quantity (${orderedQuantity}) for product ${productId} exceeds remaining stock (${remainingStock}). Cancelling order ${order.id}...`);
//                     await this.cancelPendingOrders(order.id, "Insufficient stock");
//                 } else {
//                     console.log(`Order ${order.id} for product ${productId} is valid. Deducting stock.`);
//                     remainingStock -= orderedQuantity; // Update the remaining stock
//                 }
//             }
//         } catch (error) {
//             console.error(`Error checking stock for product ${productId}:`, error);
//         }
//     }
// },


        async checkAndCancelPendingOrders() {
    console.log('Checking and canceling pending orders...');

    // Filter out only pending orders
    const pendingOrders = this.orders.filter(order => order.order_status === 'Pending');

    if (pendingOrders.length === 0) {
        console.log('No pending orders to check.');
        return;
    }

    console.log("Pending Orders:", pendingOrders);
    for (const order of pendingOrders) {
        const productId = order.product_id;

        try {
            // Fetch the stock for this product
            const stockResponse = await fetch(`/products/product_stock/${productId}`);
            const stockData = await stockResponse.json();

            if (!stockData || !stockData.data || stockData.data.Quantity === undefined) {
                console.error(`Invalid stock data for product ${productId}.`);
                continue;
            }

            const availableStock = stockData.data.Quantity;
            const orderedQuantity = order.product_quantity;

            // Cancel the order if stock is zero or if the ordered quantity exceeds available stock
            if (availableStock <= 0) {
                console.log(`Stock for product ${productId} is out. Cancelling order ${order.id}...`);
                await this.cancelPendingOrders(productId); // Cancel the specific order
            } else if (orderedQuantity > availableStock) {
                console.log(`Ordered quantity for product ${productId} is greater than available stock. Cancelling order ${order.id}...`);
                await this.cancelPendingOrders(productId); // Cancel the specific order
                if(orderedQuantity > availableStock){
                    console.log(`${order.id} is now back to pending status`);
                    console.log(`${order.id} is now back to pending status`);
                    console.log(`${order.id} is now back to pending status`);
                    await this.PendingOrdersReturned(productId);
                }
            } else {
                console.log(`Stock for product ${productId} is sufficient. Order ${order.id} remains active.`);
            }
        } catch (error) {
            console.error(`Error checking stock for product ${productId}:`, error);
        }
    }
},


async confirmOrder(id) {
    const isConfirmed = window.confirm('Are you sure you want to confirm this order?');

    if (!isConfirmed) {
        // If the user clicks "Cancel", stop further execution
        return;
    }

    const confirmPayload = {
        order_id: id,
        order_status: "For Delivery",
    };

    // Find the order to get the product details
    const order = this.orders.find(order => order.id === id);
    if (!order) {
        alert('Order not found');
        return;
    }

    // Fetch stock for the product to check if it's out of stock
    const stockResponse = await fetch(`/products/product_stock/${order.product_id}`);
    const stockData = await stockResponse.json();
    console.log(stockResponse);

    // Prepare the payload to decrease the product quantity
    const productPayload = {
        quantity_to_decrease: order.product_quantity, // Decrease the quantity based on the order
    };

    // Call the 'minus_product' API to update the product stock
    fetch(`api/product1/minus_product/${order.product_id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(productPayload),
    })
    .then(async (response) => {
        const text = await response.text();
        console.log('Raw response:', text);
        return JSON.parse(text); // Parse JSON response
    })
    .then(data => {
        if (data.status === 'success') {
            // If product stock is updated successfully, proceed to confirm the order status
            fetch(this.API_BASE + '/buyer/update-order-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(confirmPayload),
            })
            .then(async (response) => {
                const text = await response.text();
                console.log('Raw response:', text);
                return JSON.parse(text);
            })
            .then(data => {
                    if (data.status === 'success') {
                        alert('Order has been confirmed.');
                        location.reload();
                        this.getData(); // Refresh the order list


                    } else {
                        alert('Failed to confirm the order. Please try again.');
                    }
            })
            .catch(error => {
                console.error('Error confirming order:', error);
                alert('An error occurred while confirming the order.');
            });
        } else {
            alert('Failed to update product stock. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error updating product stock:', error);
        alert('An error occurred while updating the product stock.');
    });
},

async cancelPendingOrders(productId) {
    // Loop through all orders to find pending orders for the product
    const pendingOrders = this.orders.filter(order => order.product_id === productId && order.order_status === 'Pending');

    if (pendingOrders.length === 0) {
        console.log('No pending orders to cancel.');
        return;
    }

    // Loop through each pending order and cancel it
    for (const order of pendingOrders) {
        const cancelPayload = {
            order_id: order.id,
            reason_cancel: "Stock is out of order",
            order_status: "Cancelled Order",
        };

        try {
            const response = await fetch(this.API_BASE + '/buyer/order-cancelled.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(cancelPayload),
            });
            const data = await response.json();
            if (data.status === 'success') {
                console.log(`Order ${order.id} cancelled successfully due to stock being out.`);
            } else {
                console.error(`Failed to cancel order ${order.id}.`);
            }
        } catch (error) {
            console.error('Error canceling order:', error);
            alert("An error occurred while canceling the order.");
        }
    }

    alert('All pending orders for this product have been canceled due to stock being out.');
    this.getData(); // Refresh the order list
},

async PendingOrdersReturned(productId) {
    console.log(productId)

    // Loop through all orders to find pending orders for the product
    const pendingOrders = this.orders.filter(order => order.product_id === productId && order.order_status === '"Pending"' || order.order_status === '"Cancelled Order"');
    console.log(pendingOrders)
    console.log(this.orders)
    console.log(this.orders)

    if (pendingOrders.length === 0) {
        console.log('No Cancelled Order.');
        return;
    }

    // Loop through each pending order and cancel it
    for (const order of pendingOrders) {
        const cancelPayload = {
            order_id: order.id,
            reason_cancel: "",
            order_status: "Pending",
        };

        try {
            const response = await fetch(this.API_BASE + '/buyer/order_pending.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(cancelPayload),
            });
            const data = await response.json();
            if (data.status === 'success') {
                console.log(`Order ${order.id} cancelled successfully due to stock being out.`);
            } else {
                console.error(`Failed to cancel order ${order.id}.`);
            }
        } catch (error) {
            console.error('Error canceling order:', error);
            alert("An error occurred while canceling the order.");
        }
    }

    alert('All pending orders for this product have been canceled due to stock being out.');
    this.getData(); // Refresh the order list
},

        // Handle Cancel Order
            async submitCancelOrder() {
            if (!this.reason_cancel.trim()) {
                alert("Please provide a cancellation reason.");
                return;
            }

            const isCancelled = window.confirm('Are you sure you want to cancel this order?');

            if (!isCancelled) {
                // If the user clicks "Cancel", stop further execution
                return;
            }

            const cancelPayload = {
                order_id: this.cancelOrderId,
                reason_cancel: this.reason_cancel,
                order_status: "Cancelled Order",
            };
            console.log(cancelPayload);
            try {
                const response = await fetch(this.API_BASE + '/buyer/order-cancelled.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(cancelPayload),
                });
                const data = await response.json();
                console.log(data);
                if (data.status === 'success') {
                    alert("Order canceled successfully.");
                    location.reload();
                    this.getData(); // Refresh orders list
                } else {
                    alert("Failed to cancel the order. Please try again.");
                }
            } catch (error) {
                console.error('Error canceling order:', error);
                alert("An error occurred while canceling the order.");
            } finally {
                this.closeCancelModal(); // Close the modal
            }
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
