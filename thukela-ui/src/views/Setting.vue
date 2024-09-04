<template>
	<div class="py-4 container-fluid">
  		<div v-if="isLoading" class="loader-container d-flex justify-content-center">
			<div class="spinner-border" role="status">
			<span class="sr-only">Loading...</span>
			</div>
		</div>
      	<div v-else class="row d-flex" style="height: 75vh">
        	<div class="mx-auto col-lg-9 col-12">
				<div class="alert alert-success alert-dismissible fade show" v-if="isSuccessShow" role="alert">
					<span class="alert-icon"><i class="ni ni-like-2"></i></span>
					<span class="alert-text"><strong>Success!</strong> Setting update successfully!</span>
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
					<h4 class="mb-0 text-center">Settings</h4>
					<hr class="my-3 horizontal dark" />
					<div class="mt-4 row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="email_alerts_percentage" class="form-label label-text-heading">Email Alerts Percentage:</label>
								<input id="email_alerts_percentage" v-model="email_alerts_percentage" placeholder="Please enter percentage" type="number" step="1" min="1" class="form-control" />
							</div>
						</div>
					</div>
					<div class="col-md-12 text-center">
						<button
							type="button"
							name="button"
							class="m-0 btn bg-gradient-success ms-2"
							@click="updateSetting">
							Update
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
	data() {
		return {
			isLoading: true,
			isSuccessShow: false,
			isErrorShow: false,
			email_alerts_percentage: 1,
		};
  	},
  	mounted() {
    	var isLoggedIn = localStorage.getItem('is_logged_in');
    	if(!isLoggedIn || isLoggedIn == 'false'){
        	this.$router.push('/login/');
    	}
  		if(this.$store.getters.getUsername != this.$store.getters.getAdminUser || this.$store.getters.getPassword != this.$store.getters.getAdminPassword){
        	this.$router.push('/login/');
      	}
  	},
  	async created() {
  		this.isLoading = true;
    	try {
        	const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
        	const response = await axios.get(API_URL + "get_setting", {
				params: {
					username: this.$store.getters.getUsername,
					password: this.$store.getters.getPassword,
				},
       		});
        	this.email_alerts_percentage = response.data.settingData.email_alerts_percentage ?? 1;
      		this.isLoading = false;
    	} catch (error) {
    		console.error("Error fetching chart data:", error);
        	this.isLoading = false;
     	}
  	},
  	methods:{
		async updateSetting() {
			try {
				this.isLoading = true;
				const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
				const response = await axios.get(API_URL + "set_setting", {
					params: {
						username: this.$store.getters.getUsername,
						password: this.$store.getters.getPassword,
						email_alerts_percentage: this.email_alerts_percentage,
					}
				});
				if (response.status == 200) {
					this.isSuccessShow = true;
					this.isLoading = false;
				} else {
					this.isErrorShow = true;
					this.isLoading = false;
				}
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
  