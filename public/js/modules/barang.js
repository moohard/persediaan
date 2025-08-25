document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('barang-modal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('barang-form');
    const modalTitle = document.getElementById('modal-title');
    const tableBody = document.getElementById('barang-table-body');
    const btnAdd = document.getElementById('btn-add-barang');

    const loadBarang = async () => {
        try {
            const response = await axios.get('/barang/api/getAll');
            let html = '';
            if (response.data.data.length > 0) {
                response.data.data.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.kode_barang}</td>
                            <td>${item.nama_barang}</td>
                            <td><span class="badge bg-secondary">${item.jenis_barang.replace('_', ' ')}</span></td>
                            <td>${item.stok_saat_ini}</td>
                            <td>
                                <a href="/kartustok/index/${item.id_barang_encrypted}" class="btn btn-sm btn-success">Log</a>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id_barang_encrypted}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id_barang_encrypted}">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>';
            }
            tableBody.innerHTML = html;
        } catch (error) {
            console.error('Gagal memuat data barang:', error);
            Swal.fire('Error', 'Gagal memuat data barang.', 'error');
        }
    };

    btnAdd.addEventListener('click', () => {
        form.reset();
        form.dataset.action = '/barang/api/create';
        modalTitle.textContent = 'Tambah Barang Baru';
        document.getElementById('barang-id').value = '';
        modal.show();
    });

    tableBody.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;
        
        if (target.classList.contains('btn-edit')) {
            try {
                const response = await axios.get(`/barang/api/getById/${id}`);
                if (response.data.success) {
                    const item = response.data.data;
                    form.reset();
                    form.dataset.action = '/barang/api/update';
                    modalTitle.textContent = 'Edit Data Barang';
                    document.getElementById('barang-id').value = id;
                    document.getElementById('kode_barang').value = item.kode_barang;
                    document.getElementById('nama_barang').value = item.nama_barang;
                    document.getElementById('jenis_barang').value = item.jenis_barang;
                    document.getElementById('stok').value = item.stok_saat_ini;
                    modal.show();
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal mengambil data untuk diedit.', 'error');
            }
        }

        if (target.classList.contains('btn-delete')) {
            Swal.fire({
                title: 'Pindahkan ke Sampah?',
                text: "Data ini akan disembunyikan, bukan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, pindahkan!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await axios.post('/barang/api/delete', { id: id });
                        if (response.data.success) {
                            Swal.fire('Berhasil!', response.data.message, 'success');
                            loadBarang();
                        } else {
                            Swal.fire('Gagal!', response.data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Gagal memindahkan data ke sampah.', 'error');
                    }
                }
            });
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const url = form.dataset.action;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await axios.post(url, data);
            modal.hide();
            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                loadBarang();
            }
        } catch (error) {
            const errorData = error.response?.data;
            let errorMessage = errorData?.message || 'Terjadi kesalahan tidak terduga.';
            if (errorData?.errors) {
                errorMessage += '<br><ul class="text-start mt-2">';
                errorData.errors.forEach(err => { errorMessage += `<li>${err}</li>`; });
                errorMessage += '</ul>';
            }
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: errorMessage,
            });
        }
    });

    loadBarang();
});