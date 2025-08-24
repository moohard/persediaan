$(document).ready(function () {
  // Konfigurasi Global AJAX untuk mengirim token CSRF
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  });

  console.log("Main JS loaded.");
});
