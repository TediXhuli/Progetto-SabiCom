<template>
  <b-container class="w-50 items-center">
    <b-card class="card">
      <h3 class="text-center">
        Crea nuovo attivita
      </h3>
      <div class="text-end">
        <b-button type="button" variant="secondary" @click="goBack"
          >Indietro</b-button
        >
      </div>

      <activity-form action="create" buttonText="Crea" @submit="onSubmit" />
    </b-card>
  </b-container>
</template>

<script>
import ActivityForm from "../components/acitvityForm";
import Activities from "../services/Activities";

export default {
  name: "CreateActivity",
  components: { ActivityForm },
  data: () => ({}),
  methods: {
    onSubmit(data) {
      Activities.create(data).then(() => {
        this.$router.push({ name: "activities" });
      });
    },
    goBack() {
      this.$router.push({ name: "activities" });
    },
  },
};
</script>
