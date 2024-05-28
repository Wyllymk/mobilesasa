// CHECKBOX JS
document.getElementById("select_all").addEventListener("change", function (e) {
  let checkboxes = document.querySelectorAll('input[name="send_sms[]"]');
  for (let checkbox of checkboxes) {
    checkbox.checked = e.target.checked;
  }
});

// TAB JS
window.addEventListener("load", function () {
  // Store Tabs variables
  const tabs = document.querySelectorAll("ul.nav-tabs > li");

  // Define the switchTab function
  const switchTab = (event) => {
    event.preventDefault();

    document.querySelector("ul.nav-tabs li.active").classList.remove("active");
    document.querySelector(".tab-pane.active").classList.remove("active");

    const clickedTab = event.currentTarget;
    const anchor = event.target;
    let activePaneID = anchor.getAttribute("href");

    clickedTab.classList.add("active");
    document.querySelector(activePaneID).classList.add("active");

    // Save the active tab to local storage
    localStorage.setItem("activeTab", activePaneID);

    // Update the URL with the current tab
    const url = new URL(window.location);
    url.searchParams.set("tab", activePaneID.substring(1)); // Remove the '#' from the ID
    window.history.replaceState(null, null, url.toString());
  };

  // Attach the event listener to each tab
  for (let i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener("click", switchTab);
  }

  // Check if there's an active tab stored in local storage
  const activeTab = localStorage.getItem("activeTab");

  if (activeTab) {
    document.querySelector("ul.nav-tabs li.active").classList.remove("active");
    document.querySelector(".tab-pane.active").classList.remove("active");

    const activeTabElement = document.querySelector(`ul.nav-tabs a[href="${activeTab}"]`);
    activeTabElement.parentElement.classList.add("active");
    document.querySelector(activeTab).classList.add("active");

    // Update the URL with the active tab
    const url = new URL(window.location);
    url.searchParams.set("tab", activeTab.substring(1)); // Remove the '#' from the ID
    window.history.replaceState(null, null, url.toString());
  }
});

// DATETIME-LOCAL JS
document.addEventListener("DOMContentLoaded", function () {
  const scheduleSmsToggle = document.getElementById("schedule_sms_toggle");
  const scheduleDateLabel = document.getElementById("schedule_date_label");
  const scheduleDateInput = document.getElementById("schedule_date");

  scheduleSmsToggle.addEventListener("change", function () {
    if (scheduleSmsToggle.checked) {
      scheduleDateLabel.style.display = "inline";
      scheduleDateInput.style.display = "inline";
    } else {
      scheduleDateLabel.style.display = "none";
      scheduleDateInput.style.display = "none";
    }
  });
});

// TIME PRE-FILL
// Get the current date and time
var now = new Date();

// Format the date and time in the required format (YYYY-MM-DDTHH:MM)
var year = now.getFullYear();
var month = ("0" + (now.getMonth() + 1)).slice(-2);
var day = ("0" + now.getDate()).slice(-2);
var hours = ("0" + now.getHours()).slice(-2);
var minutes = ("0" + now.getMinutes()).slice(-2);
var currentDateTime = year + "-" + month + "-" + day + "T" + hours + ":" + minutes;

// Set the value of the input element to the current date and time
document.getElementById("schedule_date").value = currentDateTime;
