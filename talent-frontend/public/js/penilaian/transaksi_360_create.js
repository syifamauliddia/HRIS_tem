(function () {
  // ================= PENGATURAN =================
  const API_URL = 'http://localhost:3000/api/transaction-360'
  const BASE_PATH = '/penilaian-360'

  // HARUS sama dengan nilai di transaksi_360_index.js
  // true  = simpan di browser (localStorage)
  // false = simpan ke server.js + MySQL
  const USE_DUMMY = false

  const EMPLOYEES_API = 'http://localhost:3000/api/employees'
  const MASTERS_API = 'http://localhost:3000/api/master-assessments'
  // ==============================================

  // ---- DATA MASTER (dimuat dari API) ----
  let EMPLOYEES = []
  let TIPE = []
  let ASSESS = []
  let ME = null

  // ---- siapa yang login (penilai) ----
  const u = JSON.parse(localStorage.getItem('user') || '{}')
  const ud = u.data || u

  // ---- mode halaman ----
  const parts = location.pathname.replace(/\/+$/, '').split('/')
  const last = parts[parts.length - 1]
  const isCreate = last === 'create'
  const recId = isCreate ? null : last
  const qAction = new URLSearchParams(location.search).get('action')
  const mode = isCreate ? 'Tambah' : (qAction === 'Edit' || qAction === 'Copy' ? qAction : 'Read')
  const editable = mode !== 'Read'

  // ---- state ----
  const S = { employee: null, tipeId: '', assessId: '', questions: [], answers: {} }

  const $ = (id) => document.getElementById(id)
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))

  const toYmd = (t) => {
    if (!t) return ''
    if (/^\d{4}-\d{2}-\d{2}$/.test(t)) return t
    const d = new Date(t)
    if (isNaN(d)) return ''
    const p = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`
  }

  // ---- helper API ----
  async function fetchJSON(url, options) {
    const res = await fetch(url, options)
    const j = await res.json().catch(() => ({}))
    if (!res.ok || (j.status && j.status !== 'success')) {
      throw new Error(j.message || `Gagal mengambil data (${res.status})`)
    }
    return j.data !== undefined ? j.data : j
  }

  // Ubah satu master (dari /api/master-assessments) menjadi daftar pertanyaan.
  function buildQuestions(master) {
    return (master.indicators || []).map((ind) => ({
      kategori: ind.kategori || 'Komponen Penilaian / KPI',
      pertanyaan: ind.nama_indicator || '',
      bobot: Number(ind.bobot) || 0,
      options: (ind.details || []).map((d) => ({
        nilai: Number(d.nilai) || 0,
        text: d.keterangan || ''
      }))
    }))
  }

  async function initData() {
    const [employees, mastersRaw] = await Promise.all([
      fetchJSON(EMPLOYEES_API),
      fetchJSON(MASTERS_API)
    ])

    EMPLOYEES = Array.isArray(employees) ? employees : []

    const masters = (Array.isArray(mastersRaw) ? mastersRaw : [])
      .filter((m) => String(m.status) === 'Active' || String(m.status) === '1')

    // TIPE = unique tipe_penilaian
    TIPE = []
    const seen = new Set()
    masters.forEach((m) => {
      const name = m.tipe_penilaian
      if (!name || seen.has(name)) return
      seen.add(name)
      TIPE.push({ id: TIPE.length + 1, value: name })
    })

    // ASSESS memetakan master ke tipe + daftar pertanyaan dari indicators
    ASSESS = masters.map((m) => {
      const t = TIPE.find((x) => x.value === m.tipe_penilaian)
      return {
        id: m.id,
        tipe: t ? t.id : null,
        deskripsi: m.deskripsi || '',
        questions: buildQuestions(m)
      }
    })

    // Penilai yang login: cocokkan nama dari localStorage, fallback ke karyawan pertama.
    ME = EMPLOYEES.find((e) => ud.name && String(e.nama || '').toLowerCase().startsWith(String(ud.name).toLowerCase())) ||
      EMPLOYEES[0] ||
      { id: ud.id || null, nama: ud.name || '', jabatan: '', divisi: '', level: '', atasan_id: null }
  }

  // ---- hubungan penilai ----
  function relation(t) {
    if (!t || !ME) return 'Lainnya'
    if (String(t.id) === String(ME.id)) return 'Diri Sendiri'
    if (t.atasan_id && String(t.atasan_id) === String(ME.id)) return 'Bawahan'
    if (ME.atasan_id && String(ME.atasan_id) === String(t.id)) return 'Atasan'
    if (ME.atasan_id && String(ME.atasan_id) === String(t.atasan_id)) return 'Rekan'
    return 'Lainnya'
  }
  // boleh dinilai: punya hubungan, atau satu divisi
  const allowed = (t) => relation(t) !== 'Lainnya' || (!!ME && t.divisi === ME.divisi)

  // ---- modal karyawan ----
  const empModal = new bootstrap.Modal($('empModal'))
  function renderEmpList() {
    const q = $('empSearch').value.trim().toLowerCase()
    const rows = EMPLOYEES.filter(allowed).filter((e) => {
      const atasan = (EMPLOYEES.find((x) => String(x.id) === String(e.atasan_id)) || {}).nama || ''
      return !q || [e.kode, e.nama, e.jabatan, e.divisi, atasan].join(' ').toLowerCase().includes(q)
    })
    $('empBody').innerHTML = rows.length ? rows.map((e, i) => {
      const atasan = (EMPLOYEES.find((x) => String(x.id) === String(e.atasan_id)) || {}).nama || '-'
      return `<tr data-id="${e.id}"><td class="text-center">${i + 1}</td><td>${esc(e.kode)}</td><td>${esc(e.nama)}</td><td>${esc(atasan)}</td><td>${esc(e.jabatan)}</td><td>${esc(e.divisi)}</td><td>${esc(relation(e))}</td></tr>`
    }).join('') : '<tr><td colspan="7" class="text-center text-muted">Tidak ada karyawan</td></tr>'
  }
  $('fKaryawan').addEventListener('click', () => { if (editable) { $('empSearch').value = ''; renderEmpList(); empModal.show() } })
  $('empSearch').addEventListener('input', renderEmpList)
  $('empBody').addEventListener('click', (e) => {
    const tr = e.target.closest('tr[data-id]')
    if (!tr) return
    selectEmployee(EMPLOYEES.find((x) => String(x.id) === tr.dataset.id))
    empModal.hide()
  })

  function selectEmployee(emp, keepForm) {
    S.employee = emp
    $('fKaryawan').value = emp ? emp.nama : ''
    $('fJabatan').value = emp ? emp.jabatan : ''
    $('fDivisi').value = emp ? emp.divisi : ''
    $('fLevel').value = emp ? emp.level : ''
    $('fHubungan').value = emp ? (emp.atasan_id !== undefined ? relation(emp) : $('fHubungan').value) : ''
    $('whoNama').textContent = emp ? emp.nama : ''
    $('whoJabatan').textContent = emp ? `${emp.jabatan || 'Jabatan'}${$('fHubungan').value ? ' · ' + $('fHubungan').value : ''}` : 'Jabatan'
    if (!keepForm) {
      // ganti karyawan -> pilihan penilaian diulang
      $('fTipe').value = ''
      S.tipeId = ''
      fillAssess()
      resetQuestions()
    }
  }

  // ---- tipe & penilaian ----
  function fillTipe() {
    $('fTipe').innerHTML = '<option value="">Pilih Tipe Penilaian</option>' + TIPE.map((t) => `<option value="${t.id}">${esc(t.value)}</option>`).join('')
  }
  function fillAssess() {
    const list = ASSESS.filter((a) => String(a.tipe) === String(S.tipeId))
    $('fAssess').innerHTML = '<option value="">Pilih Penilaian Karyawan</option>' + list.map((a) => `<option value="${a.id}">${esc(a.deskripsi)}</option>`).join('')
    $('fAssess').disabled = !editable || !S.tipeId
    $('fDeskripsi').value = ''
    S.assessId = ''
  }
  function resetQuestions() {
    S.questions = []
    S.answers = {}
    renderQuestions()
  }
  $('fTipe').addEventListener('change', (e) => { S.tipeId = e.target.value; fillAssess(); resetQuestions() })
  $('fAssess').addEventListener('change', (e) => {
    S.assessId = e.target.value
    const a = ASSESS.find((x) => String(x.id) === String(S.assessId))
    $('fDeskripsi').value = a ? a.deskripsi : ''
    S.answers = {}
    S.questions = a ? a.questions.map((q) => ({ ...q })) : []
    renderQuestions()
  })

  // ---- hitung nilai ----
  function calc() {
    let skor = 0
    let bobot = 0
    S.questions.forEach((q, i) => {
      skor += (S.answers[i] || 0) * q.bobot
      bobot += q.bobot
    })
    let hasil = bobot > 0 ? (skor / (bobot * 5)) * 10 : 0
    if (isNaN(hasil) || !isFinite(hasil) || hasil < 0) hasil = 0
    if (hasil > 99.99) hasil = 99.99
    return hasil
  }

  function renderQuestions() {
    $('questions').innerHTML = S.questions.map((q, i) => {
      const sel = S.answers[i]
      const total = sel ? sel * q.bobot : 0
      return `
      <div class="f-q">
        <div class="f-qh"><span>${esc(q.kategori)}</span><span class="d-flex align-items-center gap-2">Total Nilai <span class="f-badge w">${total}</span></span></div>
        <div class="f-qs">
          <div class="f-qt">${esc(q.pertanyaan)}</div>
          <div class="d-flex align-items-center gap-3" style="font-size:12px;">
            <span>Bobot <span class="f-badge d">${q.bobot}</span></span>
            <span>Rating <span class="f-badge d">${sel || 0}</span></span>
            <span>Nilai <span class="f-badge d">${total}</span></span>
          </div>
        </div>
        <div class="f-opts">
          ${q.options.map((o) => `
            <div class="f-opt ${sel === o.nilai ? 'sel' : ''} ${editable ? '' : 'disabled'}" data-q="${i}" data-v="${o.nilai}">
              <div class="f-ot">${esc(o.text)}</div>
              <div class="f-of"><span>${o.nilai}</span><input type="radio" ${sel === o.nilai ? 'checked' : ''} ${editable ? '' : 'disabled'}></div>
            </div>`).join('')}
        </div>
      </div>`
    }).join('')

    $('scoreBox').textContent = calc().toFixed(2)
    $('notesWrap').style.display = S.questions.length ? '' : 'none'
  }

  $('questions').addEventListener('click', (e) => {
    if (!editable) return
    const el = e.target.closest('.f-opt')
    if (!el) return
    S.answers[el.dataset.q] = Number(el.dataset.v)
    renderQuestions()
  })

  // ---- simpan ----
  function buildPayload() {
    const tanggal = $('fTanggal').value
    const tipe = TIPE.find((t) => String(t.id) === String(S.tipeId))
    const a = ASSESS.find((x) => String(x.id) === String(S.assessId))
    return {
      employee_id: S.employee.id,
      penilai_id: ME.id,
      master_assessment_id: a ? a.id : null,
      periode: tanggal ? tanggal.slice(0, 4) : null,
      hubungan_penilai: $('fHubungan').value,
      tanggal,
      total_nilai: Number(calc().toFixed(2)),
      nama: S.employee.nama,
      nama_jabatan: S.employee.jabatan,
      nama_divisi: S.employee.divisi,
      nama_level: S.employee.level,
      tipe_penilaian: tipe ? tipe.value : null,
      penilaian: a ? a.deskripsi : null,
      catatan_1: $('fCatatan1').value,
      catatan_2: $('fCatatan2').value,
      catatan_3: $('fCatatan3').value,
      catatan_4: $('fCatatan4').value,
      details: S.questions.map((q, i) => ({
        kategori: q.kategori,
        pertanyaan: q.pertanyaan,
        bobot: q.bobot,
        jawaban: S.answers[i] || 0,
        total_nilai: (S.answers[i] || 0) * q.bobot,
        options: q.options
      }))
    }
  }

  let saving = false
  async function save() {
    if (!editable || saving) return
    if (!S.employee) return alert('Pilih karyawan yang dinilai dulu.')
    if (!$('fTanggal').value) return alert('Periode (tanggal) wajib diisi.')
    if (!S.assessId) return alert('Pilih tipe penilaian dan penilaian (pertanyaan / KPI) dulu.')

    const payload = buildPayload()
    const isEdit = mode === 'Edit'
    saving = true
    try {
      const res = await fetch(isEdit ? `${API_URL}/${recId}` : API_URL, {
        method: isEdit ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      const j = await res.json().catch(() => ({}))
      if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Gagal menyimpan data')
      alert('Data Penilaian 360 Berhasil Disimpan')
      location.href = `${BASE_PATH}?reload=${Date.now()}`
    } catch (e) {
      console.error(e)
      alert('Gagal menyimpan: ' + e.message + '\nPastikan server.js berjalan di port 3000.')
    } finally {
      saving = false
    }
  }

  // ---- muat data lama (Edit / Read / Copy) ----
  async function loadExisting() {
    const r = await fetchJSON(`${API_URL}/${recId}`)
    if (!r) { alert('Data tidak ditemukan'); location.href = BASE_PATH; return }

    const emp = EMPLOYEES.find((e) => String(e.id) === String(r.employee_id)) ||
      { id: r.employee_id, nama: r.nama || '', jabatan: r.nama_jabatan || '', divisi: r.nama_divisi || '', level: r.nama_level || '' }
    $('fHubungan').value = r.hubungan_penilai || ''
    selectEmployee(emp, true)
    $('fHubungan').value = r.hubungan_penilai || ''
    $('whoJabatan').textContent = `${emp.jabatan || 'Jabatan'}${r.hubungan_penilai ? ' · ' + r.hubungan_penilai : ''}`
    $('fTanggal').value = toYmd(r.tanggal)

    const a = ASSESS.find((x) => String(x.id) === String(r.master_assessment_id))
    const tipe = TIPE.find((t) => t.value === r.tipe_penilaian) || (a ? TIPE.find((t) => String(t.id) === String(a.tipe)) : null)
    S.tipeId = tipe ? String(tipe.id) : ''
    $('fTipe').value = S.tipeId
    fillAssess()
    if (a) { S.assessId = String(a.id); $('fAssess').value = S.assessId; $('fDeskripsi').value = a.deskripsi }

    const det = r.details || (r.detail_json ? JSON.parse(r.detail_json) : [])
    S.questions = det.map((d) => ({ kategori: d.kategori, pertanyaan: d.pertanyaan, bobot: d.bobot, options: d.options || [] }))
    S.answers = {}
    det.forEach((d, i) => { if (d.jawaban) S.answers[i] = Number(d.jawaban) })
    renderQuestions()

    $('fCatatan1').value = r.catatan_1 || ''
    $('fCatatan2').value = r.catatan_2 || ''
    $('fCatatan3').value = r.catatan_3 || ''
    $('fCatatan4').value = r.catatan_4 || ''
    if (!det.length) $('notesWrap').style.display = ''
  }

  // ---- event tombol ----
  $('btnSave').addEventListener('click', save)
  $('btnReset').addEventListener('click', () => { if (confirm('Anda yakin akan mereset data ini?')) location.reload() })
  document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key.toLowerCase() === 's') { e.preventDefault(); save() }
  })

  // ---- clock & read-only setup ----
  function applyModeUi() {
    $('modeLabel').textContent = { Tambah: 'Create', Edit: 'Edit', Copy: 'Copy', Read: 'Read' }[mode]
    $('fPenilai').value = ME.nama
    if (!editable) {
      ['fTanggal', 'fTipe', 'fAssess', 'fCatatan1', 'fCatatan2', 'fCatatan3', 'fCatatan4'].forEach((id) => { $(id).disabled = true })
      $('fKaryawan').style.cursor = 'default'
      $('btnReset').style.display = 'none'
      $('btnSave').style.display = 'none'
      $('hintCtrlS').style.display = 'none'
      $('btnKembali').style.display = ''
    }
  }

  // ---- jam ----
  const HARI = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
  const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  function tickClock() {
    const d = new Date()
    const p = (n) => String(n).padStart(2, '0')
    $('t360Clock').textContent = `${HARI[d.getDay()]}, ${d.getDate()} ${BULAN[d.getMonth()]} ${d.getFullYear()} ${d.getHours()}:${p(d.getMinutes())}:${p(d.getSeconds())}`
  }
  tickClock()
  setInterval(tickClock, 1000)

  // ---- mulai (async: fetch employee + master dulu) ----
  async function init() {
    try {
      await initData()
    } catch (e) {
      console.error('Gagal memuat data master:', e)
      alert('Gagal memuat data karyawan / template penilaian dari server.\nPastikan server.js berjalan di port 3000.\n\n' + e.message)
      return
    }

    applyModeUi()
    fillTipe()
    fillAssess()
    renderQuestions()

    if (!isCreate) loadExisting().catch((e) => { console.error(e); alert('Gagal memuat data: ' + e.message) })
  }

  init()
})()
