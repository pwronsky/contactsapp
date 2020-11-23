import VueRouter from "vue-router";
import ContactsList from "./components/ContactsList/ContactsList";
import Login from "./components/Auth/Login";
import Register from "./components/Auth/Register";

const routes = [
  {
    path: '/',
    component: ContactsList,
    name: 'home'
  },
  {
    path: '/auth/login',
    component: Login,
    name: 'login'
  },
  {
    path: '/auth/register',
    component: Register,
    name: 'register'
  }
];

const router = new VueRouter({routes, mode: 'history'});

router.beforeEach((to, from, next) => {
  const authUser = localStorage.getItem('authUser');

  if ((to.name === 'login' || to.name === 'register') && authUser) {
    next({ name: 'home' });
    return;
  }

  if ((to.name !== 'login' && to.name !== 'register') && !authUser) {
    next({ name: 'login' });
    return;
  }

  next();
})

export default router;