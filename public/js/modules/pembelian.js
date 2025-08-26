document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('pembelian-table-body');
    if (!tableBody) return;

    const loadApprovedRequests = async () => {
        try {
            const response = await axios.get('/pembelian/api/getApproved');
            let html = '';
            if (response.data.data.length > 0) {
                response.data.data.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.kode_permintaan}</td>
                            <td>${new Date(item.tanggal_diproses).toLocaleString('id-ID')}</td>
                            <td>${item.nama_pemohon}</td>
                            <td>${item.nama_penyetuju}</td>
                            <td>${item.jumlah_item}</td>
                            <td>
                                <button class="btn btn-sm btn-success btn-validate" data-id="${item.id_permintaan_encrypted}">
                                    <i class="bi bi-check-lg"></i> Tandai Sudah Dibeli
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">Tidak ada permintaan pembelian yang perlu diproses.</td></tr>';
            }
            tableBody.innerHTML = html;
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Gagal memuat data permintaan pembelian.', 'error');
        }
    };

    tableBody.addEventListener('click', e => {
        const target = e.target.closest('.btn-validate');
        if (!target) return;

        const id = target.dataset.id;
        
        Swal.fire({
            title: 'Validasi Pembelian?',
            text: "Pastikan semua barang pada permintaan ini sudah dibeli.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, sudah dibeli!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post('/pembelian/api/validatePurchase', { id: id });
                    if (response.data.success) {
                        Swal.fire('Berhasil!', response.data.message, 'success');
                        loadApprovedRequests();
                    }
                } catch (error) {
                    Swal.fire('Gagal!', error.response?.data?.message || 'Terjadi kesalahan.', 'error');
                }
            }
        });
    });

    loadApprovedRequests();
});