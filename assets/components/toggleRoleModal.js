const disableModal = document.getElementById("disableRoleModal");
if (disableModal) {
  disableModal.addEventListener("show.bs.modal", (event) => {
    const button = event.relatedTarget;
    const role = button.dataset.role;
    const roleName = button.dataset.roleName;
    const active = button.dataset.roleActive != "" ?? null;
    const modalTitle = disableModal.querySelector(".modal-title");
    const modalBodyInput = disableModal.querySelector(".modal-body input");
    const modalHeader = disableModal.querySelector(".modal-header");
    const submitBtn = disableModal.querySelector("[type=submit]");
    disableModal
      .querySelector("#enableText")
      .classList.remove("visually-hidden");
    disableModal.querySelector("#disableText").classList.add("visually-hidden");
    modalTitle.textContent = `Disable the ${roleName} role?`;
    if (!active) {
      modalHeader.classList.remove("text-bg-danger");
      modalHeader.classList.add("text-bg-primary");
      modalTitle.textContent = `Enable the ${roleName} role?`;
      submitBtn.classList.remove("btn-danger");
      submitBtn.classList.add("btn-primary");
      submitBtn.textContent = `Enable Role`;
    }
    modalBodyInput.value = role;
  });
}
