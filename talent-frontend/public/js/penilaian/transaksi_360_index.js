(function () {
  // ================= PENGATURAN =================
  const API_URL = 'http://localhost:3000/api/transaction-360'
  const BASE_PATH = '/penilaian-360'

  // true  = data disimpan di browser (localStorage), tanpa backend
  // false = pakai server.js + MySQL (http://localhost:3000)
  const USE_DUMMY = true

  const STORE_KEY = 't360_rows'
  const DIVISI = ['Information Technology', 'HRD', 'Finance', 'Marketing']
  // ==============================================

  // ---- data dummy awal (dipakai kalau USE_DUMMY = true) ----
  const DUMMY_NAMA = ['Syifa Mauliddia', 'Julyandra Andi Santoso', 'Khabibi Al Munif', 'ARU TEST', 'Rina Wulandari', 'Budi Hartono', 'Dewi Lestari', 'Agus Prasetyo', 'Nadia Putri', 'Fajar Ramadhan']
  const DUMMY_HUB = ['Atasan', 'Rekan', 'Bawahan', 'Diri Sendiri']
  // 13 baris pertama persis seperti screenshot: [nama, hubungan, tanggal, status]
  const FIXED = [
    ['ARU TEST', '', '2026-08-22', 'DRAFT'],
    ['ARU TEST', '', '2026-08-28', 'DRAFT'],
    ['ARU TEST', '', '2026-08-22', 'POSTED'],
    ['ARU TEST', '', '2026-08-26', 'DRAFT'],
    ['', '', '2026-08-26', 'DRAFT'],
    ['', '', '2026-08-26', 'DRAFT'],
    ['ARU TEST', '', '2026-08-26', 'DRAFT'],
    ['Julyandra Andi Santoso', '', '2026-08-22', 'POSTED'],
    ['', '', '2026-08-22', 'DRAFT'],
    ['Julyandra Andi Santoso', '', '2026-08-22', 'DRAFT'],
    ['', '', '2026-08-22', 'POSTED'],
    ['Khabibi Al Munif', '', '2026-08-22', 'DRAFT'],
    ['ARU TEST', '', '2026-08-22', 'DRAFT']
  ]
  const seedRows = () => Array.from({ length: 45 }, (_, i) => {
    const f = FIXED[i]
    return {
      id: i + 1,
      employee_id: null,
      nama: f ? f[0] : DUMMY_NAMA[i % DUMMY_NAMA.length],
      nama_divisi: DIVISI[i % 4],
      hubungan_penilai: f ? f[1] : DUMMY_HUB[i % DUMMY_HUB.length],
      tanggal: f ? f[2] : `2026-08-${String((i % 28) + 1).padStart(2, '0')}`,
      status: f ? f[3] : (i % 4 === 2 ? 'POSTED' : 'DRAFT'),
      total_nilai: 0,
      details: []
    }
  })

  const getStore = () => { try { return JSON.parse(localStorage.getItem(STORE_KEY)) } catch (e) { return null } }
  const setStore = (rows) => localStorage.setItem(STORE_KEY, JSON.stringify(rows))
  if (USE_DUMMY && !getStore()) setStore(seedRows())

  // ---- lapisan data: dummy (localStorage) atau API ----
  const dataApi = {
    async list() {
      if (USE_DUMMY) return getStore() || []
      const res = await fetch(API_URL)
      const j = await res.json()
      if (j.status !== 'success') throw new Error(j.message || 'Gagal mengambil data')
      return j.data || []
    },
    async remove(id) {
      if (USE_DUMMY) { setStore((getStore() || []).filter((r) => String(r.id) !== String(id))); return }
      const res = await fetch(`${API_URL}/${id}`, { method: 'DELETE' })
      const j = await res.json().catch(() => ({}))
      if (!res.ok) throw new Error(j.message || 'Gagal menghapus data')
    },
    async post(id) {
      if (USE_DUMMY) {
        const rows = getStore() || []
        const r = rows.find((x) => String(x.id) === String(id))
        if (r) r.status = 'POSTED'
        setStore(rows)
        return 'Data berhasil di-post (dummy)'
      }
      const res = await fetch(`${API_URL}/post`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(id) })
      })
      const j = await res.json().catch(() => ({}))
      if (!res.ok) throw new Error(j.message || 'Gagal post data')
      return j.message || 'Berhasil'
    }
  }

  // ---- state ----
  const state = {
    page: 1, hasNext: false, status: null, divisi: '', search: '', limit: 20,
    colFilters: { nama: '', hubungan: '', tanggal: '' },
    selectedId: null
  }
  let currentRows = []

  const $ = (id) => document.getElementById(id)
  const tbody = $('tbodyData')
  const btnDraft = $('btnDraft')
  const btnPosted = $('btnPosted')

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))

  // tanggal dari DB bisa berupa "2026-08-22" atau ISO "2026-08-21T17:00:00.000Z"
  const toYmd = (t) => {
    if (!t) return ''
    if (/^\d{4}-\d{2}-\d{2}$/.test(t)) return t
    const d = new Date(t)
    if (isNaN(d)) return String(t)
    const p = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`
  }
  const fmtTanggal = (t) => {
    const m = toYmd(t).match(/^(\d{4})-(\d{2})-(\d{2})$/)
    return m ? `${m[3]}/${m[2]}/${m[1]}` : toYmd(t)
  }
  const getNama = (r) => r.nama || ''

  const matchColFilters = (r) => {
    const f = state.colFilters
    const has = (val, q) => !q || String(val ?? '').toLowerCase().includes(q.toLowerCase())
    return has(getNama(r), f.nama) && has(r.hubungan_penilai, f.hubungan) && has(fmtTanggal(r.tanggal), f.tanggal)
  }

  // ---- ambil & tampilkan data ----
  async function loadData() {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Memuat data...</td></tr>'
    try {
      const all = await dataApi.list()
      const rows = all.filter((r) =>
        (!state.status || r.status === state.status) &&
        (!state.divisi || r.nama_divisi === state.divisi) &&
        (!state.search || getNama(r).toLowerCase().includes(state.search.toLowerCase())) &&
        matchColFilters(r)
      )
      const start = (state.page - 1) * state.limit
      state.hasNext = start + state.limit < rows.length
      currentRows = rows.slice(start, start + state.limit)
      renderRows()
      $('pageInfo').textContent = `Halaman ${state.page} (${rows.length} data${USE_DUMMY ? ' dummy' : ''})`
    } catch (e) {
      console.error(e)
      currentRows = []
      updateButtons()
      tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data (${esc(e.message)}). Pastikan server.js berjalan di port 3000.</td></tr>`
    }
    $('btnPrev').disabled = state.page <= 1
    $('btnNext').disabled = !state.hasNext
  }

  function renderRows() {
    if (!currentRows.some((r) => String(r.id) === String(state.selectedId))) state.selectedId = null
    updateButtons()

    if (!currentRows.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data</td></tr>'
      return
    }
    const offset = (state.page - 1) * state.limit
    tbody.innerHTML = currentRows.map((r, i) => `
      <tr data-id="${r.id}" class="${String(r.id) === String(state.selectedId) ? 'selected' : ''}">
        <td class="t360-center">${offset + i + 1}</td>
        <td>${esc(getNama(r))}</td>
        <td>${esc(r.hubungan_penilai)}</td>
        <td>${esc(fmtTanggal(r.tanggal))}</td>
        <td class="t360-status-${esc(r.status)}">${esc(r.status)}</td>
      </tr>`).join('')
  }

  const selectedRow = () => currentRows.find((r) => String(r.id) === String(state.selectedId))

  // tombol hanya muncul kalau ada baris terpilih
  function updateButtons() {
    const r = selectedRow()
    const isDraft = r && r.status === 'DRAFT'
    const show = (id, visible) => { $(id).style.display = visible ? '' : 'none' }
    show('btnDelete', !!isDraft)
    show('btnRead', !!r)
    show('btnEdit', !!isDraft)
    show('btnPost', !!isDraft)
    show('btnCopy', !!isDraft)
  }

  function loadDivisi() {
    const sel = $('filterDivisi')
    DIVISI.forEach((d) => {
      const opt = document.createElement('option')
      opt.value = d
      opt.textContent = d
      sel.appendChild(opt)
    })
  }

  // ---- aksi ----
  async function hapus(id) {
    if (!confirm('Hapus Data Terpilih?')) return
    try {
      await dataApi.remove(id)
      state.selectedId = null
      loadData()
    } catch (e) { alert(e.message) }
  }

  async function postData(id) {
    if (!confirm('Post Data?')) return
    try {
      alert(await dataApi.post(id))
    } catch (e) { alert(e.message) }
    loadData()
  }

  function exportCsv() {
    const header = ['No', 'Nama', 'Hubungan Penilai', 'Tanggal', 'Status']
    const lines = currentRows.map((r, i) => [i + 1, getNama(r), r.hubungan_penilai, fmtTanggal(r.tanggal), r.status]
      .map((v) => `"${String(v ?? '').replace(/"/g, '""')}"`).join(','))
    const csv = [header.join(','), ...lines].join('\n')
    const a = document.createElement('a')
    a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }))
    a.download = 'penilaian_360.csv'
    a.click()
    URL.revokeObjectURL(a.href)
  }

  // ---- event ----
  function setStatus(s) {
    state.status = state.status === s ? null : s
    state.page = 1
    btnDraft.classList.toggle('active', state.status === 'DRAFT')
    btnPosted.classList.toggle('active', state.status === 'POSTED')
    loadData()
  }
  btnDraft.addEventListener('click', () => setStatus('DRAFT'))
  btnPosted.addEventListener('click', () => setStatus('POSTED'))

  $('filterDivisi').addEventListener('change', (e) => {
    state.divisi = e.target.value
    e.target.classList.toggle('has-value', !!e.target.value)
    state.page = 1
    loadData()
  })
  $('pageSize').addEventListener('change', (e) => { state.limit = Number(e.target.value); state.page = 1; loadData() })
  $('btnReload').addEventListener('click', loadData)
  $('btnCsv').addEventListener('click', exportCsv)
  $('btnPrev').addEventListener('click', () => { if (state.page > 1) { state.page--; loadData() } })
  $('btnNext').addEventListener('click', () => { if (state.hasNext) { state.page++; loadData() } })

  let t
  const debounced = (fn) => { clearTimeout(t); t = setTimeout(fn, 350) }
  $('searchBox').addEventListener('input', (e) => {
    debounced(() => { state.search = e.target.value.trim(); state.page = 1; loadData() })
  })
  document.querySelectorAll('input[data-col]').forEach((inp) => {
    inp.addEventListener('input', () => {
      debounced(() => { state.colFilters[inp.dataset.col] = inp.value.trim(); state.page = 1; loadData() })
    })
  })

  document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key.toLowerCase() === 'f') {
      e.preventDefault()
      $('searchBox').focus()
    }
  })

  tbody.addEventListener('click', (e) => {
    const tr = e.target.closest('tr[data-id]')
    if (!tr) return
    state.selectedId = String(state.selectedId) === tr.dataset.id ? null : tr.dataset.id
    renderRows()
  })

  const needRow = () => {
    const r = selectedRow()
    if (!r) alert('Pilih salah satu baris dulu.')
    return r
  }
  $('btnRead').addEventListener('click', () => { const r = needRow(); if (r) location.href = `${BASE_PATH}/${r.id}` })
  $('btnEdit').addEventListener('click', () => { const r = needRow(); if (r) location.href = `${BASE_PATH}/${r.id}?action=Edit` })
  $('btnCopy').addEventListener('click', () => { const r = needRow(); if (r) location.href = `${BASE_PATH}/${r.id}?action=Copy` })
  $('btnDelete').addEventListener('click', () => { const r = needRow(); if (r) hapus(r.id) })
  $('btnPost').addEventListener('click', () => { const r = needRow(); if (r) postData(r.id) })

  // ---- jam di bar atas ----
  const HARI = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
  const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  function tickClock() {
    const el = $('t360Clock')
    if (!el) return
    const d = new Date()
    const p = (n) => String(n).padStart(2, '0')
    el.textContent = `${HARI[d.getDay()]}, ${d.getDate()} ${BULAN[d.getMonth()]} ${d.getFullYear()} ${d.getHours()}:${p(d.getMinutes())}:${p(d.getSeconds())}`
  }
  tickClock()
  setInterval(tickClock, 1000)

  // ---- mulai ----
  updateButtons()
  loadDivisi()
  loadData()
})()