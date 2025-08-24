document.addEventListener("DOMContentLoaded", function () {
  const modalElement = document.getElementById("permintaan-modal");
  if (!modalElement) return;

  const modal = new bootstrap.Modal(modalElement);
  const form = document.getElementById("form-permintaan");
  const itemList = document.getElementById("item-list");
  const btnAddItem = document.getElementById("btn-add-item");
  const btnCreatePermintaan = document.getElementById("btn-create-permintaan");
  const itemRowTemplate = document.getElementById("item-row-template");

  const addRow = () => {
    const templateContent = itemRowTemplate.content.cloneNode(true);
    itemList.appendChild(templateContent);
  };

  btnCreatePermintaan.addEventListener("click", function () {
    form.reset();
    itemList.innerHTML = ""; // Kosongkan list item
    addRow(); // Tambah satu baris baru
    modal.show();
  });

  btnAddItem.addEventListener("click", addRow);

  itemList.addEventListener("click", function (e) {
    if (e.target && e.target.closest(".btn-remove-item")) {
      e.target.closest(".item-row").remove();
    }
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const items = [];
    const itemBarangSelects = document.querySelectorAll(".item-barang");
    const itemJumlahInputs = document.querySelectorAll(".item-jumlah");

    for (let i = 0; i < itemBarangSelects.length; i++) {
      items.push({
        id_barang: itemBarangSelects[i].value,
        jumlah: itemJumlahInputs[i].value,
      });
    }

    const dataToSend = {
      catatan_pemohon: document.getElementById("catatan_pemohon").value,
      items: items,
    };

    Swal.fire({
      title: "Mengajukan Permintaan...",
      text: "Mohon tunggu sebentar.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    fetch("/permintaan/api/store", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
      },
      body: JSON.stringify(dataToSend),
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((err) => {
            throw err;
          });
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          modal.hide();
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: data.message,
          }).then(() => {
            window.location.reload(); // Muat ulang halaman untuk melihat data baru
          });
        }
      })
      .catch((errorData) => {
        let errorText =
          errorData.message || "Terjadi kesalahan tidak diketahui.";
        if (errorData.errors) {
          errorText += '<br><ul class="text-start mt-2">';
          errorData.errors.forEach((err) => {
            errorText += `<li>${err}</li>`;
          });
          errorText += "</ul>";
        }
        Swal.fire({
          icon: "error",
          title: "Oops...",
          html: errorText,
        });
      });
  });
});
