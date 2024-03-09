"use strict";

(function () {
  class mdEditor extends HTMLElement {
    constructor() {
      super();
      const shadow = this.attachShadow({ mode: "open" });
      const editorContainer = document.createElement("div");
      editorContainer.innerHTML = `
      <label for="mdeditor" class="form-label fw-bold">${this.title}
      <i class="fa-solid fa-asterisk text-danger" data-bs-toggle="tooltip" data-bs-title="Required Field"></i>
  </label>
  <div class="card">
      <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="mdEditorTabs" role="tablist" style="margin-top: -1px; margin-left: -1px;">
              <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="write-tab" data-bs-toggle="tab" data-bs-target="#write-tab-pane" type="button" role="tab" aria-controls="write-tab-pane" aria-selected="true">Write</button>
              </li>
              <li class="nav-item" role="presentation">
                  <button class="nav-link" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-tab-pane" type="button" role="tab" aria-controls="preview-tab-pane" aria-selected="true">Preview</button>
              </li>
          </ul>
      </div>
      <div class="card-body">
          <div class="tab-content" id="mdEditorContent">
              <div class="tab-pane fade show active" id="write-tab-pane" role="tabpanel" aria-labelledby="write-tab" tabindex="0">
                  <textarea class="form-control" name="mdeditor" id="mdeditor" rows="5" required>/textarea>
                  <div class="form-text mt-2">
                      <i class="fa-brands fa-markdown"></i>
                      Markdown is supported!</div>
              </div>
              <div class="tab-pane fade" id="preview-tab-pane" role="tabpanel" aria-labelledby="preview-tab" tabindex="0">
                  Nothing to preview
              </div>
          </div>
      </div>
  </div>`;
      const incident = this.incident;
      const event = this.event;
      const comment = this.comment;
      const uploadUrl = this.uploadUrl;
      if (!uploadUrl) {
        throw new Error("uploadUrl is not specified");
      }
      shadow.appendChild(editorContainer);
    }

    connectedCallback() {
      console.log("Hello world");
    }
    get title() {
      return this.dataset.title || "Comment";
    }
    get incident() {
      return this.dataset.incident;
    }
    get event() {
      return this.dataset.event || null;
    }
    get comment() {
      return this.dataset.comment || null;
    }
    get uploadUrl() {
      return this.dataset.uploadUrl || null;
    }
  }
  customElements.define("md-editor", mdEditor);
})();
