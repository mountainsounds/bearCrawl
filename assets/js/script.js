$(document).ready(() => {

  $(".result").on("click", function() {
    let url = $(this).attr("href");
    let id = $(this).attr("data-linkId");

    increaseLinkClicks(id, url);

    return false;
  });



});

const increaseLinkClicks = (linkId, url) => {
  $.post("ajax/updateLinkCount.php", {linkId: linkId})
  .done(function(result) {
    if (result != "") {
      alert(result);
      return;
    }

    window.location.href = url;
  });
}