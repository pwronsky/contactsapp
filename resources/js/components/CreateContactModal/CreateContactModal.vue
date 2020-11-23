<template>
  <div>
    <transition name="modal">
      <div class="modal-mask">
        <div class="modal-wrapper">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add contact</h5>
                <button @click="close" type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  <div class="form-group">
                    <label for="first_name">First name</label>
                    <input v-model="form.first_name" :maxlength="191" type="email" class="form-control" :class="{'is-invalid': !form.first_name}" id="first_name">
                    <div class="invalid-feedback">
                      The first name field is required.
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="last_name">Last name</label>
                    <input v-model="form.last_name" :maxlength="191" type="email" class="form-control" id="last_name">
                  </div>
                  <div class="form-group">
                    <label for="email">Email address</label>
                    <input
                      v-model="form.email"
                      :maxlength="191"
                      :class="{'is-invalid': !form.email || isInvalidEmail}"
                      type="email"
                      class="form-control"
                      id="email"
                    >
                    <div v-if="!form.email" class="invalid-feedback">
                      The email field is required.
                    </div>
                    <div v-if="isInvalidEmail" class="invalid-feedback">
                      Please enter valid email address.
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="phone">Phone number</label>
                    <input v-model="form.phone" :maxlength="20" type="email" class="form-control" id="phone">
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button @click="close" type="button" class="btn btn-secondary">Close</button>
                <button :disabled="!form.first_name || !form.email" @click="saveContact" type="button" class="btn btn-primary">Save contact</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      form: {
        first_name: '',
        last_name: '',
        email: '',
        phone: ''
      }
    }
  },
  computed: {
    isInvalidEmail() {
      return !new RegExp(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/).test(this.form.email);
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    async saveContact() {
      const rawValues = JSON.parse(JSON.stringify(this.form));

      try {
        const response = await axios.post('api/contacts', rawValues);
        this.$toast.success("Contact added successfully");
        this.$emit('contact-created', response.data);
      } catch (e) {
        this.$toast.error("Something went wrong. Contact was not added.");
      }
    }
  },
  name: "CreateContactModal"
}
</script>

<style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}
.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
</style>