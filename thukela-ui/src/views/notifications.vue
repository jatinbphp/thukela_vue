<template>
  <div class="row mt-5 d-flex" style="height:75vh">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body text-center">
          <div class="row">
            <div class="col-md-12">
              <div v-if="isLoading" class="loader-container d-flex justify-content-center">
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
              <div v-else class="table-responsive">
                <table class="table">
                  <thead class=" text-primary">
                  <tr>
                    <th>Notification</th>
                  </tr>
                  </thead>
                  <tbody v-if="this.notifications && this.notifications.length > 0">
                  <tr v-for="(list, key) in this.notifications" :key="key">
                    <td>{{list.description}}</td>
                  </tr>
                  </tbody>
                  <tbody v-else>
                  <tr>
                    <td>No notifications</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Notifications",
  data() {
    return {
      isLoading: true,
      notifications: {},
    };
  },
  mounted() {
    var isLoggedIn = localStorage.getItem('is_logged_in');
    if(!isLoggedIn || isLoggedIn == 'false'){
      this.$router.push('/login/');
    }
  },
  async created() {
    try {
      const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
      const response = await axios.get(API_URL + "notifications", {
        params: {
          username: this.$store.getters.getUsername,
          password: this.$store.getters.getPassword,
        },
      });
      if (response.data.notifications) {
        this.notifications = response.data.notifications;
        this.isLoading = false;
      } else {
        console.error("Invalid response data:", response.data);
        this.isLoading = false;
      }
    } catch (error) {
      console.error("Error fetching chart data:", error);
      this.isLoading = false; // Set isLoading to false in case of error
    }
  },
};
</script>

<style>
.loader-container {
  /* Center the loader */
  display: flex;
  justify-content: center;
  align-items: center;
  height:70vh; /* Adjust as needed */
}

.card .card-body {
  font-family: Open Sans;
  padding: 1.5rem;
  background: rgb(18, 15, 38);
  background: linear-gradient(180deg, rgba(18, 15, 38, 1) 0%, rgba(45, 54, 159, 1) 100%);
  border-radius: 15px;
}

</style>
