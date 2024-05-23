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
  };

  // Attach the event listener to each tab
  for (let i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener("click", switchTab);
  }
});
document.getElementById("select_all").addEventListener("change", function (e) {
  let checkboxes = document.querySelectorAll('input[name="send_sms[]"]');
  for (let checkbox of checkboxes) {
    checkbox.checked = e.target.checked;
  }
});
