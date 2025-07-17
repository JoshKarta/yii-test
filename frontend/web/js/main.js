$(document).on("click", ".notification-link", function (e) {
  e.preventDefault();

  const id = $(this).data("id");
  const link = $(this).data("link");

  if (!id) {
    console.error("Missing notification ID");
    return;
  }

  $.ajax({
    url: "/site/mark-notification-as-read",
    type: "POST",
    data: {
      id: id,
      _csrf: yii.getCsrfToken(),
    },
    success: function (res) {
      if (res.success) {
        window.location.href = link;
      } else {
        alert("Failed to mark as read: " + (res.error || ""));
      }
    },
  });
});
