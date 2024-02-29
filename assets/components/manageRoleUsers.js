import { createApp } from "vue";
import UserList from "../Vue/Components/UserList.vue";

const buildApp = () => createApp(UserList);

const modal = document.querySelector("#addUserModal");

modal.addEventListener("show.bs.modal", (e) => {
  const button = e.relatedTarget;
  const role = modal.dataset.role ?? button.dataset.role;
  modal.dataset.role = role;
  const app = buildApp();
  app.mount("#addUserToRole");
  e.target.app = app;
});
modal.addEventListener("hide.bs.modal", (e) => {
  e.target.app.unmount();
});

const submitForm = async (url, data) => {
  
};

const removeUserModal = document.querySelector("#removeUserModal");
removeUserModal.addEventListener("show.bs.modal", (e) => {
  const button = e.relatedTarget;
  const role = button.dataset.role;
  const roleName = button.dataset.roleName;
  const target = button.dataset.target;
  const targetName = button.dataset.targetName;
  const form = removeUserModal.querySelector("form");

  removeUserModal.querySelector("#targetName").textContent = targetName;
  removeUserModal.querySelector("#roleName").textContent = roleName;

  const url = `/manage/role/${role}/user`;
  const data = new FormData();
  data.append("role", role);
  data.append("target", target);

  form.addEventListener("submit", (f) => {
    f.preventDefault()
    fetch(url, {
      method: "post",
      body: data,
    }).then((res) => {
      console.log(res)
      if(res.ok){
        document.querySelector(`#user-row-${target}`).remove()
      }
      // throw new Error("Unable to remove user")
    });
  });

});

// const addUserModal = document.getElementById("manageUserModal");
// if (addUserModal) {
//   addUserModal.addEventListener("show.bs.modal", (event) => {
//     const button = event.relatedTarget
//     const role = addUserModal.dataset.role ?? button.dataset.role;
//     const roleName = addUserModal.dataset.roleName ?? button.dataset.roleName;
//     const modalTitle = addUserModal.querySelector(".modal-title")
//     addUserModal.dataset.roleName = roleName
//     addUserModal.dataset.role = role
//     // const modalBodyInput = addUserModal.querySelector(".modal-body input");
//     modalTitle.textContent = `Manage users with the ${roleName} role`;
//     // modalBodyInput.value = role;
//   });
// }
