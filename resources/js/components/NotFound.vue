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
                    <h1></h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">For Approval</h5>
                </div>
                <div class="card-body">
                    <h1></h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Account Activated</h5>
                </div>
                <div class="card-body">
                    <h1></h1>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                <div class="card-header">
                    <h5 class="card-title">Account Deactivated</h5>
                </div>
                <div class="card-body">
                    <h1></h1>
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
                        <h5 class="card-title">Total Order</h5>
                    </div>
                    <div class="card-body">
                        <h1>Sample Order</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card m-3" :style="cardStyle">
                    <div class="card-header">
                        <h5 class="card-title">Total Sale</h5>
                    </div>
                    <div class="card-body">
                        <h1>Sample Sale</h1>
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
                  <th scope="row">{{ index + 1 }}</th>
                  <td>{{ product.Product_Name }}</td>
                  <td>{{ product.Quantity }}</td>
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

        <!-- Revenue by Category Chart -->
        <div class="col-md-8">
          <div class="card m-3">
            <div class="card-header">
              <h5 class="card-title">Revenue by Category</h5>
            </div>
            <div class="card-body">
              <div v-if="isLoading">Loading Revenue Data...</div>
              <pie-chart v-else :data="pieChartData" :options="pieChartOptions" />
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
        sellerCount: 0,
        buyerCount: 0,
        approval: 0,
        validated: 0,
        deactivated: 0,
        activated: 0,
        complaints: 0,
        user_type: '',
        isLoading: true,
        topProducts: [],
        // Bar chart for sales overview
        barChartData: {
          labels: ['January', 'February', 'March', 'April', 'May'],
          datasets: [
            {
              label: 'Sales',
              data: [40, 55, 70, 65, 90],
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

        // Pie chart data for revenue by category
        pieChartData: {
          labels: [],
          datasets: [
            {
              label: 'Revenue by Category',
              data: [],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)'
              ],
              borderWidth: 1
            }
          ]
        },

        pieChartOptions: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Revenue Distribution by Category'
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
      this.fetchCategories();
      this.fetchTopProducts();
      this.getSellerData();
      this.getBuyerData();
    },

    methods: {
      // Fetch categories and update pie chart
      async fetchCategories() {
        try {
          const response = await axios.get('/categories/all');
          const categories = response.data.data;
          const categoryNames = categories.map(category => category.category);
          const categoryRevenue = categories.map(() => Math.floor(Math.random() * 500) + 100);
          this.pieChartData.labels = categoryNames;
          this.pieChartData.datasets[0].data = categoryRevenue;
          this.isLoading = false;
        } catch (error) {
          console.error('Error fetching categories:', error);
        }
      },

      // Fetch top products and update top products chart
      async fetchTopProducts() {
      try {
        const response = await axios.get('/products/all'); // API endpoint to fetch all products
        const products = response.data.data;

        // Sort products by quantity in descending order and pick the top 5
        const topProducts = products
          .sort((a, b) => b.Quantity - a.Quantity)
          .slice(0, 5); // Get the top 5 products

        // Store the top 5 products in the topProducts array
        this.topProducts = topProducts;
        this.isLoading = false; // Stop loading after fetching data
      } catch (error) {
        console.error('Error fetching top products:', error);
        this.isLoading = false; // Stop loading in case of error
      }
    },
      // Fetch user data
      getUserData() {
            this.timer = setTimeout(() => {
                axios.get('/api/user/show/')
                    .then(response => {
                        if (response.data.data) {
                            this.user = response.data.data[0];
                            this.user_type = this.user.user_type;
                            // console.log(this.user.user_type);
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

        getSellerData() {
    axios.get('/notif/all_seller2')
        .then(response => {
            if (response.data.data && Array.isArray(response.data.data)) {
                // Assign the returned sellers array
                this.user = response.data.data;

                // Store the count of sellers
                this.sellerCount = this.user.length;

                console.log('Sellers:', this.user);
                console.log('Total Sellers:', this.sellerCount);
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

                console.log('Buyer:', this.user);
                console.log('Total Sellers:', this.buyerCount);

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
