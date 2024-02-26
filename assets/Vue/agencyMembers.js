import { createApp } from "vue";
import AgencyMembers from "./Components/AgencyMembers.vue";

const buildApp = () => createApp(AgencyMembers);

const modal = document.querySelector("#addMemberModal");

modal.addEventListener("show.bs.modal", (e) => {
  const app = buildApp();
  app.mount("#addMemberToAgency");
  e.target.app = app;
});
modal.addEventListener("hide.bs.modal", (e) => {
  e.target.app.unmount();
});
