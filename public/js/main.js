$(document).ready(function () {
  // Konfigurasi Global AJAX untuk mengirim token CSRF
  $.ajaxSetup({
    headers: {
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
    },
  });
});
