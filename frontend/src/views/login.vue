<template>
  <b-container>
    <b-card class="card">
      <h3 class="text-center">
        Login
      </h3>
      <b-form @submit.prevent="onSubmit">
        <b-form-group
          class="mb-4"
          id="input-email"
          label="Email address:"
          label-for="email"
        >
          <b-form-input
            id="email"
            v-model="email"
            type="email"
            placeholder="Email"
            required
          ></b-form-input>
        </b-form-group>
        <b-form-group
          class="mb-4"
          id="input-password"
          label="Password:"
          label-for="password"
        >
          <b-form-input
            id="password"
            v-model="password"
            type="password"
            placeholder="Password"
            required
          ></b-form-input>
        </b-form-group>
        <div class="text-end">
          <b-button type="submit" variant="primary">Submit</b-button>
        </div>
      </b-form>
    </b-card>
  </b-container>
</template>

<script>
import Auth from "../services/Auth";

export default {
  name: "Login",
  data: () => ({
    email: "",
    password: "",
  }),
  methods: {
    onSubmit() {
      Auth.login({
        email: this.email,
        password: this.password,
      }).then((res) => {
        console.log(res);
        localStorage.setItem("access_token", res.token);
        this.$router.replace({
          name: "activities",
          params: { userId: res.userId },
        });
      });
    },
  },
};
</script>

<style scoped>
.card {
  box-shadow: 1px 1px 1px;
  padding: 16px;
  max-width: 30rem;
  margin: auto;
}
</style>
