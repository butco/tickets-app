function triggerClick() {
  document.querySelector("#profileImage").click();
}

function previewProfilePhoto(e) {
  if (e.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      document
        .querySelector("#profilePhoto")
        .setAttribute("src", e.target.result);
    };

    reader.readAsDataURL(e.files[0]);
  }
}
