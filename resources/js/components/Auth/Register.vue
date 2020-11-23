<template>
  <main class="login-form mt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
              <form action="" method="">
                <div class="form-group row">
                  <label for="email" class="col-md-4 col-form-label text-md-right">Name</label>
                  <div class="col-md-6">
                    <input v-model="name" :maxlength="191" type="text" id="name" class="form-control" :class="{'is-invalid': !name}" name="name" autofocus="">
                    <div v-if="!name" class="invalid-feedback">
                      Name is required.
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="email" class="col-md-4 col-form-label text-md-right">Email Address</label>
                  <div class="col-md-6">
                    <input v-model="email" :maxlength="191" type="text" id="email" class="form-control" :class="{'is-invalid': !email || isInvalidEmail}" name="email" autofocus="">
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
                    <input
                      v-model="password"
                      :maxlength="191"
                      :class="{'is-invalid': !password || password.length < 8}"
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                    >
                    <div v-if="!password" class="invalid-feedback">
                      Password is required.
                    </div>
                    <div v-if="password.length < 8" class="invalid-feedback">
                      The password must be at least 8 characters.
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">Re-type password</label>
                  <div class="col-md-6">
                    <input
                      v-model="password_confirmation"
                      :maxlength="191"
                      :class="{'is-invalid': !password_confirmation || password_confirmation !== password}"
                      type="password"
                      id="password_confirmation"
                      class="form-control"
                      name="password_confirmation"
                    >
                    <div v-if="!password_confirmation" class="invalid-feedback">
                      Password confirmation is required.
                    </div>
                    <div v-if="password_confirmation !== password" class="invalid-feedback">
                      Password confirmation and password doesn't match.
                    </div>
                  </div>
                </div>
                <div class="col-md-6 offset-md-4">
                  <button @click.prevent="register" :disabled="!isValidForm || loading" type="submit" class="btn btn-primary">
                    Register
                  </button>
                </div>
              </form>
              <div style="margin-top: 15px;">
                <span>
                  Already have an account? <router-link to="/auth/login">Login</router-link>
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
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        loading: false
      }
    },
    computed: {
      isValidForm() {
        return this.name && this.email && !this.isInvalidEmail && this.password && this.password_confirmation === this.password;
      },
      isInvalidEmail() {
        return !new RegExp(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/).test(this.email);
      }
    },
    methods: {
      async register() {
        this.loading = true;

        try {
          await axios.get('/sanctum/csrf-cookie');
          await axios.post('/register', {
            name: this.name,
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation
          });
          const response = await axios.get('/user');
          localStorage.setItem('authUser', JSON.stringify(response.data));
          await this.$router.push({ name: 'home' });
        } catch (e) {
          this.$toast.error("Registration failed");
        }

        this.loading = false;
      }
    }
  }
</script>
