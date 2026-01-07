// Auto hide flash messages after 5 seconds
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s";
      alert.style.opacity = "0";
      setTimeout(() => {
        alert.remove();
      }, 500);
    }, 5000);
  });
});

// Confirm delete actions
document.querySelectorAll("[data-confirm]").forEach((element) => {
  element.addEventListener("click", function (e) {
    if (!confirm(this.getAttribute("data-confirm"))) {
      e.preventDefault();
    }
  });
});

// Form validation
document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", function (e) {
    const requiredFields = form.querySelectorAll("[required]");
    let isValid = true;

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.style.borderColor = "#ef4444";
      } else {
        field.style.borderColor = "#d1d5db";
      }
    });

    if (!isValid) {
      e.preventDefault();
      alert("Mohon lengkapi semua field yang wajib diisi");
    }
  });
});

// Remove error border on input
document.querySelectorAll(".form-control").forEach((input) => {
  input.addEventListener("input", function () {
    this.style.borderColor = "#d1d5db";
  });
});

// Table row highlight
document.querySelectorAll(".table tbody tr").forEach((row) => {
  row.addEventListener("click", function () {
    // Remove highlight from other rows
    document.querySelectorAll(".table tbody tr").forEach((r) => {
      r.style.background = "";
    });
    // Highlight this row
    this.style.background = "#f0f9ff";
  });
});

// Auto-update time inputs to prevent past times
const timeInputs = document.querySelectorAll('input[type="time"]');
timeInputs.forEach((input) => {
  const today = new Date().toISOString().split("T")[0];
  const dateInput = document.querySelector('input[type="date"]');

  if (dateInput && dateInput.value === today) {
    const now = new Date();
    const currentTime =
      now.getHours().toString().padStart(2, "0") +
      ":" +
      now.getMinutes().toString().padStart(2, "0");
    input.min = currentTime;
  }
});

// Number input validation
document.querySelectorAll('input[type="number"]').forEach((input) => {
  input.addEventListener("keydown", function (e) {
    // Allow: backspace, delete, tab, escape, enter, decimal point
    if (
      [46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
      // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
      (e.keyCode === 65 && e.ctrlKey === true) ||
      (e.keyCode === 67 && e.ctrlKey === true) ||
      (e.keyCode === 86 && e.ctrlKey === true) ||
      (e.keyCode === 88 && e.ctrlKey === true) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)
    ) {
      return;
    }
    // Ensure that it is a number and stop the keypress
    if (
      (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
      (e.keyCode < 96 || e.keyCode > 105)
    ) {
      e.preventDefault();
    }
  });
});

// Prevent form resubmission on refresh
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

console.log("Sistem Peminjaman Aula Kemenag Jombang - v1.0");
