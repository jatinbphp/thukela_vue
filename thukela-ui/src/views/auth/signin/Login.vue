<template>
  <main class="mt-0 main-content main-content-bg">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="mx-auto col-xl-4 col-lg-5 col-md-6 d-flex flex-column">
              <div class="mt-8 card card-plain">
                <div class="pb-0 card-header text-start">
                  <h3 class="font-weight-bolder text-success text-gradient text-center">
                    Welcome back To Thukela Metering
                  </h3>
                </div>
                <div class="card-body pb-3">
                  <Form
                    role="form"
                    class="text-start"
                    :validation-schema="schema"
                    @submit="handleLogin"
                  >
                    <label for="email">User Name</label>
                    <soft-field
                      id="username"
                      v-model="username"
                      type="text"
                      placeholder="User Name"
                      name="username"
                    />

                    <label>Password</label>
                    <soft-field
                      id="password"
                      v-model="password"
                      type="password"
                      placeholder="Password"
                      name="password"
                    />

                    <div class="text-center">
                      <soft-button
                        class="my-4 mb-2"
                        variant="gradient"
                        color="success"
                        full-width
                        :is-disabled="loading ? true : false"
                      >
                        <span
                          v-if="loading"
                          class="spinner-border spinner-border-sm"
                        ></span>
                        <span v-else>Sign in</span>
                      </soft-button>
                    </div>
                  </Form>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="top-0 oblique position-absolute h-100 d-md-block d-none me-n8">
                <div
                  class="bg-cover oblique-image position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6"
                  :style="backgroundStyle"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script>
import SoftField from "@/components/SoftField.vue";
import SoftButton from "@/components/SoftButton.vue";
import showSwal from "/src/mixins/showSwal.js";
const body = document.getElementsByTagName("body")[0];
import { mapMutations } from "vuex";
import { Form } from "vee-validate";
import * as yup from "yup";
import axios from "axios";

export default {
  name: "Login",
  components: {
    SoftField,
    SoftButton,
    Form,
  },

  data() {
    const schema = yup.object().shape({
      username: yup.string().required("user Name is required!"),
      password: yup.string().required("Password is required!"),
    });
    return {
      loading: false,
      schema,
      username:'',
      password:'',
    };
  },

  computed: {
    backgroundStyle() {
      const imageUrl = process.env.VUE_APP_BASE_URL + 'src/assets/img/curved-images/loginBackground.jpg';
      return {
        backgroundImage: `url(${imageUrl})`
      };
    }
  },

  created() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
    body.classList.remove("bg-gray-100");
  },
  beforeUnmount() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
    body.classList.add("bg-gray-100");
  },
  methods: {
    ...mapMutations(["toggleEveryDisplay", "toggleHideConfig"]),

    async handleLogin() {
      this.loading = true;
      try {
        const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
        const responce = await axios.get(API_URL + "login", {
          params: {
            'username': this.username,
            'password': this.password,
          },
        });
        if(responce.data.success == 1){
          localStorage.setItem('is_logged_in',true);
          this.$store.dispatch('setUsername', this.username);
          this.$store.dispatch('setPassword', this.password);
          this.$router.push('/ProvisionalBill/');
        } else {
          showSwal.methods.showSwal({
            type: "error",
            message: "Invalid credentials!",
          });
          this.loading = false;
        }
        this.loading = false;
      } catch (error) {
        showSwal.methods.showSwal({
          type: "error",
          message: "Invalid credentials!",
        });
        this.loading = false;
      }
    },
  },
};
</script>
<style>
body {
  font-weight: 400;
  line-height: 1.6;
  background: rgb(18, 15, 38);
  background: linear-gradient(180deg, rgba(18, 15, 38, 1) 0%, rgba(45, 54, 159, 1) 100%);
}

.card-header {
  padding: 0.5rem 1rem;
  margin-bottom: 0;
  background-color: #fff;
  border-bottom: 0 solid rgba(0, 0, 0, .125);
}

.form-label, label {
  font-size: .75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #fff;
  margin-left: 0.25rem;
}
</style>