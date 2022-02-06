import apiFactory from "../factories/ApiFactory";

export default {
  list() {
    return new Promise((resolve, reject) => {
      apiFactory.get("/activities").then(
        ({ data }) => resolve(data),
        (error) => reject(error)
      );
    });
  },
  details(id) {
    return new Promise((resolve, reject) => {
      apiFactory.get("/activities/" + id).then(
        ({ data }) => resolve(data),
        (error) => reject(error)
      );
    });
  },
  create(payload) {
    return new Promise((resolve, reject) => {
      apiFactory.post("/activities", payload).then(
        ({ data }) => resolve(data),
        (error) => reject(error)
      );
    });
  },
  update(payload) {
    return new Promise((resolve, reject) => {
      apiFactory.patch("/activities/" + payload.id, payload).then(
        ({ data }) => resolve(data),
        (error) => reject(error)
      );
    });
  },
  delete(id) {
    return new Promise((resolve, reject) => {
      apiFactory.delete("/activities/" + id).then(
        ({ data }) => resolve(data),
        (error) => reject(error)
      );
    });
  },
};
