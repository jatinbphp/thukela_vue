import { createStore } from "vuex";
import bootstrap from "bootstrap/dist/js/bootstrap.min.js";
// import { auth } from "./auth.module";
import { reset } from "./reset.module";
import { profile } from "./profile.module";
import { roles } from "./roles.module";
import { users } from "./users.module";
import { tags } from "./tags.module";
import { categories } from "./categories.module";
import { items } from "./items.module";

const STORAGE_KEY = 'my_app_state';

const savedState = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');

export default createStore({
  state: {
    hideConfigButton: false,
    isPinned: true,
    showConfig: false,
    isTransparent: "",
    isRTL: false,
    color: "",
    isNavFixed: false,
    isAbsolute: false,
    showNavs: true,
    showSidenav: true,
    showNavbar: true,
    showFooter: true,
    showMain: true,
    navbarFixed:
      "position-sticky blur shadow-blur left-auto top-1 z-index-sticky px-0 mx-4",
    absolute: "position-absolute px-4 mx-0 w-100 z-index-2",
    bootstrap,
    username: savedState.username || '',
    password: savedState.password || '',
  },
  modules: {
    // auth,
    reset,
    profile,
    roles,
    users,
    tags,
    categories,
    items,
  },
  mutations: {
    toggleConfigurator(state) {
      state.showConfig = !state.showConfig;
    },
    navbarMinimize(state) {
      const sidenav_show = document.querySelector(".g-sidenav-show");
      if (sidenav_show.classList.contains("g-sidenav-hidden")) {
        sidenav_show.classList.remove("g-sidenav-hidden");
        sidenav_show.classList.add("g-sidenav-pinned");
        state.isPinned = true;
      } else {
        sidenav_show.classList.add("g-sidenav-hidden");
        sidenav_show.classList.remove("g-sidenav-pinned");
        state.isPinned = false;
      }
    },
    sidebarType(state, payload) {
      state.isTransparent = payload;
    },
    cardBackground(state, payload) {
      state.color = payload;
    },
    navbarFixed(state) {
      if (state.isNavFixed === false) {
        state.isNavFixed = true;
      } else {
        state.isNavFixed = false;
      }
    },
    toggleEveryDisplay(state) {
      state.showNavbar = !state.showNavbar;
      state.showSidenav = !state.showSidenav;
      state.showFooter = !state.showFooter;
    },
    toggleHideConfig(state) {
      state.hideConfigButton = !state.hideConfigButton;
    },
    setUsername(state, username) {
      state.username = username;
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    },
    setPassword(state, password) {
      state.password = password;
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    },
  },
  actions: {
    toggleSidebarColor({ commit }, payload) {
      commit("sidebarType", payload);
    },
    setCardBackground({ commit }, payload) {
      commit("cardBackground", payload);
    },
    setUsername({ commit }, username) {
      commit('setUsername', username);
    },
    setPassword({ commit }, password) {
      commit('setPassword', password);
    },
  },
  getters: {
    getUsername(state) {
      return state.username;
    },
    getPassword(state) {
      return state.password;
    },
  },
});
