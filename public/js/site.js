(function () {
  "use strict";

  /* Mobile menu */
  var menuBtn = document.querySelector("[data-menu]");
  var nav = document.getElementById("nav");
  if (menuBtn && nav) {
    menuBtn.addEventListener("click", function () {
      var open = nav.classList.toggle("open");
      menuBtn.setAttribute("aria-expanded", open);
    });
  }

  /* Candidate form: show the fields for the chosen track */
  var candForm = document.querySelector("[data-candidate-form]");
  if (candForm) {
    var setTrack = function (track) {
      candForm.querySelectorAll("[data-track]").forEach(function (group) {
        group.classList.toggle("hidden", group.dataset.track !== track);
      });
      var specialty = candForm.querySelector('[name="specialty"]');
      if (specialty) specialty.required = track === "doctor";
    };
    candForm.querySelectorAll('input[name="track"]').forEach(function (radio) {
      radio.addEventListener("change", function () { setTrack(radio.value); });
    });
  }

  /* CV picker: check type and size before upload (the server checks again) */
  var drop = document.querySelector("[data-drop]");
  if (drop) {
    var input = drop.querySelector('input[type="file"]');
    var label = drop.querySelector("[data-drop-label]");
    var error = document.querySelector("[data-drop-error]");
    var reset = function (message) {
      input.value = "";
      label.textContent = "Choose a CV file";
      drop.classList.remove("has");
      error.textContent = message || "";
    };
    input.addEventListener("change", function () {
      var file = input.files[0];
      if (!file) return reset();
      if (!/\.(pdf|docx?)$/i.test(file.name)) return reset("Upload a PDF or Word document.");
      if (file.size > 5 * 1024 * 1024) return reset("This file is larger than 5 MB. Please upload a smaller version.");
      error.textContent = "";
      label.textContent = file.name + " (" + Math.ceil(file.size / 1024) + " KB)";
      drop.classList.add("has");
    });
  }

  /* Confirm destructive admin actions */
  document.querySelectorAll("form[data-confirm]").forEach(function (form) {
    form.addEventListener("submit", function (event) {
      if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
  });
})();
