document.addEventListener('DOMContentLoaded', () => {
  const createModalEl = document.getElementById('permintaan-modal');
  const detailModalEl = document.getElementById('detail-modal');
  if (!createModalEl || !detailModalEl) return;

  const createModal = new bootstrap.Modal(createModalEl);
  const detailModal = new bootstrap.Modal(detailModalEl);
  const form = document.getElementById('form-permintaan');
  const itemList = document.getElementById('item-list');
  const btnAddItem = document.getElementById('btn-add-item');
  const btnCreatePermintaan = document.getElementById('btn-create-permintaan');
  const itemRowTemplate = document.getElementById('item-row-template');
  const tableBody = document.getElementById('permintaan-table-body');

  const loadPermintaan = async () => {
    try {
      const response = await axios.get('/permintaan/api/getAll');
      let html = '';
      if (response.data.data.length > 0) {
        response.data.data.forEach(item => {
          let statusClass = 'bg-secondary';
          if (item.status_permintaan === 'Disetujui') statusClass = 'bg-success';
          if (item.status_permintaan === 'Ditolak') statusClass = 'bg-danger';
          if (item.status_permintaan === 'Diajukan') statusClass = 'bg-warning text-dark';

          let tipeClass = item.tipe_permintaan === 'pembelian' ? 'bg-primary' : 'bg-info';

          html += `
                        <tr>
                            <td>${item.kode_permintaan}</td>
                            <td>${new Date(item.tanggal_permintaan).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                            <td><span class="badge ${tipeClass}">${item.tipe_permintaan}</span></td>
                            <td>${item.nama_pemohon}</td>
                            <td>${item.jumlah_item}</td>
                            <td><span class="badge ${statusClass}">${item.status_permintaan}</span></td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail" data-id="${item.id_permintaan_encrypted}"><i class="bi bi-eye"></i> Detail</button>
                            </td>
                        </tr>
                    `;
        });
      } else {
        html = '<tr><td colspan="7" class="text-center">Belum ada data permintaan.</td></tr>';
      }
      tableBody.innerHTML = html;
    } catch (error) {
      console.error(error);
    }
  };

  const addRow = () => {
    const templateContent = itemRowTemplate.content.cloneNode(true);
    itemList.appendChild(templateContent);
  };

  btnCreatePermintaan.addEventListener('click', () => {
    form.reset();
    itemList.innerHTML = '';
    addRow();
    createModal.show();
  });

  btnAddItem.addEventListener('click', addRow);

  itemList.addEventListener('click', e => {
    if (e.target.classList.contains('btn-remove-item')) {
      e.target.closest('.item-row').remove();
    }
    if (e.target.classList.contains('item-is-custom')) {
      const row = e.target.closest('.item-row');
      const selectEl = row.querySelector('.item-barang');
      const inputEl = row.querySelector('.item-barang-custom');
      if (e.target.checked) {
        selectEl.classList.add('d-none');
        selectEl.removeAttribute('required');
        inputEl.classList.remove('d-none');
        inputEl.setAttribute('required', 'required');
      } else {
        selectEl.classList.remove('d-none');
        selectEl.setAttribute('required', 'required');
        inputEl.classList.add('d-none');
        inputEl.removeAttribute('required');
      }
    }
  });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const items = [];
    const isPembelian = document.getElementById('is_pembelian').checked;

    document.querySelectorAll('.item-row').forEach(row => {
      const isCustom = row.querySelector('.item-is-custom').checked;
      const jumlah = row.querySelector('.item-jumlah').value;
      let itemData = { jumlah: jumlah, is_custom: isCustom };

      if (isCustom) {
        itemData.nama_barang_custom = row.querySelector('.item-barang-custom').value;
        itemData.id_barang = null;
      } else {
        itemData.id_barang = row.querySelector('.item-barang').value;
        itemData.nama_barang_custom = null;
      }
      items.push(itemData);
    });

    const dataToSend = {
      catatan_pemohon: document.getElementById('catatan_pemohon').value,
      items: items,
      tipe_permintaan: isPembelian ? 'pembelian' : 'stok'
    };

    Swal.fire({
      title: 'Mengajukan Permintaan...',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const response = await axios.post('/permintaan/api/store', dataToSend);
      if (response.data.success) {
        createModal.hide();
        Swal.fire('Berhasil!', response.data.message, 'success');
        loadPermintaan();
      }
    } catch (error) {
      const errorData = error.response?.data;
      let errorText = errorData?.message || 'Terjadi kesalahan.';
      if (errorData?.errors) {
        errorText += '<br><ul class="text-start mt-2">' + errorData.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
      }
      Swal.fire('Oops...', errorText, 'error');
    }
  });

  tableBody.addEventListener('click', async e => {
    const target = e.target.closest('.btn-detail');
    if (!target) return;

    const id = target.dataset.id;
    try {
      const response = await axios.get(`/permintaan/api/getDetail/${id}`);
      const { header, items } = response.data.data;

      let detailHtml = `
                <p><strong>Kode:</strong> ${header.kode_permintaan}</p>
                <p><strong>Tipe:</strong> <span class="badge bg-primary">${header.tipe_permintaan}</span></p>
                <p><strong>Pemohon:</strong> ${header.nama_pemohon}</p>
                <p><strong>Tanggal:</strong> ${new Date(header.tanggal_permintaan).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">${header.status_permintaan}</span></p>
                <p><strong>Catatan Pemohon:</strong><br>${header.catatan_pemohon || '-'}</p>
                <hr>
                <h6>Rincian Barang:</h6>
                <table class="table table-sm">
                    <thead><tr><th>Nama Barang</th><th>Diminta</th><th>Disetujui</th></tr></thead>
                    <tbody id="detail-item-list">
            `;
      items.forEach(item => {
        const isDiajukan = header.status_permintaan === 'Diajukan';
        const itemName = item.id_barang ? item.nama_barang : `<i>${item.nama_barang_custom} (Barang Baru)</i>`;
        detailHtml += `
                    <tr>
                        <td>${itemName}</td>
                        <td>${item.jumlah_diminta}</td>
                        <td>
                            ${isDiajukan ?
            `<input type="number" class="form-control form-control-sm approved-qty" value="${item.jumlah_diminta}" min="0" max="${item.jumlah_diminta}" data-detail-id="${item.id_detail_permintaan}">` :
            (item.jumlah_disetujui !== null ? item.jumlah_disetujui : '-')
          }
                        </td>
                    </tr>
                `;
      });
      detailHtml += '</tbody></table>';

      if (header.status_permintaan !== 'Diajukan') {
        detailHtml += `
                    <hr>
                    <h6>Detail Persetujuan:</h6>
                    <p><strong>Diproses oleh:</strong> ${header.nama_penyetuju || '-'}</p>
                    <p><strong>Tanggal Proses:</strong> ${new Date(header.tanggal_diproses).toLocaleString('id-ID')}</p>
                    <p><strong>Catatan Pimpinan:</strong><br>${header.catatan_penyetuju || '-'}</p>
                `;
      }

      document.getElementById('detail-modal-body').innerHTML = detailHtml;

      let footerHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
      if (header.status_permintaan === 'Diajukan') {
        footerHtml += `
                    <button type="button" class="btn btn-danger btn-process" data-action="reject" data-id="${id}">Tolak</button>
                    <button type="button" class="btn btn-success btn-process" data-action="approve" data-id="${id}">Setujui</button>
                `;
      }
      document.getElementById('detail-modal-footer').innerHTML = footerHtml;

      detailModal.show();
    } catch (error) {
      Swal.fire('Error', 'Gagal memuat detail permintaan.', 'error');
    }
  });

  document.getElementById('detail-modal-footer').addEventListener('click', async e => {
    const target = e.target.closest('.btn-process');
    if (!target) return;

    const id = target.dataset.id;
    const action = target.dataset.action;
    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';

    let itemsToProcess = [];
    if (action === 'approve') {
      document.querySelectorAll('#detail-item-list .approved-qty').forEach(input => {
        itemsToProcess.push({
          id_detail: input.dataset.detailId,
          jumlah_disetujui: input.value
        });
      });
    }

    const { value: catatan } = await Swal.fire({
      title: `Anda yakin ingin ${actionText} permintaan ini?`,
      input: 'textarea',
      inputLabel: 'Catatan (Opsional)',
      inputPlaceholder: 'Masukkan alasan atau catatan Anda di sini...',
      showCancelButton: true,
      confirmButtonText: `Ya, ${actionText}!`,
      cancelButtonText: 'Batal'
    });

    if (catatan !== undefined) {
      try {
        const response = await axios.post('/permintaan/api/process', { id, action, items: itemsToProcess, catatan_penyetuju: catatan });
        if (response.data.success) {
          detailModal.hide();
          Swal.fire('Berhasil!', response.data.message, 'success');
          loadPermintaan();
        }
      } catch (error) {
        Swal.fire('Gagal!', error.response?.data?.message || 'Terjadi kesalahan.', 'error');
      }
    }
  });

  loadPermintaan();
});
