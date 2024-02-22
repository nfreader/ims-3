import * as bootstrap from "bootstrap";

async function getGlobalNav() {
  const response = await fetch("/incident/listing");
  return await response.json();
}

async function setActiveAgency(agency){
  const response = await fetch("/user/pickAgency",{
    method: 'POST',
    body: JSON.stringify({'agency': agency}),
    headers: {
      "Content-Type": "application/json",
    },
  })
  return await response.json()
}

const url = window.location;

var currentIncident = null;
var currentEvent = null;

const currentData = url.pathname.match(
  /\/((incident)\/([0-9]+))(\/(event)\/([0-9]+))?/
);

if (currentData) {
  currentIncident = currentData[3];
  if (undefined != currentData[6]) {
    currentEvent = currentData[6];
  }
}

const incidentSelector = document.getElementById("incidentGlobal");
const eventSelector = document.getElementById("eventGlobal");
const globalNav = document.getElementById("globalNav");

globalNav.addEventListener("change", (e) => {
  const formData = new FormData(globalNav);
  var targetIncident = formData.get("incident");
  var targetEvent = formData.get("event");
  if (targetEvent && targetIncident == currentIncident) {
    window.location.assign(
      `${url.origin}/incident/${targetIncident}/event/${targetEvent}`
    );
  } else {
    window.location.assign(`${url.origin}/incident/${targetIncident}`);
  }
});

await getGlobalNav().then((data) => {
  data.incidents.forEach((i) => {
    var current = i.id == currentIncident;
    var opt = new Option(i.name, i.id, false, current);
    incidentSelector.appendChild(opt);
  });
  data.events.forEach((e) => {
    if (e.incident == currentIncident) {
      var current = e.id == currentEvent;
      var opt = new Option(e.title, e.id, false, current);
      eventSelector.appendChild(opt);
    }
  });
});

const agencyTargets = document.querySelectorAll('.agencyTarget')
const agencyChoosers = document.querySelectorAll('.agencyChooser')
const foundAgencyChoosers = [...agencyChoosers].map((chooser) => {
  const agencyOptions = chooser.querySelectorAll('.agency-badge')
  const optionList = [...agencyOptions].map((agency) => {
    agency.addEventListener('click', (e) => {
      const agencyId = agency.dataset.agencyId ?? -1
      e.preventDefault()
      for(const target of agencyTargets){
        const newElement = agency.cloneNode(true)
        newElement.classList.remove('agency-badge')
        target.replaceChildren(newElement)
      }
      const update = async () => {
        await setActiveAgency(agencyId).then((data) => {
          const myToastEl = document.getElementById('notification') 
          const myToast = new bootstrap.Toast(myToastEl)
          myToastEl.querySelector('.toast-body').textContent = "Your active agency has been updated"
          myToastEl.classList.add("text-bg-success")
          myToast.show()
        })
      }
      update()
    })
  })
})
