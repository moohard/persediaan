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
    itemList.innerHTML = "";
    addRow();
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

    axios
      .post("/permintaan/api/store", dataToSend)
      .then((response) => {
        if (response.data.success) {
          modal.hide();
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: response.data.message,
          }).then(() => {
            window.location.reload();
          });
        }
      })
      .catch((error) => {
        let errorData = error.response.data;
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
