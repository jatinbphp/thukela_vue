<template>
  <div
    id="sidenav-collapse-main"
    class="w-auto h-auto collapse navbar-collapse max-height-vh-100 h-100"
  >
    <ul class="navbar-nav">
      <li class="nav-item" v-if="!isCredentialsMatch">
        <sidenav-item
            :to="{ name: 'ProvisionalBill' }"
            mini-icon="P"
            text="Provisional Bill"
            icon="<i class='fas fa-file-invoice'></i>"
        />
      </li>
      <li class="nav-item" v-if="!isCredentialsMatch">
        <sidenav-item
            :to="{ name: 'Notifications' }"
            mini-icon="N"
            text="Notifications"
            icon="<i class='fa fa-bell'></i>"
        />
      </li>
      <li class="nav-item" v-if="!isCredentialsMatch">
        <sidenav-item
            :to="{ name: 'ContactUs' }"
            mini-icon="C"
            text="Contact Us"
            icon="<i class='fa fa-address-book'></i>"
        />
      </li>
      <li class="nav-item" v-if="isCredentialsMatch">
        <sidenav-item
            :to="{ name: 'Setting' }"
            mini-icon="S"
            text="setting"
            icon="<i class='fa fa-cog'></i>"
        />
      </li>
    </ul>
  </div>
</template>
<script>
import SidenavItem from "./SidenavItem.vue";
import { mapState } from "vuex";
export default {
  name: "SidenavList",
  components: {
    SidenavItem,
  },
  props: {
    cardBg: {
      type: String,
      default: "",
    },
  },
  computed: {
    ...mapState(["isRTL"]),
    profile() {
      return this.$store.getters["profile/profile"];
    },
    requireAdmin() {
      if (this.$store.getters["auth/loggedIn"] && this.profile) {
        if (this.profile.roles[0].name == "admin") return true;
      }

      return false;
    },
    requireCreator() {
      if (this.$store.getters["auth/loggedIn"] && this.profile) {
        const aux = this.profile.roles[0].name;
        if (aux == "admin" || aux == "creator") return true;
      }
      return false;
    },
    username() {
      return this.$store.getters.getUsername;
    },
    password() {
      return this.$store.getters.getPassword;
    },
    isCredentialsMatch() {
      if(this.username == this.$store.getters.getAdminUser && this.password == this.$store.getters.getAdminPassword){
        return true;
      }
      return false;
    }
  },
  async created() {
    if (this.$store.getters["auth/loggedIn"]) {
      try {
        await this.$store.dispatch("profile/getProfile");
        this.profileChange = { ...this.profile };
      } catch (error) {
        try {
          await this.$store.dispatch("auth/logout");
        } finally {
          this.$router.push("/login");
        }
      }
    }
  },
  methods: {
    getRoute() {
      const routeArr = this.$route.path.split("/");
      return routeArr[1];
    },
    goToProvisionalBill() {
      alert(111);
    }
  },
};
</script>
