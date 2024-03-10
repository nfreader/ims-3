"use-strict";
// const tempalte = document.getElementById('mdeditor-template')

// class MDEditor extends HTMLElement {
//   constructor() {
//     super()
//   }
//   connectedCallback(){
//     this.innerHTML = `<h1>Hello World</h1>`
//   }
// }

// customElements.define('md-editor',MDEditor)

const mdeditorTargets = document.querySelectorAll("#mdeditor");
const mdeditors = [...mdeditorTargets].map((target) => {
  target.addEventListener("dragover", (e) => {
    e.preventDefault();
    e.stopPropagation();
    target.classList.add("targeted");
  });
  target.addEventListener("drop", (e) => {
    e.preventDefault();
    e.stopPropagation();
    target.classList.remove("targeted");
    const files = e.dataTransfer.files;
    var data = new FormData();
    data.append('incident',target.dataset.incident)
    data.append('event',target.dataset.event ?? null)
    data.append('comment',target.dataset.comment ?? null)
    for (var i = 0; i < files.length; i++) {
      data.append("files[]", files[i], files[i].name);
      var uploadingString = `[Attaching ${files[i].name}...](Attaching ${files[i].name}...)`;
      target.value += uploadingString;
    }
    uploadAttachments(data, target);
  });

  target.addEventListener("paste", function (event) {
    const clipboardData = event.clipboardData || window.clipboardData;
    const items = clipboardData.items;
    var data = new FormData();
    for (let i = 0; i < items.length; i++) {
      if (items[i].kind === "file") {
        const file = items[i].getAsFile();
        data.append("files[]", file, file.name);
        var uploadingString = `[Attaching ${file.name}...](Attaching ${file.name}...)`;
        target.value += uploadingString;
      }
    }
    uploadAttachments(data, target);
  });
});

async function uploadAttachments(data, target) {
  const response = await fetch("/attachment/new", {
    method: "POST",
    body: data,
    headers: {
      "Accept": "application/json",
    },
  });
  const attachments = await response.json();
  attachments.forEach((a) => {
    const searchString = `[Attaching ${a.originalName}...](Attaching ${a.originalName}...)`;
    const image = a.mimeType.startsWith("image/") ? "!" : "";
    const insertString = `${image}[${a.originalName}](/uploads/${a.file})`;
    target.value = target.value.replace(searchString, insertString);
  });
}

const commentEditors = document.querySelectorAll(".commentEditor");
const commentEditorList = [...commentEditors].map((f) => {
  f.addEventListener("reset", (e) => {
    e.target.classList.add("visually-hidden");
    document
      .querySelector(`#comment-${e.target.dataset.commentId}-content`)
      .classList.remove("visually-hidden");
  });
});

const commentEditToggles = document.querySelectorAll(".toggleCommentEditor");
const commentEditTogglesList = [...commentEditToggles].map((t) => {
  t.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelector(`#comment-${t.dataset.commentTarget}-content`)
      .classList.add("visually-hidden");
    document
      .querySelector(`#comment-${t.dataset.commentTarget}-editor`)
      .classList.remove("visually-hidden");
  });
});
