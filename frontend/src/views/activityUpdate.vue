<template>
  <b-container class="w-50 items-center">
    <b-card class="card">
      <h3 class="text-center">
        Aggiorno Attivita
      </h3>
      <div class="text-end">
        <b-button type="button" variant="secondary" @click="goBack"
          >Indietro</b-button
        >
      </div>
      <activity-form
        :formData="formData"
        action="update"
        buttonText="Aggiorna"
        @submit="onSubmit"
      />
    </b-card>
  </b-container>
</template>

<script>
import ActivityForm from "../components/acitvityForm";
import Activities from "../services/Activities";

export default {
  name: "UpdateActivity",
  components: { ActivityForm },
  data: () => ({
    formData: {},
  }),
  methods: {
    getActivity() {
      const id = this.$route.params.id;
      Activities.details(id).then((res) => (this.formData = res));
    },
    onSubmit(data) {
      Activities.update(data).then(() => {
        this.$router.push({ name: "activities" });
      });
    },
    goBack() {
      this.$router.push({ name: "activities" });
    },
  },
  created() {
    this.getActivity();
  },
};
</script>
