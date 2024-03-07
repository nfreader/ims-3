// assets/app.js
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";
import * as bootstrap from "bootstrap";

const tooltipTriggerList = document.querySelectorAll(
  '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

const toastElList = document.querySelectorAll(".toast.ssr");
const toastList = [...toastElList].map((toastEl) =>
  new bootstrap.Toast(toastEl).show()
);

const popoverTriggerList = document.querySelectorAll(
  '[data-bs-toggle="popover"]'
);
const popoverList = [...popoverTriggerList].map(
  (popoverTriggerEl) =>
    new bootstrap.Popover(popoverTriggerEl, {
      trigger: "click",
      html: true,
    })
);

const anchorTags = document.querySelectorAll("[href]");
const anchorTagList = [...anchorTags].map((a) => {
  const href = a.getAttribute("href");
  if (window.location.pathname.endsWith(href)) {
    a.classList.add("active");
  }
});

async function postAsyncForm(url, data) {
  const response = await fetch(url, {
    method: "POST",
    body: JSON.stringify(Object.fromEntries(data)),
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json"
    }
  });
  return await response.json();
}

const handleFormSubmission = (form) => {
  const indicator = form.querySelector('.indicator') ?? null
  const asyncdata = new FormData(form);
  console.log(asyncdata);
  postAsyncForm(form.getAttribute("action"), asyncdata).then((response) => {
    if(response.error && indicator){
      indicator.classList.remove('visually-hidden')
      indicator.classList.remove('text-success')
      indicator.classList.remove('fa-check')
      indicator.classList.add('text-danger')
      indicator.classList.add('fa-times')
    }
  });
}

const asyncForms = document.querySelectorAll(".form-async");
const asyncFormList = [...asyncForms].map((form) => {
  form.addEventListener("change", (e) => {
    if (form.dataset.autosubmit) {
      handleFormSubmission(form)
    }
  });
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    handleFormSubmission(form)
  });
});

const setBG = () => {
  const body = document.querySelector('body')
  if(body.classList.contains('sudo')) {
    const svgText = `<svg width='128' height='128' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><text x="0" y="128" style="font-family:&quot;Arial&quot;,sans-serif;font-weight:700;font-size:18.534px;opacity:.25;transform:rotate(-45deg)" fill='red'>SUDO MODE</text></svg>`
    const bg = window.getComputedStyle(body).getPropertyValue('background')
    body.setAttribute('style',`background: url(data:image/svg+xml;base64,${btoa(svgText)}) repeat scroll 0% 0% / 384px padding-box border-box, ${bg}`)
  }
}

setBG()
