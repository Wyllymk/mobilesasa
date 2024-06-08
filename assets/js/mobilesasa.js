// CHECKBOX JS
jQuery(document).ready(function ($) {
  $("#select_all").on("change", function () {
    let checkboxes = $('input[name="send_sms[]"]');
    checkboxes.prop("checked", $(this).prop("checked"));
  });

  // TAB JS
  const tabs = $("ul.nav-tabs > li");

  // Check if the page was reloaded
  const isPageReloaded = performance.navigation.type === performance.navigation.TYPE_RELOAD;

  const switchTab = (event) => {
    event.preventDefault();

    $("ul.nav-tabs li.active").removeClass("active");
    $(".tab-pane.active").removeClass("active");

    const clickedTab = $(event.currentTarget);
    const anchor = clickedTab.find("a");
    const activePaneID = anchor.attr("href");

    clickedTab.addClass("active");
    $(activePaneID).addClass("active");

    localStorage.setItem("activeTab", activePaneID);

    const url = new URL(window.location);
    url.searchParams.set("tab", activePaneID.substring(1)); // Remove the '#' from the ID
    window.history.replaceState(null, null, url.toString());
  };

  tabs.on("click", switchTab);

  const activeTab = localStorage.getItem("activeTab");

  if (activeTab) {
    $("ul.nav-tabs li.active").removeClass("active");
    $(".tab-pane.active").removeClass("active");

    const activeTabElement = $(`ul.nav-tabs a[href="${activeTab}"]`);
    activeTabElement.parent().addClass("active");
    $(activeTab).addClass("active");

    const url = new URL(window.location);
    url.searchParams.set("tab", activeTab.substring(1)); // Remove the '#' from the ID
    window.history.replaceState(null, null, url.toString());
  }

  // Clear the active tab state when navigating away from the page, but not on reload
  window.addEventListener("beforeunload", (event) => {
    // Check if the navigation is not a reload
    if (!isPageReloaded) {
      localStorage.removeItem("activeTab");
    }
  });

  // Ensure the form action URL includes the current tab
  $("form").on("submit", function () {
    const activeTab = localStorage.getItem("activeTab");
    if (activeTab) {
      const form = $(this);
      const actionUrl = new URL(form.attr("action"));
      actionUrl.searchParams.set("tab", activeTab.substring(1)); // Remove the '#' from the ID
      form.attr("action", actionUrl.toString());
    }
  });

  // DATETIME-LOCAL JS
  const scheduleSmsToggle = $("#schedule_sms_toggle");
  const scheduleDateLabel = $("#schedule_date_label");
  const scheduleDateInput = $("#schedule_date");

  scheduleSmsToggle.on("change", function () {
    if (scheduleSmsToggle.prop("checked")) {
      scheduleDateLabel.show();
      scheduleDateInput.show();
    } else {
      scheduleDateLabel.hide();
      scheduleDateInput.hide();
    }
  });

  // TIME PRE-FILL
  const now = new Date();
  const year = now.getFullYear();
  const month = ("0" + (now.getMonth() + 1)).slice(-2);
  const day = ("0" + now.getDate()).slice(-2);
  const hours = ("0" + now.getHours()).slice(-2);
  const minutes = ("0" + now.getMinutes()).slice(-2);
  const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

  $("#schedule_date").val(currentDateTime);

  // RECIPIENTS JS
  window.toggleRecipient = function (id) {
    const recipientsDiv = $(`#recipients-${id}`);
    recipientsDiv.toggle();
  };

  window.toggleRecipients = function (id, type) {
    const recipientsDiv = $(`#recipients-${id}-${type}`);
    recipientsDiv.toggle();
  };

  // DELIVERY STATUS CHECK
  $(".delivery-status-btn").on("click", function (e) {
    e.preventDefault();

    var button = $(this);
    var row = button.closest("tr");
    var messageId = row.data("message-id");

    button.prop("disabled", true);
    button.text("Checking...");

    $.ajax({
      url: mobilesasa.ajaxurl,
      type: "POST",
      data: {
        action: "delivery_status_sms",
        message_id: messageId,
        _ajax_nonce: mobilesasa.nonce_delivery_status,
      },
      success: function (response) {
        if (response.success) {
          alert(response.data.message);
          location.reload(); // Reload the page to show updated statuses
        } else {
          alert("Failed to check delivery status.");
        }
        button.prop("disabled", false);
        button.text("Check Status");
      },
      error: function () {
        alert("Failed to check delivery status.");
        button.prop("disabled", false);
        button.text("Check Status");
      },
    });
  });

  // SEND SMS JS
  $("#send_sms_button").on("click", function (e) {
    e.preventDefault();

    let button = $(this);
    button.prop("disabled", true);
    button.text("Sending...");

    var recipients = []; // Initialize an empty array for recipients
    $("input[name='send_sms[]']:checked").each(function () {
      recipients.push($(this).val()); // Push each checked value into the array
    });
    var scheduleSms = $("#schedule_sms_toggle").prop("checked") ? "yes" : "no";
    var scheduleTime = $("#schedule_date").val();

    $.ajax({
      url: mobilesasa.ajaxurl,
      type: "POST",
      data: {
        action: "send_sms",
        recipients: recipients,
        schedule_sms: scheduleSms,
        schedule_time: scheduleTime,
        _ajax_nonce: mobilesasa.nonce_send_sms,
      },
      success: function (response) {
        if (response.success) {
          alert(response.data.message);
          // Optionally, you can reset the form after successful submission
          $("#send-sms-form")[0].reset();
        } else {
          alert("Failed to send SMS.");
        }
        button.prop("disabled", false);
        button.text("Send SMS");
      },
      error: function () {
        alert("Failed to send SMS.");
        button.prop("disabled", false);
        button.text("Send SMS");
      },
    });
  });

  // Function to handle deleting scheduled message

  $(".delete-btn").on("click", function () {
    if (confirm("Are you sure you want to delete this scheduled message?")) {
      var messageId = $(this).data("message-id");

      $.ajax({
        url: mobilesasa.ajaxurl,
        type: "POST",
        data: {
          action: "delete_scheduled_message",
          message_id: messageId,
          _ajax_nonce: mobilesasa.nonce_delete_message,
        },
        success: function (response) {
          if (response.success) {
            alert(response.data.message);
            // Remove the row from the table
            $('tr[data-message-id="' + messageId + '"]').remove();
          } else {
            alert("Failed to delete scheduled message.");
          }
        },
        error: function () {
          alert("Failed to delete scheduled message.");
        },
      });
    }
  });

  // Function to hide the alerts after a time limit
  const hideAlerts = () => {
    $(".notice-mobilesasa").fadeOut(500, function () {
      $(this).remove();
    });
  };

  // Hide the alerts after a time limit (e.g., 10 seconds)
  setTimeout(hideAlerts, 10000); // Adjust the time limit as needed

  // Function to hide alerts on clicking another tab
  const hideAlertsOnTabClick = () => {
    $("ul.nav-tabs > li > a").on("click", function () {
      hideAlerts();
    });
  };

  // Call the function to hide alerts on clicking another tab
  hideAlertsOnTabClick();
});
