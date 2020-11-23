<template>
  <div class="main-wrapper">
    <div class="list-wrapper">
      <div v-if="loading" class="spinner-border loader" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <div class="card card-default" id="card_contacts">
        <div id="contacts" class="panel-collapse collapse show" aria-expanded="true">
          <ul class="list-group pull-down" id="contact-list">
            <li v-if="contacts.length > 0" class="list-group-item" v-for="(contact, index) in contacts">
              <Contact
                :contact="contact"
                :index="index"
                @contact-updated="onContactUpdated"
                @contact-deleted="onContactDeleted"
              ></Contact>
            </li>
            <li v-if="contacts.length === 0 && !loading" class="list-group-item">
              <span>No contacts</span>
            </li>
          </ul>
        </div>
      </div>
      <div v-if="lastPage !== 1" style="margin-top: 10px;">
        <Pagination v-model="page" :per-page="6" :records="totalContacts" :options="{chunk: 5}" @paginate="getContacts"></Pagination>
      </div>
    </div>
    <CreateContactModal v-if="showModal" @contact-created="onContactCreated" @close="showModal = false"></CreateContactModal>
    <div class="buttons-wrapper">
      <button @click="showModal = true" type="button" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add contact</button>
      <input @change="uploadFile" ref="file" :multiple="false" type="file" id="file" style="display:none;" accept=".csv"/>
      <button @click="openFileManager" style="margin-top: 10px;" type="button" class="btn btn-outline-primary"><i class="fas fa-upload"></i> Import (CSV only)</button>
    </div>
  </div>

</template>

<script>
import axios from "axios";
import Pagination from "vue-pagination-2";
import Contact from "./Contact/Contact";
import CreateContactModal from "../CreateContactModal/CreateContactModal";

export default {
  name: "ContactsList",
  data() {
    return {
      page: 1,
      totalContacts: 0,
      lastPage: 1,
      perPage: 6,
      loading: false,
      contacts: [],
      showModal: false
    }
  },
  methods: {
    onContactDeleted() {
      this.page = 1;
      this.getContacts();
    },
    onContactCreated() {
      this.showModal = false;
      this.page = 1;
      this.getContacts();
    },
    onContactUpdated(newContact, index) {
      this.contacts[index] = newContact;
      this.contacts = [...this.contacts];
    },
    async getContacts() {
      this.loading = true;
      try {
        const response = await axios.get(`api/contacts?page=${this.page}`);
        this.contacts = response.data.data;
        this.totalContacts = response.data.total;
        this.perPage = response.data.per_page;
        this.lastPage = response.data.last_page;
        this.loading = false;
      } catch (e) {
        console.error(e);
      }
    },
    openFileManager() {
      this.$refs.file.click();
    },
    async uploadFile(event) {
      const file = event.target.files[0];
      event.target.value = null;

      if ('text/csv' === file.type) {
        const formData = new FormData();
        formData.append('file', file);

        try {
          await axios.post('api/contacts/import', formData);
          this.$toast.success("Contacts imported");
          await this.getContacts();
        } catch (e) {
          if (e.response && e.response.status === 422) {
            this.$toast.error('Uploaded file is not valid');
            return;
          }

          this.$toast.error('Something went wrong');
        }
      } else {
        this.$toast.warning('Only CSV files are accepted');
      }
    }
  },
  created() {
    this.getContacts();
  },
  components: {
    Contact, CreateContactModal, Pagination
  }
}
</script>

<style scoped>
  .loader {
    position: fixed;
    width: 50px;
    height: 50px;
    top: calc(50% - 25px);
    left: calc(50% - 25px);
  }
  .main-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 40px;
    padding-bottom: 50px;
  }
  .list-wrapper {
    min-width: 450px;
  }
  .buttons-wrapper {
    margin-left: 20px;
    display: flex;
    flex-direction: column;
  }
  @media (max-width: 768px) {
    .main-wrapper {
      flex-direction: column;
    }
    .list-wrapper {
      padding-left: 20px;
      padding-right: 20px;
    }
    .buttons-wrapper {
      margin-top: 20px;
      margin-bottom: 40px;
      padding-left: 20px;
      padding-right: 40px;
    }
  }
</style>