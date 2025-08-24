$(document).ready(function () {
  const modal = new bootstrap.Modal(document.getElementById("barang-modal"));
  const form = $("#barang-form");
  const modalTitle = $("#modal-title");

  function loadBarang() {
    $.ajax({
      url: "/barang/api/getAll",
      method: "GET",
      dataType: "json",
      success: function (response) {
        let html = "";
        if (response.data.length > 0) {
          response.data.forEach((item) => {
            html += `
                            <tr>
                                <td>${item.kode_barang}</td>
                                <td>${item.nama_barang}</td>
                                <td><span class="badge bg-secondary">${item.jenis_barang.replace(
                                  "_",
                                  " "
                                )}</span></td>
                                <td>${item.stok_saat_ini}</td>
                                <td>
                                    <a href="/kartustok/index/${
                                      item.id_barang_encrypted
                                    }" class="btn btn-sm btn-success">Log</a>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="${
                                      item.id_barang_encrypted
                                    }">Edit</button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="${
                                      item.id_barang_encrypted
                                    }">Hapus</button>
                                </td>
                            </tr>
                        `;
          });
        } else {
          html =
            '<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>';
        }
        $("#barang-table-body").html(html);
      },
    });
  }

  $("#btn-add-barang").on("click", function () {
    form.trigger("reset");
    form.attr("action", "/barang/api/create");
    modalTitle.text("Tambah Barang Baru");
    $("#barang-id").val("");
    modal.show();
  });

  $("#barang-table-body").on("click", ".btn-edit", function () {
    const id = $(this).data("id");
    $.ajax({
      url: `/barang/api/getById/${id}`,
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const item = response.data;
          form.trigger("reset");
          form.attr("action", "/barang/api/update");
          modalTitle.text("Edit Data Barang");
          $("#barang-id").val(id);
          $("#kode_barang").val(item.kode_barang);
          $("#nama_barang").val(item.nama_barang);
          $("#jenis_barang").val(item.jenis_barang);
          $("#stok").val(item.stok_saat_ini);
          modal.show();
        }
      },
    });
  });

  form.on("submit", function (e) {
    e.preventDefault();
    const url = $(this).attr("action");
    let formData = $(this).serializeArray();
    formData.push({
      name: "csrf_token",
      value: $('meta[name="csrf-token"]').attr("content"),
    });

    $.ajax({
      url: url,
      method: "POST",
      data: $.param(formData),
      dataType: "json",
      success: function (response) {
        modal.hide();
        if (response.success) {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: response.message,
            timer: 1500,
            showConfirmButton: false,
          });
          loadBarang();
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: response.message,
          });
        }
      },
    });
  });

  $("#barang-table-body").on("click", ".btn-delete", function () {
    const id = $(this).data("id");
    Swal.fire({
      title: "Anda yakin?",
      text: "Data yang dihapus tidak dapat dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/barang/api/delete",
          method: "POST",
          data: {
            id: id,
            csrf_token: $('meta[name="csrf-token"]').attr("content"),
          },
          dataType: "json",
          success: function (response) {
            if (response.success) {
              Swal.fire("Terhapus!", response.message, "success");
              loadBarang();
            } else {
              Swal.fire("Gagal!", response.message, "error");
            }
          },
        });
      }
    });
  });

  loadBarang();
});
