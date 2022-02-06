import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

const routerView = {
  render(h) {
    return h("router-view");
  },
};

const router = new Router({
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 };
  },
  routes: [
    {
      path: "/login",
      name: "login",
      component: () => import("./views/login"),
    },
    {
      path: "/activities",
      component: routerView,
      children: [
        {
          path: "list",
          name: "activities",
          component: () => import("./views/activities"),
        },
        {
          path: "createActivity",
          name: "createActivity",
          component: () => import("./views/activityCreate"),
        },
        {
          path: "updateActivity/:id",
          name: "updateActivity",
          component: () => import("./views/activityUpdate"),
        },
      ],
    },
    { path: "*", redirect: "/activities/list" },
  ],
});

export default router;
