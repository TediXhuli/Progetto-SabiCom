<template>
  <b-form @submit.prevent="onSubmit">
    <b-form-group class="mb-2" id="input-name" label="Nome *" label-for="name">
      <b-form-input
        id="name"
        v-model="formData.name"
        type="text"
        placeholder="Esempio"
        required
      />
    </b-form-group>
    <b-form-group
      class="mb-4"
      id="input-description"
      label="Descrizione *"
      label-for="description"
    >
      <b-form-textarea
        id="description"
        rows="3"
        v-model="formData.description"
        type="text"
        placeholder="Lorem ipsum dolor sit amet"
        required
      />
    </b-form-group>

    <div>
      <b-row>
        <b-col v-if="action === 'update'">
          <input
            type="checkbox"
            v-model="formData.status"
            name="check-button"
          />
          Completato
        </b-col>
        <b-col class="text-end">
          <b-button class="mx-2" type="submit" variant="primary">{{
            buttonText
          }}</b-button>

          <b-button
            v-b-modal.modal-delete
            v-if="action === 'update'"
            type="button"
            variant="danger"
            >Cancella</b-button
          >
        </b-col>
      </b-row>
    </div>
    <b-modal
      id="modal-delete"
      title="Cancela attivita!"
      class=" bg-danger"
      @ok="deleteActivity"
    >
      <p class="my-4 bg-danger">Sei sicuro di cancelare questa attivita?</p>
    </b-modal>
  </b-form>
</template>

<script>
import Activities from "../services/Activities";

export default {
  name: "ActivityForm",
  props: {
    action: {
      type: String,
      required: true,
    },
    buttonText: {
      type: String,
      required: true,
    },
    formData: {
      type: Object,
      default: () => ({
        name: "",
        description: "",
        status: 0,
      }),
    },
  },
  methods: {
    onSubmit() {
      this.$emit("submit", this.formData);
    },
    deleteActivity() {
      Activities.delete(this.$route.params.id).then(() => {
        this.$router.back();
      });
    },
  },
};
</script>
