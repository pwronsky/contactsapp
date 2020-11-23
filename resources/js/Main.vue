<template>
  <div>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li v-if="isLoggedIn" class="nav-item" style="margin-right: 30px;">
            <a @click.prevent="logout" class="nav-link" href="#">Logout</a>
          </li>
          <li v-if="!isLoggedIn" class="nav-item" style="margin-right: 30px;">
            <router-link to="/auth/register" class="nav-link">Register</router-link>
          </li>
          <li v-if="!isLoggedIn" class="nav-item" style="margin-right: 30px;">
            <router-link to="/auth/login" class="nav-link">Login</router-link>
          </li>
        </ul>
      </div>
    </nav>
    <router-view></router-view>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: 'Main',
  data() {
    return {
      isLoggedIn: false
    }
  },
  methods: {
    async logout() {
      try {
        await axios.post('/logout');
        this.isLoggedIn = false;
        localStorage.removeItem('authUser');
        await this.$router.push({name : 'login'})
        this.$toast.success("You have been successfully logged out!")
      } catch (e) {
        console.error(e);
      }
    }
  },
  created() {
    this.isLoggedIn = !!localStorage.getItem('authUser');
  },
  beforeUpdate() {
    this.isLoggedIn = !!localStorage.getItem('authUser');
  }
}
</script>