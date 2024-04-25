import { createRouter, createWebHistory } from "vue-router";
// import Default from "../views/dashboards/Default.vue";
// import Automotive from "../views/dashboards/Automotive.vue";
// import SmartHome from "../views/dashboards/SmartHome.vue";
// import VRDefault from "../views/dashboards/vr/VRDefault.vue";
// import VRInfo from "../views/dashboards/vr/VRInfo.vue";
// import CRM from "../views/dashboards/CRM.vue";
// import Overview from "../views/pages/profile/Overview.vue";
// import Teams from "../views/pages/profile/Teams.vue";
// import Projects from "../views/pages/profile/Projects.vue";
// import General from "../views/pages/projects/General.vue";
// import Timeline from "../views/pages/projects/Timeline.vue";
// import NewProject from "../views/pages/projects/NewProject.vue";
// import Pricing from "../views/pages/Pricing.vue";
// import RTL from "../views/pages/Rtl.vue";
// import Charts from "../views/pages/Charts.vue";
// import SweetAlerts from "../views/pages/SweetAlerts.vue";
// import Notifications from "../views/pages/Notifications.vue";
// import Kanban from "../views/applications/Kanban.vue";
// import Wizard from "../views/applications/wizard/Wizard.vue";
// import DataTables from "../views/applications/DataTables.vue";
// import Calendar from "../views/applications/Calendar.vue";
// import Analytics from "../views/applications/analytics/Analytics.vue";
// import EcommerceOverview from "../views/ecommerce/overview/Overview.vue";
// import NewProduct from "../views/ecommerce/products/NewProduct.vue";
// import EditProduct from "../views/ecommerce/EditProduct.vue";
// import ProductPage from "../views/ecommerce/ProductPage.vue";
// import ProductsList from "../views/ecommerce/ProductsList.vue";
// import OrderDetails from "../views/ecommerce/Orders/OrderDetails";
// import OrderList from "../views/ecommerce/Orders/OrderList";
// import Referral from "../views/ecommerce/Referral";
// import Reports from "../views/pages/Users/Reports.vue";
// import NewUserCT from "../views/pages/Users/NewUserCT.vue";
// import Settings from "../views/pages/Account/Settings.vue";
// import Billing from "../views/pages/Account/Billing.vue";
// import Invoice from "../views/pages/Account/Invoice.vue";
// import Security from "../views/pages/Account/Security.vue";
// import Widgets from "../views/pages/Widgets.vue";
// import Basic from "../views/auth/signin/Basic.vue";
// import Cover from "../views/auth/signin/Cover.vue";
import Login from "../views/auth/signin/Login.vue";
// import Illustration from "../views/auth/signin/Illustration.vue";
// import ResetBasic from "../views/auth/reset/Basic.vue";
// import ResetCover from "../views/auth/reset/Cover.vue";
// import SendEmail from "../views/auth/reset/SendEmail.vue";
// import ResetPassword from "../views/auth/reset/ResetPassword.vue";
// import ResetIllustration from "../views/auth/reset/Illustration.vue";
// import VerificationBasic from "../views/auth/verification/Basic.vue";
// import VerificationCover from "../views/auth/verification/Cover.vue";
// import VerificationIllustration from "../views/auth/verification/Illustration.vue";
// import SignupBasic from "../views/auth/signup/Basic.vue";
// import SignupCover from "../views/auth/signup/Cover.vue";
// import Register from "../views/auth/signup/Register.vue";
// import SignupIllustration from "../views/auth/signup/Illustration.vue";
// import Error404 from "../views/auth/error/Error404.vue";
// import Error500 from "../views/auth/error/Error500.vue";
// import lockBasic from "../views/auth/lock/Basic.vue";
// import lockCover from "../views/auth/lock/Cover.vue";
// import lockIllustration from "../views/auth/lock/Illustration.vue";
// import Profile from "../views/examples/Profile.vue";
// import Roles from "../views/examples/Roles/Roles.vue";
// import NewRole from "../views/examples/Roles/NewRole.vue";
// import EditRole from "../views/examples/Roles/EditRole.vue";
// import Users from "../views/examples/Users/Users.vue";
// import NewUser from "../views/examples/Users/NewUser.vue";
// import EditUser from "../views/examples/Users/EditUser.vue";
// import Tags from "../views/examples/Tags/Tags.vue";
// import NewTag from "../views/examples/Tags/NewTag.vue";
// import EditTag from "../views/examples/Tags/EditTag.vue";/me
// import Categories from "../views/examples/Categories/Categories.vue";
// import NewCategory from "../views/examples/Categories/NewCategory.vue";
// import EditCategory from "../views/examples/Categories/EditCategory.vue";
// import Items from "../views/examples/Items/Items.vue";
// import NewItem from "../views/examples/Items/NewItem.vue";
// import EditItem from "../views/examples/Items/EditItem.vue";
// import admin from "../middlewares/admin.js";
// import adminCreator from "../middlewares/admin_creator.js";
// import guest from "../middlewares/guest.js";
// import auth from "../middlewares/auth.js";
import provisionalBill from "../views/provisionalBill.vue";
import notifications from "../views/notifications.vue";
import contactUs from "../views/contactUs.vue";

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/ProvisionalBill/",
  },
  {
    path: "/login/",
    name: "Login",
    component: Login,
  },
  {
    path: "/ProvisionalBill/",
    name: "ProvisionalBill",
    component: provisionalBill,
    meta: { requiresAuth: true }
  },
  {
    path: "/Notifications/",
    name: "Notifications",
    component: notifications,
    meta: { requiresAuth: true }
  },
  {
    path: "/ContactUs/",
    name: "ContactUs",
    component: contactUs,
    meta: { requiresAuth: true }
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// function nextFactory(context, middleware, index) {
//   const subsequentMiddleware = middleware[index];
//   if (!subsequentMiddleware) return context.next;
//
//   return (...parameters) => {
//     context.next(...parameters);
//     const nextMiddleware = nextFactory(context, middleware, index + 1);
//     subsequentMiddleware({ ...context, next: nextMiddleware });
//   };
// }


router.beforeEach((to, from, next) => {
  var isLoggedIn = localStorage.getItem('is_logged_in');
  if (to.meta.requiresAuth) {
    if (!isLoggedIn || isLoggedIn != 'false') {
      next();
    } else {
      next('/login/');
    }
  } else {
    next();
  }
});

export default router;
