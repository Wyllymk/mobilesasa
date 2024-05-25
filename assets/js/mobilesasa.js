document.getElementById("select_all").addEventListener("change", function (e) {
  let checkboxes = document.querySelectorAll('input[name="send_sms[]"]');
  for (let checkbox of checkboxes) {
    checkbox.checked = e.target.checked;
  }
});

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
