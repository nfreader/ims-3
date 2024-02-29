import { createApp } from "vue";
import UserList from "../Vue/Components/UserList.vue";

const buildApp = () => createApp(UserList);

const modal = document.querySelector("#addUserModal");

modal.addEventListener("show.bs.modal", (e) => {
  const app = buildApp();
  app.mount("#addUserToRole");
  e.target.app = app;
});
modal.addEventListener("hide.bs.modal", (e) => {
  e.target.app.unmount();
});
