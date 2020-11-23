<template>
  <main class="login-form mt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
              <form>
                <div class="form-group row">
                  <label for="email" class="col-md-4 col-form-label text-md-right">Email Address</label>
                  <div class="col-md-6">
                    <input v-model="email" type="text" id="email" class="form-control" :class="{'is-invalid': !email || isInvalidEmail}" name="email" autofocus="">
                    <div v-if="!email" class="invalid-feedback">
                      Email address is required.
                    </div>
                    <div v-if="isInvalidEmail" class="invalid-feedback">
                      Please enter valid email address.
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                  <div class="col-md-6">
                    <input v-model="password" :class="{'is-invalid': !password}" type="password" id="password" class="form-control" name="password">
                    <div class="invalid-feedback">
                      Password is required.
                    </div>
                  </div>
                </div>
                <div class="col-md-6 offset-md-4">
                  <button @click.prevent="login" :disabled="!email || !password || loading" type="submit" class="btn btn-primary">
                    Login
                  </button>
                </div>
              </form>
              <div style="margin-top: 15px;">
                <span>
                  No account yet? <router-link to="/auth/register">Register</router-link>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
  import axios from "axios";

  export default {
    name: 'Login',
    data() {
      return {
        email: '',
        password: '',
        loading: false
      }
    },
    computed: {
      isInvalidEmail() {
        return !new RegExp(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/).test(this.email);
      }
    },
    methods: {
      async login() {
        this.loading = true;

        try {
          await axios.get('/sanctum/csrf-cookie');
          await axios.post('/login', { email: this.email, password: this.password });
          const response = await axios.get('/user');
          localStorage.setItem('authUser', JSON.stringify(response.data));
          this.$toast.success('Logged successfully')
          await this.$router.push({ name: 'home' });
        } catch (e) {
          this.$toast.error("Login attempt failed");
        }

        this.loading = false;
      }
    }
  }
</script>
