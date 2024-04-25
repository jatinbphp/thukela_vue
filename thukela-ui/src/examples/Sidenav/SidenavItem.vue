<template>
  <li class="nav-item">
    <router-link class="nav-link" :to="to">
      <div v-if="icon" class="text-center bg-white shadow icon icon-sm border-radius-md d-flex align-items-center justify-content-center me-2"><span v-html="icon" class="sidenav-icon ml-2"></span></div>
      <span class="sidenav-mini-icon"> {{ miniIcon }} </span>
      <span class="sidenav-normal"> {{ text }} </span>
    </router-link>
  </li>
</template>
<script>
export default {
  name: "SidenavItem",
  props: {
    to: {
      type: [Object, String],
      required: true,
    },
    miniIcon: {
      type: String,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      required: false,
    },
  },
};
</script>
