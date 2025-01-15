<template>
    <div class="container">
         <!-- First Container: For Admin -->
      <div v-if="user_type === 'admin'" class="row justify-content-center">
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Total Number of Buyers</h5>
                </div>
                <div class="card-body">
                    <h1>{{ buyerCount }}</h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Total Number of Sellers</h5>
                </div>
                <div class="card-body">
                    <h1>{{ sellerCount }}</h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Total Number of Complaints</h5>
                </div>
                <div class="card-body">
                    <h1>{{ complaints }}</h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">For Approval</h5>
                </div>
                <div class="card-body">
                    <h1>{{  approval }}</h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Account Activated</h5>
                </div>
                <div class="card-body">
                    <h1>{{ activated }}</h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Account Deactivated</h5>
                </div>
                <div class="card-body">
                    <h1>{{ deactivated }}</h1>
                </div>
                </div>
            </div>
            </div>



  <!-- Second Container: For Seller -->
            <div v-if="user_type === 'seller'" class="row justify-content-center">
                <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                    <div class="card-header">
                        <h5 class="card-title">Total Completed Order</h5>
                    </div>
                    <div class="card-body">
                        <h1>{{ top_orders }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                    <div class="card-header">
                        <h5 class="card-title">Total Sales</h5>
                    </div>
                    <div class="card-body">
                        <h1>{{ "₱ "+ totalSales+".00" }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                    <div class="card-header">
                        <h5 class="card-title">Pending Orders</h5>
                    </div>
                    <div class="card-body">
                        <h1>{{ total_pending }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                    <div class="card-header">
                        <h5 class="card-title">For Delivery</h5>
                    </div>
                    <div class="card-body">
                        <h1>{{ total_for_delivery }}</h1>
                    </div>
                </div>
            </div>
        </div>
      <div class="row justify-content-center">
        <!-- Left side: Top Most Sold Products Chart -->
        <div class="col-md-6">
            <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                <h5 class="card-title">Top Most Sold Products</h5>
                </div>
                <div class="card-body">
                <!-- Display the list of Top Most Sold Products in a table -->
                <table class="table border table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Quantity Sold</th>
                    </tr>
                    </thead>
                    <tbody class="border-black">
                    <!-- Loop through topProducts and display them in table rows -->
                    <tr v-for="(product, index) in topProducts" :key="index">
                        <th scope="row">{{ product.rank }}</th>
                        <td>{{ product.product_name }}</td>
                        <td>{{ product.total_quantity }}</td>
                    </tr>
                    </tbody>
                </table>

                <!-- If no products are fetched yet, show a loading message -->
                <div v-if="!topProducts.length && !isLoading" class="text-center">
                    No products available.
                </div>
                <div v-if="isLoading" class="text-center">
                    Loading top products...
                </div>
                </div>
            </div>
            </div>

        <!-- Sales Overview Chart -->
        <div class="col-md-6">
          <div class="card m-3">
            <div class="card-header">
              <h5 class="card-title">Sales Overview</h5>
            </div>
            <div class="card-body">
              <bar-chart :data="barChartData" :options="barChartOptions" />
            </div>
          </div>
        </div>


      </div>
            </div>


    </div>








  </template>

  <script>
  import { Bar, Pie } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
  import axios from 'axios';

  // Register necessary chart components
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

  export default {
    components: {
      'bar-chart': Bar,
      'pie-chart': Pie,
    },
    data() {
      return {
        API_BASE : 'http://192.168.1.129:8080',
        apiBaseUrl: process.env.VUE_APP_API_BASE_URL,
        totalSales: 0,
        total_pending: 0,
        total_for_delivery: 0,
        top_orders: 0,
        sellerCount: 0,
        buyerCount: 0,
        approval: 0,
        validated: 0,
        deactivated: 0,
        activated: 0,
        complaints: 0,
        user_type: '',
        userId: null,
        isLoading: true,
        topProducts: [],
        // Bar chart for sales overview
        barChartData: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'], // Placeholder labels
        datasets: [
            {
            label: 'Sales',
            data: [], // Initially empty
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
            }
            ]
        },

                barChartOptions: {
            responsive: true,
            scales: {
                y: {
                beginAtZero: true
                }
            },
            plugins: {
        title: {
          display: true,
          text: 'Monthly Sales'
        }
      }
        },




        // New chart data for Top Most Sold Products
        topProductsChartData: {
          labels: [], // Product names (Top 5)
          datasets: [
            {
              label: 'Quantity Sold',
              data: [], // Quantities for top 5 products
              backgroundColor: 'rgba(153, 102, 255, 0.6)',
              borderColor: 'rgba(153, 102, 255, 1)',
              borderWidth: 1
            }
          ]
        },

        topProductsChartOptions: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            title: {
              display: true,
              text: 'Top Most Sold Products'
            }
          }
        }
      };
    },

    mounted() {
      console.log('Dashboard Component Mounted.');
      this.fetchSalesData(this.userId);
      this.getUserData();
      this.getTotalPending(this.userId);
      this.getTotalDelivery(this.total_for_delivery);
      this.fetchTopProducts(this.userId);
      this.getSellerData();
      this.getBuyerData();
      this.getReportData();
      this.getApproveData();
      this.getActivatedData();
      this.getDeactivatedData();
      this.getTotalSales(this.userId);
      this.getTotalOrders(this.userId);
    },

    methods: {
        async fetchSalesData() {

            const userId = window.user.id;


            if (!userId) {  // Ensure userId is available
                console.error("UserID is missing");
                return;
            }
            // console.log('legit' + userId)
            try {
                console.log(userId)
            const response = await fetch(this.API_BASE + `/buyer/month_sales.php?seller_id=${userId}`);
            const data = await response.json();

            if (data.status === 'success' && data.data) {
                // console.log('data.data');
                const salesData = data.data;
                // Prepare the data for the bar chart
                const labels = salesData.map(item => {
                switch (item.month) {
                    case 1: return 'January';
                    case 2: return 'February';
                    case 3: return 'March';
                    case 4: return 'April';
                    case 5: return 'May';
                    // Add other months as necessary
                    default: return `Month ${item.month}`;
                }
                });
                const sales = salesData.map(item => item.total_payment_per_month);
                // Update chart data
                this.barChartData.labels = labels;
                this.barChartData.datasets[0].data = sales;
            }
            } catch (error) {
            console.error('Error fetching sales data:', error);
            }
        },
        async fetchTopProducts() {
            const userId = window.user.id;
            if (!userId) {  // Ensure userId is available
      console.error("UserID is missingssssss");
      return;
    }
            // console.log('sample');
      try {
        const response = await fetch(this.API_BASE + `/buyer/top_products.php?seller_id=${userId}`);
        const data = await response.json();
        if (data && data.data) {
          this.topProducts = data.data;
        }
      } catch (error) {
        console.error('Error fetching top products:', error);
      } finally {
        this.isLoading = false;  // Hide the loading spinner once data is fetched
      }
    },
      // Fetch categories and update pie chart


      // Fetch user data
      getUserData() {
            this.timer = setTimeout(() => {
                axios.get('/api/user/show/')
                    .then(response => {
                        if (response.data.data) {
                            this.user = response.data.data[0];
                            this.user_type = this.user.user_type;
                            this.userId = this.user.id;
                            console.log("userID: " + this.userId);
                            this.getTotalSales();
                            this.getTotalOrders();
                            this.fetchTopProducts();
                            this.fetchSalesData();
                            this.getTotalPending();
                            this.getTotalDelivery();
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
        async getTotalOrders() {
            const userId = window.user.id;
    if (!userId) {  // Ensure userId is available
      console.error("UserID is missing");
      return;
    }

    try {
      const response = await fetch(this.API_BASE + `/buyer/top_orders.php?seller_id=${userId}`);
      const data = await response.json();  // Parse JSON response
        // console.log(response);
      // Check if the status is 'success' and update totalSales
      if (data.status === 'success') {
        this.top_orders = data.data.Total_orders;  // Update total sales value
      } else {
        console.error('Failed to fetch top orders');
      }
    } catch (error) {
      console.error('Error fetching total top orders:', error);
    }
  },

  async getTotalDelivery() {
    const userId = window.user.id;
    if (!userId) {  // Ensure userId is available
      console.error("UserID is missing");
      return;
    }

    try {
      const response = await fetch(this.API_BASE +`/buyer/total_delivery.php?seller_id=${userId}`);
      const data = await response.json();  // Parse JSON response

      // Check if the status is 'success' and update totalSales
      if (data.status === 'success') {
        this.total_for_delivery = data.data.total_for_delivery;  // Update total sales value
      } else {
        console.error('Failed to fetch total sales');
      }
    } catch (error) {
      console.error('Error fetching total sales:', error);
    }
  },

  async getTotalPending() {
    const userId = window.user.id;
    if (!userId) {  // Ensure userId is available
      console.error("UserID is missing");
      return;
    }

    try {
      const response = await fetch(this.API_BASE +`/buyer/total_pending.php?seller_id=${userId}`);
      const data = await response.json();  // Parse JSON response

      // Check if the status is 'success' and update totalSales
      if (data.status === 'success') {
        this.total_pending = data.data.total_pending;  // Update total sales value
      } else {
        console.error('Failed to fetch total sales');
      }
    } catch (error) {
      console.error('Error fetching total sales:', error);
    }
  },

    async getTotalSales() {
        const userId = window.user.id;
    if (!userId) {  // Ensure userId is available
      console.error("UserID is missing");
      return;
    }

    try {
      const response = await fetch(this.API_BASE +`/buyer/total_sales.php?seller_id=${userId}`);
      const data = await response.json();  // Parse JSON response

      // Check if the status is 'success' and update totalSales
      if (data.status === 'success') {
        this.totalSales = data.data.total_sales;  // Update total sales value
      } else {
        console.error('Failed to fetch total sales');
      }
    } catch (error) {
      console.error('Error fetching total sales:', error);
    }
  },


        getSellerData() {
    axios.get('/notif/all_seller2')
        .then(response => {
            if (response.data.data && Array.isArray(response.data.data)) {
                // Assign the returned sellers array
                this.user = response.data.data;

                // Store the count of sellers
                this.sellerCount = this.user.length;

                // console.log('Sellers:', this.user);
                // console.log('Total Sellers:', this.sellerCount);
            }
        })
        .catch(error => {
            this.error = error;
            toast.fire({
                icon: 'error',
                text: error.response?.data?.message || 'An error occurred',
            });
        });
},

        getBuyerData() {
        axios.get('/notif/all_buyer2')
            .then(response => {
                if (response.data.data && Array.isArray(response.data.data)) {
                    // Assign the returned sellers array
                    this.user = response.data.data;

                    this.buyerCount = this.user.length;

                // console.log('Buyer:', this.user);
                // console.log('Total Sellers:', this.buyerCount);

                }
            })
            .catch(error => {
                this.error = error;
                toast.fire({
                    icon: 'error',
                    text: error.response?.data?.message || 'An error occurred',
                });
            });
        },

        getReportData() {
        axios.get('/report/all_reports')
            .then(response => {
                if (response.data.data && Array.isArray(response.data.data)) {
                    // Assign the returned sellers array
                    this.user = response.data.data;

                    this.complaints = this.user.length;

                // console.log('complaints:', this.user);
                // console.log('Total Complaints:', this.complaints);

                }
            })
            .catch(error => {
                this.error = error;
                toast.fire({
                    icon: 'error',
                    text: error.response?.data?.message || 'An error occurred',
                });
            });
        },
        getApproveData() {
        axios.get('/notif/approval')
            .then(response => {
                if (response.data.data && Array.isArray(response.data.data)) {
                    // Assign the returned sellers array
                    this.user = response.data.data;

                    this.approval = this.user.length;

                // console.log('approval:', this.user);
                // console.log('Total Approval:', this.approval);

                }
            })
            .catch(error => {
                this.error = error;
                toast.fire({
                    icon: 'error',
                    text: error.response?.data?.message || 'An error occurred',
                });
            });
        },
        getActivatedData() {
        axios.get('/notif/activate')
            .then(response => {
                if (response.data.data && Array.isArray(response.data.data)) {
                    // Assign the returned sellers array
                    this.user = response.data.data;

                    this.activated = this.user.length;

                // console.log('Activated:', this.user);
                // console.log('Total Activated:', this.activated);

                }
            })
            .catch(error => {
                this.error = error;
                toast.fire({
                    icon: 'error',
                    text: error.response?.data?.message || 'An error occurred',
                });
            });
        },
        getDeactivatedData() {
        axios.get('/notif/deactivate')
            .then(response => {
                if (response.data.data && Array.isArray(response.data.data)) {
                    // Assign the returned sellers array
                    this.user = response.data.data;

                    this.deactivated = this.user.length;

                console.log('Deactivated:', this.user);
                console.log('Total Deactivated:', this.deactivated);

                }
            })
            .catch(error => {
                this.error = error;
                toast.fire({
                    icon: 'error',
                    text: error.response?.data?.message || 'An error occurred',
                });
            });
        },
    },


    created() {
        this.getUserData();
    },
  };
  </script>

  <style scoped>
  .card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .card-header {
    background-color: #f8f9fa;
  }

  .card-body {
    background-color: #fff;
  }

  .list-group-item {
  font-size: 16px;
}
  </style>
