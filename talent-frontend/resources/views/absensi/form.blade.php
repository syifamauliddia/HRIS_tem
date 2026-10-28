@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4 bg-white">
        
        <h4 class="fw-bold mb-3">Form Presensi Absensi</h4>
        <hr class="mb-4">

        <form id="formAbsensi">
            <div class="row g-4">
                
                <!-- KOLOM KIRI (INFORMASI UTAMA & CHECKIN) -->
                <div class="col-md-6 border-end pe-md-4">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Karyawan</label>
                        <select id="default_user_id" class="form-select" required>
                            <option value="1" selected>EMP-001 - Syifa Mauliddia</option>
                            <option value="2">EMP-002 - Budi Santoso</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" id="tanggal" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select id="status" class="form-select">
                            <option value="ATTEND">ATTEND</option>
                            <option value="WORKING">WORKING</option>
                            <option value="BREAK">BREAK</option>
                            <option value="RESUME WORKING">RESUME WORKING</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jam Kerja (Shift)</label>
                        <select id="shift" class="form-select">
                            <option value="Reguler Pagi">Reguler Pagi (08:00 - 17:00)</option>
                            <option value="Shift Malam">Shift Malam (20:00 - 05:00)</option>
                        </select>
                    </div>

                    <!-- SUB-SECTION CHECKIN -->
                    <h5 class="fw-bold text-primary mt-4 mb-3">Check In</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Waktu Check In</label>
                        <input type="time" id="checkin_time" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Check In</label>
                        <div class="bg-light p-2 text-center border rounded" style="height: 220px;">
                            <img id="previewCheckinFoto" src="https://via.placeholder.com/150x200.png?text=Preview+Checkin" class="h-100 img-fluid rounded">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi Check In (Wilayah)</label>
                        <input type="text" id="checkin_region" class="form-control" placeholder="Contoh: Surabaya Karah">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="text" id="checkin_lat" class="form-control" readonly placeholder="-7.3244677">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="text" id="checkin_long" class="form-control" readonly placeholder="112.7550714">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Check In</label>
                        <textarea id="checkin_address" class="form-control" rows="2" placeholder="Alamat lengkap checkin"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Check In</label>
                        <textarea id="catatan_in" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">On Scope <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkin_on_scope" id="checkin_scope_1" value="1" checked>
                            <label class="form-check-label" for="checkin_scope_1">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkin_on_scope" id="checkin_scope_0" value="0">
                            <label class="form-check-label" for="checkin_scope_0">Tidak</label>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN (CHECKOUT) -->
                <div class="col-md-6 ps-md-4">
                    
                    <h5 class="fw-bold text-danger mb-3">Check Out</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Waktu Check Out</label>
                        <input type="time" id="checkout_time" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Check Out</label>
                        <div class="bg-light p-2 text-center border rounded" style="height: 220px;">
                            <img id="previewCheckoutFoto" src="https://via.placeholder.com/150x200.png?text=Preview+Checkout" class="h-100 img-fluid rounded">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi Check Out (Wilayah)</label>
                        <input type="text" id="checkout_region" class="form-control" placeholder="Contoh: Surabaya Karah">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="text" id="checkout_lat" class="form-control" readonly placeholder="-7.3244677">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="text" id="checkout_long" class="form-control" readonly placeholder="112.7550714">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Check Out</label>
                        <textarea id="checkout_address" class="form-control" rows="2" placeholder="Alamat lengkap checkout"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Check Out</label>
                        <textarea id="catatan_out" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">On Scope <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkout_on_scope" id="checkout_scope_1" value="1" checked>
                            <label class="form-check-label" for="checkout_scope_1">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkout_on_scope" id="checkout_scope_0" value="0">
                            <label class="form-check-label" for="checkout_scope_0">Tidak</label>
                        </div>
                    </div>

                </div>

            </div>

            <!-- TOMBOL AKSI -->
            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary px-4" onclick="javascript:history.back()">Kembali</button>
                <button type="button" class="btn btn-success px-4 fw-bold" onclick="saveAbsensi()">Simpan</button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const tsDate = urlParams.get('ts') || new Date().toISOString().slice(0, 10);
    const userId = urlParams.get('user_id');
    const pathSegments = window.location.pathname.split('/');
    const recordId = pathSegments[pathSegments.length - 1] !== 'create' ? pathSegments[pathSegments.length - 1] : null;

    document.addEventListener('DOMContentLoaded', async function() {
        document.getElementById('tanggal').value = tsDate;
        if (userId) document.getElementById('default_user_id').value = userId;

        if (recordId) {
            // Mode Edit: Ambil data dari backend
            try {
                const res = await fetch(`http://localhost:3000/api/presensi_absensi/${recordId}`);
                const result = await res.json();
                if (result.status === 'success') {
                    const data = result.data;
                    document.getElementById('status').value = data.status || 'ATTEND';
                    document.getElementById('shift').value = data.shift || 'Reguler Pagi';

                    document.getElementById('checkin_time').value = data.checkin_time || '';
                    document.getElementById('checkin_region').value = data.checkin_region || '';
                    document.getElementById('checkin_lat').value = data.checkin_lat || '';
                    document.getElementById('checkin_long').value = data.checkin_long || '';
                    document.getElementById('checkin_address').value = data.checkin_address || '';
                    document.getElementById('catatan_in').value = data.catatan_in || '';

                    document.getElementById('checkout_time').value = data.checkout_time || '';
                    document.getElementById('checkout_region').value = data.checkout_region || '';
                    document.getElementById('checkout_lat').value = data.checkout_lat || '';
                    document.getElementById('checkout_long').value = data.checkout_long || '';
                    document.getElementById('checkout_address').value = data.checkout_address || '';
                    document.getElementById('catatan_out').value = data.catatan_out || '';
                }
            } catch (err) {
                console.error("Gagal load detail absensi:", err);
            }
        }
    });

    async function saveAbsensi() {
        const confirm = await Swal.fire({
            icon: 'warning',
            text: 'Simpan data absensi ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        const payload = {
            default_user_id: document.getElementById('default_user_id').value,
            tanggal: document.getElementById('tanggal').value,
            status: document.getElementById('status').value,
            shift: document.getElementById('shift').value,
            checkin_time: document.getElementById('checkin_time').value,
            checkin_region: document.getElementById('checkin_region').value,
            checkin_address: document.getElementById('checkin_address').value,
            catatan_in: document.getElementById('catatan_in').value,
            checkout_time: document.getElementById('checkout_time').value,
            checkout_region: document.getElementById('checkout_region').value,
            checkout_address: document.getElementById('checkout_address').value,
            catatan_out: document.getElementById('catatan_out').value
        };

        const method = recordId ? 'PUT' : 'POST';
        const endpoint = recordId ? `http://localhost:3000/api/presensi_absensi/${recordId}` : 'http://localhost:3000/api/presensi_absensi';

        try {
            const res = await fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                Swal.fire({ icon: 'success', text: 'Data absensi berhasil disimpan!' }).then(() => {
                    window.location.href = `/absensi`;
                });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', text: 'Gagal menyimpan data absensi.' });
        }
    }
</script>
@endpush