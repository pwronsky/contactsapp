<template>
  <div>
    <div class="row" style="justify-content: space-between;">
      <div style="width: 20%;">
        <div class="avatar mx-auto">
          <div style="text-transform: uppercase;">{{ initials }}</div>
        </div>
      </div>
      <div style="width: 60%;">
        <div>
          <label class="name lead">{{ contact.first_name }} {{ contact.last_name }}</label>
          <div v-if="contact.phone">
            <span class="fa fa-phone fa-fw text-muted"></span>
            <span class="text-muted small">{{ contact.phone }}</span>
          </div>
          <div>
            <span class="fa fa-envelope fa-fw text-muted"></span>
            <span class="text-muted small text-truncate">{{ contact.email }}</span>
          </div>
        </div>
      </div>
      <div style="margin-right: 10px;">
        <div class="icons-wrapper">
          <i @click="trackClick" title="Track click" class="fas fa-mouse contact-action"></i>
          <i @click="showModal = true" title="Edit contact" class="fas fa-edit contact-action"></i>
          <i @click="remove" title="Remove contact" class="fas fa-trash-alt contact-action"></i>
        </div>
      </div>
    </div>
    <UpdateContactModal
      v-if="showModal"
      @close="showModal = false"
      @contact-updated="onContactUpdated"
      :first-name="contact.first_name"
      :last-name="contact.last_name"
      :phone="contact.phone"
      :email="contact.email"
      :index="index"
      :id="contact.id"
    ></UpdateContactModal>
  </div>
</template>

<script>
import axios from "axios";
import UpdateContactModal from "../../UpdateContactModal/UpdateContactModal";

export default {
  data() {
    return {
      showModal: false,
      loading: false
    }
  },
  props: {
    'index': { type: Number, required: true },
    'contact': { type: Object, required: true }
  },
  computed: {
    initials() {
      let lastNameInitial = '';
      const firstNameInitial = this.contact.first_name.charAt(0);

      if (this.contact.last_name) {
        lastNameInitial = this.contact.last_name.charAt(0);
      }

      return firstNameInitial + lastNameInitial;
    }
  },
  methods: {
    onContactUpdated(newContact) {
      this.showModal = false;
      this.$emit('contact-updated', newContact, this.index);
    },
    async trackClick() {
      try {
        await axios.post(`api/contacts/${this.contact.id}/track`);
        this.$toast.success("Click event tracked successfully");
      } catch (e) {
        this.$toast.error("Something went wrong. Click event was not tracked.");
      }
    },
    async remove() {
      if (this.loading) {
        return;
      }

      this.loading = true;
      try {
        await axios.delete(`api/contacts/${this.contact.id}`);
        this.$toast.success("Contact removed successfully");
        this.$emit('contact-deleted', this.index);
        this.loading = false;
      } catch (e) {
        this.$toast.error("Something went wrong. Contact was not removed.");
        this.loading = false;
      }
    }
  },
  components: {
    UpdateContactModal
  },
  name: "Contact"
}
</script>

<style scoped>
.avatar {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 80px;
  height: 80px;
  background-color: #a98fd6;
  color: #fff;
  border-radius: 100px;
  font-weight: bold;
  font-size: 30px;
}
.icons-wrapper {
  display: flex;
  justify-content: flex-end;
}
.contact-action {
  cursor: pointer;
  color: #6c757d;
  margin-left: 10px;
}
.contact-action:hover {
  color: lightgray;
}
</style>