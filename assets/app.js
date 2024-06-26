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
import { intlFormatDistance, parseJSON } from "date-fns";
import { utcToZonedTime, getTimezoneOffset } from 'date-fns-tz';

const timeElements = document.querySelectorAll('time')
const timestamps =  [...timeElements].map((e) => {
  const time = new Date(e.textContent)
  e.setAttribute('title', time.toLocaleString())
  e.setAttribute('data-bs-toggle', 'tooltip')
  e.textContent = utcToZonedTime(time)
  e.textContent = intlFormatDistance(
    utcToZonedTime(
      time,
      Intl.DateTimeFormat().resolvedOptions().timeZone),
    new Date())
})

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

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

async function postAsyncForm(url, data) {
  const response = await fetch(url, {
    method: "POST",
    body: JSON.stringify(Object.fromEntries(data)),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  });
  return await response.json();
}

const handleFormSubmission = (form) => {
  const indicator = form.querySelector(".indicator") ?? null;
  const asyncdata = new FormData(form);
  console.log(asyncdata);
  postAsyncForm(form.getAttribute("action"), asyncdata).then((response) => {
    if (response.error && indicator) {
      indicator.classList.remove("visually-hidden");
      indicator.classList.remove("text-success");
      indicator.classList.remove("fa-check");
      indicator.classList.add("text-danger");
      indicator.classList.add("fa-times");
    }
  });
};

const asyncForms = document.querySelectorAll(".form-async");
const asyncFormList = [...asyncForms].map((form) => {
  form.addEventListener("change", (e) => {
    if (form.dataset.autosubmit) {
      handleFormSubmission(form);
    }
  });
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    handleFormSubmission(form);
  });
});

const form = document.querySelector("#sudoMode");
form.addEventListener("submit", function (e) {
  e.preventDefault();
  fetch("/user/sudo", { method: "POST" });
  setTimeout(() => {
    window.location.reload();
  }, 100);
});

const markdownLinks = document.querySelectorAll('.comment-content a')
const linksToIconify = [...markdownLinks].map(function(e){
  if(!e.getAttribute('href').startsWith(window.location.origin)){
    e.classList.add("external-link")
    try {
      let host = new URL(e.getAttribute('href'))
      e.setAttribute("style",
      `--url: url("https://v1.indieweb-avatar.11ty.dev/${encodeURIComponent(
          `${host?.protocol}//${host?.hostname}`,
      )}")`,)
    } catch {}
  }
})