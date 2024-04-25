<template>
  <div class="py-4 container-fluid">
    <div class="row d-flex" style="height: 75vh">
      <div class="mx-auto col-lg-9 col-12">
        <div class="alert alert-success alert-dismissible fade show" v-if="isSuccessShow" role="alert">
          <span class="alert-icon"><i class="ni ni-like-2"></i></span>
          <span class="alert-text"><strong>Success!</strong> Message send successfully!</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="alert alert-danger alert-dismissible fade show" v-if="isErrorShow" role="alert">
          <span class="alert-icon"><i class="ni ni-like-2"></i></span>
          <span class="alert-text"><strong>Error!</strong> Something is wrong. Please try again!</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="mt-4 card card-body">
          <h4 class="mb-0 text-center">Contact Us</h4>
          <hr class="my-3 horizontal dark" />
          <div class="mt-4 row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="buildingName" class="form-label label-text-heading">Building Name</label>
                <input id="buildingName" v-model="buildingname" placeholder="Building Name" type="text" class="form-control" />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="flatnumber" class="form-label label-text-heading">Flat Number</label>
                <input id="flatnumber" v-model="flatnumber" placeholder="Flat Number" type="text" class="form-control" />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="phonenumber" class="form-label label-text-heading">Phone Number</label>
                <input id="phonenumber" v-model="phonenumber" placeholder="Phone Number" type="text" class="form-control" />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="message" class="form-label label-text-heading">Message</label>
                <input id="message" v-model="message" placeholder="Message" type="text" class="form-control" />
              </div>
            </div>
          </div>
          <div class="col-md-12 text-center">
            <button
                type="button"
                name="button"
                class="m-0 btn bg-gradient-success ms-2"
                @click="submitContactUs"
            >
              Send
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ContactUs",
  data() {
    return {
      isSuccessShow:false,
      isErrorShow:false,
      isLoading: true,
      buildingname: "",
      flatnumber: "",
      phonenumber: "",
      message: "",
    };
  },
  mounted() {
    var isLoggedIn = localStorage.getItem('is_logged_in');
    if(!isLoggedIn || isLoggedIn == 'false'){
      this.$router.push('/login/');
    }
  },
  methods:{
    async submitContactUs() {
      try {
        this.isLoading = true;
        const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
        const response = await axios.get(API_URL + "sendContactUs", {
          params: {
            buildingname: this.buildingname,
            flatnumber: this.flatnumber,
            phonenumber: this.phonenumber,
            message: this.message,
            username: this.$store.getters.getUsername,
            password: this.$store.getters.getPassword,
          }
        });
        if (response.status == 200) {
          this.isSuccessShow = true;
          this.isLoading = false;
        } else {
          this.isErrorShow = true;
          this.isLoading = false;
        }
        this.buildingname = this.flatnumber = this.phonenumber = this.message = "";
      } catch (error) {
        console.error("Error fetching chart data:", error);
        this.isErrorShow = true;
        this.isLoading = false;
      }
    }
  }
};
</script>
<style>
.card {
  box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
  background-color: #0b0e34;
}

.label-text-heading{
  color: #29308E !important;
}

</style>
