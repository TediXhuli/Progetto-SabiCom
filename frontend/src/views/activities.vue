<template>
  <div>
    <b-container class="text-center">
      <h3>Le tue attivita</h3>
      <div class="text-end mb-3">
        <b-button
          class="mx-2"
          variant="primary"
          size="sm"
          @click="createActivity"
        >
          <b-icon icon="plus"></b-icon>
          Crea nuova attivita
        </b-button>
        <b-button v-b-modal.modal-logOut variant="secondary" size="sm">
          <b-icon icon="box-arrow-left"></b-icon>
          Logout
        </b-button>
      </div>
      <b-table
        class="table"
        bordered
        :busy="loading"
        :fields="fields"
        :items="activities"
        :tbody-tr-class="rowClass"
      >
        <template #cell(index)="data">
          {{ data.index + 1 }}
        </template>
        <template #cell(status)="{item}">
          {{ !item.status ? "da completare" : "completato" }}
        </template>

        <template #cell(actions)="{item}">
          <div class="text-center">
            <b-button
              v-b-modal.modal-confirm
              class="mx-1"
              size="sm"
              type="button"
              :variant="
                item.deleted_at !== null || item.status
                  ? 'secondary'
                  : 'success'
              "
              :disabled="item.deleted_at !== null || item.status"
              @click="selectedItem = item"
            >
              <b-icon icon="check-square"></b-icon>
            </b-button>
            <b-button
              class="mx-1"
              size="sm"
              type="button"
              :variant="item.deleted_at !== null ? 'secondary' : 'primary'"
              :disabled="item.deleted_at !== null"
              @click="updateActivity(item)"
            >
              <b-icon icon="pencil"></b-icon>
            </b-button>
            <b-button
              v-b-modal.modal-delete
              class="mx-1"
              size="sm"
              type="button"
              :variant="item.deleted_at !== null ? 'secondary' : 'danger'"
              :disabled="item.deleted_at !== null"
              @click="selectedItem = item"
            >
              <b-icon icon="trash"></b-icon>
            </b-button>
          </div>
        </template>
        <template #table-busy>
          <div class="text-center text-info my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
      </b-table>
    </b-container>
    <b-modal id="modal-confirm" title="Confirm Activity" @ok="confirmActivity">
      <p class="my-4">Sei sicuro di completare questa attivita?</p>
    </b-modal>
    <b-modal id="modal-delete" title="Delete Activity" @ok="deleteActivity">
      <p class="my-4">Sei sicuro di cancelare questa attivita?</p>
    </b-modal>
    <b-modal id="modal-logOut" title="Delete Activity" @ok="logOut">
      <p class="my-4">Sei sicuro di uscire?</p>
    </b-modal>
  </div>
</template>

<script>
import Activities from "../services/Activities";

export default {
  name: "Activities",
  data: () => ({
    fields: [
      { key: "index", label: "#" },
      { key: "name", label: "Nome" },
      { key: "description", label: "Descrizione" },
      { key: "status", label: "Completato" },
      { key: "actions", label: "", class: "text-end" },
    ],
    activities: [],
    selectedItem: {},
    loading: false,
  }),
  methods: {
    rowClass(activities) {
      if (activities.deleted_at !== null) return "table-danger";
      if (activities.status) return "table-primary";
    },
    getActivities() {
      this.loading = true;
      Activities.list()
        .then((res) => (this.activities = res))
        .finally(() => {
          this.loading = false;
        });
    },
    confirmActivity() {
      this.selectedItem.status = true;
      Activities.update(this.selectedItem).then(() => {
        this.selectedItem = {};
        this.getActivities();
      });
    },
    createActivity() {
      this.$router.push({
        name: "createActivity",
      });
    },
    updateActivity(data) {
      this.$router.push({
        name: "updateActivity",
        params: { id: data.id },
      });
    },
    deleteActivity() {
      Activities.delete(this.selectedItem.id).then(() => {
        this.selectedItem = {};
        this.getActivities();
      });
    },
    logOut() {
      localStorage.removeItem("access_token");
      this.$router.push({
        name: "login",
      });
    },
  },
  created() {
    this.getActivities();
  },
};
</script>

<style></style>
