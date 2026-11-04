@extends('layouts.app')

@section('content')

<style>
  /* ===== Bar atas "Transaksi" ===== */
  .t360-topbar { display:flex; justify-content:space-between; align-items:center; margin:-18px -24px 14px; padding:0 24px; height:52px; background:#f3f4f6; border-bottom:1px solid #e5e7eb; font-size:15px; color:#374151; }
  .t360-topbar i { color:#1f2937; }
  .t360-topbar .t360-clock { font-size:13.5px; color:#374151; }

  /* ===== Kartu ===== */
  .t360-card { background:#fff; border-radius:10px 10px 6px 6px; border-top:12px solid #6b7280; padding:0 0 6px; box-shadow:0 1px 3px rgba(0,0,0,.06); font-size:14px; }
  .t360-row-top { display:flex; justify-content:space-between; align-items:center; padding:10px 14px 14px; }
  .t360-btn-status { border-radius:6px; padding:3px 11px; background:#fff; transition:all .2s; font-size:16px; line-height:1.4; }
  .t360-btn-draft { border:1px solid #374151; color:#374151; }
  .t360-btn-draft:hover, .t360-btn-draft.active { background:#4b5563; border-color:#4b5563; color:#fff; }
  .t360-btn-posted { border:1px solid #d97706; color:#d97706; }
  .t360-btn-posted:hover, .t360-btn-posted.active { background:#d97706; color:#fff; }
  .t360-btn-add { border:1px solid #428bca; color:#3b82c4; background:#fff; border-radius:6px; padding:3px 11px; text-decoration:none; font-size:16px; transition:all .2s; }
  .t360-btn-add:hover { background:#428bca; color:#fff; }

  /* ===== Filter divisi ===== */
  .t360-divisi { margin:6px 14px 14px; width:34%; min-width:300px; position:relative; }
  .t360-divisi i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#374151; pointer-events:none; }
  .t360-divisi select { width:100%; height:44px; padding:0 12px 0 52px; border:1px solid #d1d5db; border-radius:5px; background:#fff; color:#9ca3af; font-size:13.5px; box-shadow:0 1px 2px rgba(0,0,0,.04); outline:none; }
  .t360-divisi select.has-value { color:#1f2937; }

  /* ===== Toolbar ===== */
  .t360-toolbar { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-top:1px solid #e5e7eb; }
  .t360-search { position:relative; width:150px; }
  .t360-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:17px; }
  .t360-search input { width:100%; height:38px; padding-left:38px; border:1px solid #d1d5db; border-radius:4px; outline:none; color:#374151; background:#fff; }
  .t360-search input::placeholder { color:#9ca3af; font-size:15px; }
  .t360-act { width:43px; height:38px; border:0; border-radius:4px; color:#fff; font-size:15px; }
  .t360-act:hover { filter:brightness(.92); }
  .t360-act:disabled { opacity:.5; cursor:not-allowed; }
  .t360-tool { height:38px; min-width:42px; border:1px solid #d1d5db; border-radius:4px; background:#fff; color:#374151; }
  .t360-tool.csv { border:0; background:transparent; color:#6b7280; }
  .t360-tool:hover { background:#f3f4f6; }
  .t360-size { width:80px; height:38px; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; background:#fff; color:#374151; }

  /* ===== Tabel ===== */
  .t360-tbl-wrap { margin:0 14px 8px; border:1px solid #d1d5db; border-radius:4px; overflow:auto; max-height:560px; }
  table.t360-tbl { width:100%; border-collapse:collapse; font-size:14px; }
  .t360-tbl thead th { background:#fff; text-align:center; font-weight:700; padding:10px 8px; position:relative; border-bottom:1px solid #d1d5db; font-size:14px; }
  .t360-tbl thead th:not(:last-child)::after { content:''; position:absolute; right:0; top:30%; height:40%; width:1px; background:#d1d5db; }
  .t360-tbl thead tr.t360-filter-row td { padding:5px 8px 6px; border-bottom:1px solid #d1d5db; background:#f3f4f6; }
  .t360-fbox { position:relative; }
  .t360-fbox i { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#6b7280; font-size:13px; }
  .t360-fbox input { width:100%; height:32px; padding-left:30px; border:1px solid #e5e7eb; border-radius:4px; background:#f9fafb; outline:none; }
  .t360-tbl tbody td { padding:0 10px; height:40px; border:2px solid #fff; border-bottom:2px solid #eef0f3; background:#fff; vertical-align:middle; }
  .t360-tbl tbody tr:nth-child(even) td { background:#fcfcfd; }
  .t360-tbl tbody tr { cursor:pointer; }
  .t360-tbl tbody tr:hover td { background:#eaf3fc; }
  .t360-tbl tbody tr.selected td { background:#b8dcfa; }
  .t360-status-DRAFT { color:#6b7280; font-weight:bold; text-align:center; }
  .t360-status-POSTED { color:#FFC107; font-weight:bold; text-align:center; }
  .t360-center { text-align:center; }
  .t360-pager { display:flex; justify-content:space-between; align-items:center; padding:4px 14px 10px; }
</style>

{{-- Bar atas --}}
<div class="t360-topbar">
  <div class="d-flex align-items-center gap-3">
    <i class="fas fa-ellipsis"></i>
    <span>Transaksi</span>
  </div>
  <div class="d-flex align-items-center gap-4">
    <span class="t360-clock" id="t360Clock"></span>
    <i class="fas fa-note-sticky"></i>
    <i class="fas fa-expand"></i>
    <span><i class="fas fa-user"></i> <i class="fas fa-chevron-down" style="font-size:12px;"></i></span>
  </div>
</div>

<div class="t360-card">

  {{-- Filter status + tombol tambah --}}
  <div class="t360-row-top">
    <div class="d-flex align-items-center gap-3">
      <span style="font-size:16px;">Filter Status :</span>
      <div class="d-flex align-items-center gap-2">
        <button type="button" id="btnDraft" class="t360-btn-status t360-btn-draft">DRAFT</button>
        <div style="width:2px;height:18px;background:#6E91D1;"></div>
        <button type="button" id="btnPosted" class="t360-btn-status t360-btn-posted">POSTED</button>
      </div>
    </div>
    <a href="{{ url('/penilaian-360/create') }}" class="t360-btn-add">Tambah Baru</a>
  </div>

  {{-- Filter divisi --}}
  <div class="t360-divisi">
    <i class="fas fa-building"></i>
    <select id="filterDivisi">
      <option value="">Filter Berdasarkan Divisi</option>
    </select>
  </div>

  {{-- Toolbar --}}
  <div class="t360-toolbar">
    <div class="d-flex align-items-center gap-2">
      <div class="t360-search">
        <i class="fas fa-search"></i>
        <input type="text" id="searchBox" placeholder="(Ctrl+F)">
      </div>
      <button type="button" id="btnDelete" class="t360-act" style="background:#ef5350;" title="Hapus"><i class="fas fa-trash"></i></button>
      <button type="button" id="btnRead" class="t360-act" style="background:#2fa57a;" title="Read"><i class="fas fa-eye"></i></button>
      <button type="button" id="btnEdit" class="t360-act" style="background:#4a7df0;" title="Edit"><i class="fas fa-edit"></i></button>
      <button type="button" id="btnPost" class="t360-act" style="background:#d6456b;" title="Post Data"><i class="fas fa-location-arrow"></i></button>
      <button type="button" id="btnCopy" class="t360-act" style="background:#4b5563;" title="Copy"><i class="fas fa-copy"></i></button>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" id="btnCsv" class="t360-tool csv" title="Export CSV"><i class="fas fa-file-csv" style="font-size:26px;"></i></button>
      <button type="button" id="btnReload" class="t360-tool" title="Refresh"><i class="fas fa-sync-alt"></i></button>
      <select id="pageSize" class="t360-size">
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>

  {{-- Tabel --}}
  <div class="t360-tbl-wrap">
    <table class="t360-tbl">
      <thead>
        <tr>
          <th style="width:70px;">No</th>
          <th>Nama</th>
          <th>Hubungan Penilai</th>
          <th>Tanggal</th>
          <th>Status</th>
        </tr>
        <tr class="t360-filter-row">
          <td></td>
          <td><div class="t360-fbox"><i class="fas fa-filter"></i><input type="text" data-col="nama"></div></td>
          <td><div class="t360-fbox"><i class="fas fa-filter"></i><input type="text" data-col="hubungan"></div></td>
          <td><div class="t360-fbox"><i class="fas fa-filter"></i><input type="text" data-col="tanggal"></div></td>
          <td></td>
        </tr>
      </thead>
      <tbody id="tbodyData">
        <tr><td colspan="5" class="text-center text-muted py-4">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="t360-pager">
    <small class="text-muted" id="pageInfo"></small>
    <div class="btn-group">
      <button type="button" id="btnPrev" class="btn btn-outline-secondary btn-sm">&laquo; Prev</button>
      <button type="button" id="btnNext" class="btn btn-outline-secondary btn-sm">Next &raquo;</button>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/penilaian/transaksi_360_index.js') }}"></script>
@endpush