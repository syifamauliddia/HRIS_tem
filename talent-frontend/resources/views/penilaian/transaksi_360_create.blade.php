@extends('layouts.app')

@section('content')

<style>
  /* ===== Bar atas ===== */
  .t360-topbar { display:flex; justify-content:space-between; align-items:center; margin:-18px -24px 14px; padding:0 24px; height:52px; background:#f3f4f6; border-bottom:1px solid #e5e7eb; font-size:15px; color:#374151; }
  .t360-topbar i { color:#1f2937; }
  .t360-topbar .t360-clock { font-size:13.5px; color:#374151; }

  /* ===== Kartu form ===== */
  .f-card { background:#fff; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,.08); overflow:hidden; font-size:14px; }
  .f-head { background:#6b7280; color:#fff; padding:10px 18px; display:flex; align-items:center; gap:12px; }
  .f-head a { color:#fff; text-decoration:none; font-size:18px; }
  .f-head a:hover { color:#facc15; }
  .f-head h1 { font-size:20px; font-weight:700; margin:0; }
  .f-head p { margin:0; font-size:14px; color:#f3f4f6; }

  .f-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:14px 12px; padding:18px; }
  @media (max-width: 992px) { .f-grid { grid-template-columns:1fr; } }
  .f-field label { display:block; font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px; }
  .f-field .form-control, .f-field .form-select { height:42px; font-size:13.5px; }
  .f-field .form-control[readonly], .f-field .form-control:disabled, .f-field .form-select:disabled { background:#eef0f3; color:#6b7280; }
  .f-field .f-icon { position:relative; }
  .f-field .f-icon i { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#6b7280; pointer-events:none; }

  .f-body { padding:6px 22px 18px; }
  .f-title-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
  .f-title-row h2 { font-size:22px; font-weight:600; margin:0; }
  .f-who { text-align:right; }
  .f-who .n { font-size:14px; font-weight:600; color:#2563eb; line-height:1.2; }
  .f-who .j { font-size:10.5px; color:#6b7280; }
  .f-score { padding:3px 10px; background:#eff6ff; border:1px solid #93c5fd; border-radius:5px; color:#2563eb; font-weight:700; font-size:15px; }

  /* kartu pertanyaan */
  .f-q { border:1px solid #e5e7eb; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom:22px; overflow:hidden; }
  .f-qh { display:flex; justify-content:space-between; align-items:center; background:#128AE9; color:#fff; padding:8px 18px; font-weight:600; }
  .f-qs { display:flex; justify-content:space-between; align-items:center; background:#F6F7FF; padding:8px 18px; }
  .f-qt { color:#128AE9; font-weight:500; }
  .f-badge { display:inline-flex; align-items:center; justify-content:center; min-width:24px; height:24px; padding:0 6px; border-radius:50px; font-size:12px; font-weight:600; }
  .f-badge.w { background:#fff; color:#128AE9; }
  .f-badge.d { background:#49525D; color:#fff; }
  .f-opts { display:grid; grid-template-columns:repeat(5, 1fr); }
  @media (max-width: 992px) { .f-opts { grid-template-columns:1fr; } }
  .f-opt { border:1px solid #e5e7eb; background:#fff; padding-top:12px; min-height:150px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; transition:background .15s; }
  .f-opt.disabled { cursor:default; }
  .f-opt:not(.disabled):hover { background:#f5faff; }
  .f-opt.sel { background:#eff6ff; outline:2px solid #3b82f6; outline-offset:-2px; }
  .f-ot { padding:0 16px; font-size:13px; color:#333; line-height:1.6; flex:1; }
  .f-of { display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding:8px 16px; border-top:1px solid #e5e7eb; background:#fafafa; }
  .f-of span { font-size:12px; font-weight:600; color:#374151; }
  .f-of input { width:17px; height:17px; accent-color:#2563eb; cursor:pointer; }

  .f-notes { display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; }
  @media (max-width: 992px) { .f-notes { grid-template-columns:1fr; } }
  .f-notes label { font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; display:block; min-height:34px; }
  .f-notes textarea { width:100%; min-height:140px; }

  .f-foot { display:flex; justify-content:flex-end; align-items:center; gap:8px; padding:14px 18px; border-top:1px solid #e5e7eb; background:#fff; }
  .f-btn { border:0; border-radius:6px; color:#fff; font-weight:600; padding:8px 18px; transition:transform .2s; }
  .f-btn:hover { transform:translateY(-2px); }
  .f-btn.red { background:#dc2626; }
  .f-btn.green { background:#16a34a; }
  .f-btn.gray { background:#6b7280; }

  /* tabel pilih karyawan */
  #empTable tbody tr { cursor:pointer; }
  #empTable tbody tr:hover td { background:#eaf3fc; }
</style>

{{-- Bar atas --}}
<div class="t360-topbar">
  <div class="d-flex align-items-center gap-3">
    <i class="fas fa-ellipsis"></i>
    <span>Transaksi &gt; <span id="modeLabel">Create</span></span>
  </div>
  <div class="d-flex align-items-center gap-4">
    <span class="t360-clock" id="t360Clock"></span>
    <i class="fas fa-note-sticky"></i>
    <i class="fas fa-expand"></i>
    <span><i class="fas fa-user"></i> <i class="fas fa-chevron-down" style="font-size:12px;"></i></span>
  </div>
</div>

<div class="f-card">

  <div class="f-head">
    <a href="{{ url('/penilaian-360') }}" title="Kembali"><i class="fas fa-arrow-left"></i></a>
    <div>
      <h1>Form Penilaian 360</h1>
      <p>Penilaian oleh atasan, rekan, bawahan &amp; diri sendiri</p>
    </div>
  </div>

  {{-- Isian atas --}}
  <div class="f-grid">
    <div class="f-field">
      <label>Karyawan Yang Dinilai</label>
      <div class="f-icon">
        <input type="text" id="fKaryawan" class="form-control" placeholder="Pilih Karyawan" readonly style="cursor:pointer;background:#fff;">
        <i class="fas fa-search"></i>
      </div>
    </div>
    <div class="f-field">
      <label>Jabatan Karyawan</label>
      <input type="text" id="fJabatan" class="form-control" placeholder="Jabatan Terisi Otomatis" readonly>
    </div>
    <div class="f-field">
      <label>Divisi</label>
      <input type="text" id="fDivisi" class="form-control" placeholder="Divisi Terisi Otomatis" readonly>
    </div>
    <div class="f-field">
      <label>Level Karyawan</label>
      <input type="text" id="fLevel" class="form-control" placeholder="Level Terisi Otomatis" readonly>
    </div>
    <div class="f-field">
      <label>Periode</label>
      <input type="date" id="fTanggal" class="form-control">
    </div>
    <div class="f-field">
      <label>Penilai</label>
      <input type="text" id="fPenilai" class="form-control" readonly>
    </div>
    <div class="f-field">
      <label>Hubungan Penilai</label>
      <input type="text" id="fHubungan" class="form-control" placeholder="Terisi Otomatis" readonly>
    </div>
    <div class="f-field">
      <label>Tipe Penilaian</label>
      <select id="fTipe" class="form-select"><option value="">Pilih Tipe Penilaian</option></select>
    </div>
    <div class="f-field">
      <label>Penilaian (Pertanyaan / KPI)</label>
      <select id="fAssess" class="form-select" disabled><option value="">Pilih Penilaian Karyawan</option></select>
    </div>
    <div class="f-field">
      <label>Deskripsi Penilaian</label>
      <input type="text" id="fDeskripsi" class="form-control" placeholder="Penilaian Terisi Otomatis" readonly>
    </div>
  </div>

  {{-- Penilaian --}}
  <div class="f-body">
    <div class="f-title-row">
      <h2>Penilaian Feedback 360</h2>
      <div class="d-flex align-items-center gap-2">
        <div class="f-who">
          <div class="n" id="whoNama"></div>
          <div class="j" id="whoJabatan">Jabatan</div>
        </div>
        <div class="f-score" id="scoreBox">0.00</div>
      </div>
    </div>

    <div id="questions"></div>

    <div id="notesWrap" class="f-notes" style="display:none;">
      <div><label>Catatan Manager / OM</label><textarea id="fCatatan1" class="form-control"></textarea></div>
      <div><label>Catatan Departemen HRD</label><textarea id="fCatatan2" class="form-control"></textarea></div>
      <div><label>Laporan Pekerjaan &amp; Evaluasi (Kelebihan / Kekurangan)</label><textarea id="fCatatan3" class="form-control"></textarea></div>
      <div><label>Development / Training &amp; Usulan Pengembangan Prestasi Kerja</label><textarea id="fCatatan4" class="form-control"></textarea></div>
    </div>
  </div>

  {{-- Tombol --}}
  <div class="f-foot">
    <small class="text-muted me-2 fst-italic" id="hintCtrlS">Tekan CTRL + S untuk shortcut Save Data</small>
    <a href="{{ url('/penilaian-360') }}" class="f-btn gray text-decoration-none" id="btnKembali" style="display:none;"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    <button type="button" id="btnReset" class="f-btn red"><i class="fas fa-times me-1"></i> Reset</button>
    <button type="button" id="btnSave" class="f-btn green"><i class="fas fa-save me-1"></i> Simpan</button>
  </div>
</div>

{{-- Modal pilih karyawan --}}
<div class="modal fade" id="empModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Karyawan Yang Dinilai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="empSearch" class="form-control mb-3" placeholder="Cari kode / nama / posisi / divisi...">
        <table class="table table-bordered table-hover align-middle" id="empTable">
          <thead class="table-light">
            <tr><th style="width:60px;" class="text-center">No</th><th>Kode</th><th>Nama</th><th>Atasan</th><th>Posisi</th><th>Divisi</th><th>Hubungan</th></tr>
          </thead>
          <tbody id="empBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/penilaian/transaksi_360_create.js') }}"></script>
@endpush