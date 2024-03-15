const BORDER_SIZE = 8;
const panel = document.querySelector("#panel");
const sidebar = document.querySelector("#sidebar");
const sidebar2 = document.querySelector("#sidebar2");
const sidebar_width = parseInt(getComputedStyle(sidebar).width);
const body = document.querySelector("body");

const getStoredWidth = () => localStorage.getItem("2swidth");
const storeWidth = (width) => localStorage.setItem("2swidth", width);
if (getStoredWidth()) {
  sidebar.style.width = getStoredWidth() + "px";
  if (sidebar2) {
    sidebar2.style.width = getStoredWidth() + "px";
  }
}

let m_pos;

function resize(e) {
  body.classList.add("user-select-none");
  const dx = m_pos - e.x;
  m_pos = e.x;
  if (sidebar2) {
    sidebar.style.width = e.x / 2 + "px";
    sidebar2.style.width = e.x / 2 + "px";
  } else {
    sidebar.style.width = e.x + "px";
  }
}

panel.addEventListener(
  "mousedown",
  function (e) {
    if (e.offsetX < BORDER_SIZE) {
      m_pos = e.x;
      document.addEventListener("mousemove", resize, false);
    }
  },
  false
);

document.addEventListener(
  "mouseup",
  function (e) {
    document.removeEventListener("mousemove", resize, false);
    body.classList.remove("user-select-none");
    storeWidth(parseInt(sidebar.style.width));
  },
  false
);
