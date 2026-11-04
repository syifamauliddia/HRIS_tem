(function () {
  // ================= PENGATURAN =================
  const API_URL = 'http://localhost:3000/api/transaction-360'
  const BASE_PATH = '/penilaian-360'

  // HARUS sama dengan nilai di transaksi_360_index.js
  // true  = simpan di browser (localStorage)
  // false = simpan ke server.js + MySQL
  const USE_DUMMY = true

  const STORE_KEY = 't360_rows'
  // ==============================================

  // ---- DATA MASTER (dummy; server.js belum punya endpoint karyawan & template) ----
  const EMPLOYEES = [
    { id: 1, kode: 'EMP-001', nama: 'Syifa Mauliddia', jabatan: 'Manager IT', divisi: 'Information Technology', level: 'Manager', atasan_id: 2 },
    { id: 2, kode: 'EMP-002', nama: 'Budi Hartono', jabatan: 'Direktur Holding', divisi: 'Information Technology', level: 'Direktur Holding', atasan_id: null },
    { id: 3, kode: 'EMP-003', nama: 'Julyandra Andi Santoso', jabatan: 'Software Engineer', divisi: 'Information Technology', level: 'Staff', atasan_id: 1 },
    { id: 4, kode: 'EMP-004', nama: 'Khabibi Al Munif', jabatan: 'IT Support', divisi: 'Information Technology', level: 'Staff', atasan_id: 1 },
    { id: 5, kode: 'EMP-005', nama: 'Dewi Lestari', jabatan: 'HR Staff', divisi: 'HRD', level: 'Staff', atasan_id: 6 },
    { id: 6, kode: 'EMP-006', nama: 'Agus Prasetyo', jabatan: 'HR Manager', divisi: 'HRD', level: 'Manager', atasan_id: 2 },
    { id: 7, kode: 'EMP-007', nama: 'Nadia Putri', jabatan: 'Staff Finance', divisi: 'Finance', level: 'Staff', atasan_id: 8 },
    { id: 8, kode: 'EMP-008', nama: 'Fajar Ramadhan', jabatan: 'Finance Manager', divisi: 'Finance', level: 'Manager', atasan_id: 2 }
  ]

  const TIPE = [
    { id: 1, value: 'Kompetensi SoftSkill' },
    { id: 2, value: 'KPI' }
  ]
  const ASSESS = [
    { id: 1, tipe: 1, deskripsi: 'Penilaian Direktur', items: [0, 1, 2, 3] },
    { id: 2, tipe: 1, deskripsi: 'Penilaian Manager', items: [0, 1, 2, 3] },
    { id: 3, tipe: 2, deskripsi: 'KPI Staff', items: [0, 3] }
  ]
  const opt = (arr) => arr.map((text, k) => ({ text, nilai: 5 - k }))
  const BANK = [
    {
      pertanyaan: 'Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?', bobot: 3,
      options: opt(['Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya', 'Sering menetapkan target kerja yang tinggi untuk dirinya', 'Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan', 'Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum', 'Tidak pernah menetapkan target kerja sendiri'])
    },
    {
      pertanyaan: 'Bagaimana kemampuan individu dalam bekerja sama dengan tim?', bobot: 2,
      options: opt(['Selalu aktif membangun kerja sama dan membantu rekan tim', 'Sering bekerja sama dengan baik bersama tim', 'Dapat bekerja sama jika diperlukan', 'Kurang aktif dalam kerja sama tim', 'Sulit bekerja sama dengan tim'])
    },
    {
      pertanyaan: 'Bagaimana individu berkomunikasi dengan atasan, rekan, dan bawahan?', bobot: 2,
      options: opt(['Selalu berkomunikasi dengan jelas, terbuka, dan tepat waktu', 'Sering berkomunikasi dengan jelas dan terbuka', 'Komunikasi cukup baik untuk kebutuhan pekerjaan', 'Kurang jelas dalam menyampaikan informasi', 'Sulit berkomunikasi dan sering menimbulkan salah paham'])
    },
    {
      pertanyaan: 'Bagaimana individu menyelesaikan masalah dan mengambil keputusan?', bobot: 3,
      options: opt(['Selalu menyelesaikan masalah dengan cepat dan keputusan tepat', 'Sering mengambil keputusan yang baik', 'Mampu menyelesaikan masalah umum dengan arahan', 'Kurang tegas dan sering menunda keputusan', 'Tidak mampu menyelesaikan masalah secara mandiri'])
    }
  ]

  // ---- siapa yang login (penilai) ----
  const u = JSON.parse(localStorage.getItem('user') || '{}')
  const ud = u.data || u
  const ME = EMPLOYEES.find((e) => ud.name && e.nama.toLowerCase().startsWith(String(ud.name).toLowerCase())) || EMPLOYEES[0]

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

  // ---- hubungan penilai ----
  function relation(t) {
    if (String(t.id) === String(ME.id)) return 'Diri Sendiri'
    if (t.atasan_id && String(t.atasan_id) === String(ME.id)) return 'Bawahan'
    if (ME.atasan_id && String(ME.atasan_id) === String(t.id)) return 'Atasan'
    if (ME.atasan_id && String(ME.atasan_id) === String(t.atasan_id)) return 'Rekan'
    return 'Lainnya'
  }
  // boleh dinilai: punya hubungan, atau satu divisi
  const allowed = (t) => relation(t) !== 'Lainnya' || t.divisi === ME.divisi

  // ---- modal karyawan ----
  const empModal = new bootstrap.Modal($('empModal'))
  function renderEmpList() {
    const q = $('empSearch').value.trim().toLowerCase()
    const rows = EMPLOYEES.filter(allowed).filter((e) => {
      const atasan = (EMPLOYEES.find((x) => x.id === e.atasan_id) || {}).nama || ''
      return !q || [e.kode, e.nama, e.jabatan, e.divisi, atasan].join(' ').toLowerCase().includes(q)
    })
    $('empBody').innerHTML = rows.length ? rows.map((e, i) => {
      const atasan = (EMPLOYEES.find((x) => x.id === e.atasan_id) || {}).nama || '-'
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
    S.questions = a ? a.items.map((idx) => ({ kategori: 'Komponen Penilaian / KPI', ...BANK[idx] })) : []
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
  const getStore = () => { try { return JSON.parse(localStorage.getItem(STORE_KEY)) || [] } catch (e) { return [] } }
  const setStore = (rows) => localStorage.setItem(STORE_KEY, JSON.stringify(rows))

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
      if (USE_DUMMY) {
        const rows = getStore()
        if (isEdit) {
          const i = rows.findIndex((r) => String(r.id) === String(recId))
          if (i === -1) throw new Error('Data tidak ditemukan')
          rows[i] = { ...rows[i], ...payload }
        } else {
          rows.push({ id: rows.reduce((m, r) => Math.max(m, Number(r.id) || 0), 0) + 1, ...payload, status: 'DRAFT' })
        }
        setStore(rows)
      } else {
        const res = await fetch(isEdit ? `${API_URL}/${recId}` : API_URL, {
          method: isEdit ? 'PUT' : 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        const j = await res.json().catch(() => ({}))
        if (!res.ok || j.status !== 'success') throw new Error(j.message || 'Gagal menyimpan data')
      }
      alert('Data Penilaian 360 Berhasil Disimpan')
      location.href = `${BASE_PATH}?reload=${Date.now()}`
    } catch (e) {
      console.error(e)
      alert('Gagal menyimpan: ' + e.message + (USE_DUMMY ? '' : '\nPastikan server.js berjalan di port 3000.'))
    } finally {
      saving = false
    }
  }

  // ---- muat data lama (Edit / Read / Copy) ----
  async function loadExisting() {
    let r
    if (USE_DUMMY) {
      r = getStore().find((x) => String(x.id) === String(recId))
    } else {
      const res = await fetch(`${API_URL}/${recId}`)
      const j = await res.json()
      r = j.data
    }
    if (!r) { alert('Data tidak ditemukan'); location.href = BASE_PATH; return }

    const emp = EMPLOYEES.find((e) => String(e.id) === String(r.employee_id)) ||
      { id: r.employee_id, nama: r.nama || '', jabatan: r.nama_jabatan || '', divisi: r.nama_divisi || '', level: r.nama_level || '' }
    $('fHubungan').value = r.hubungan_penilai || ''
    selectEmployee(emp, true)
    $('fHubungan').value = r.hubungan_penilai || ''
    $('whoJabatan').textContent = `${emp.jabatan || 'Jabatan'}${r.hubungan_penilai ? ' · ' + r.hubungan_penilai : ''}`
    $('fTanggal').value = toYmd(r.tanggal)

    const a = ASSESS.find((x) => String(x.id) === String(r.master_assessment_id))
    const tipe = TIPE.find((t) => t.value === r.tipe_penilaian) || (a ? TIPE.find((t) => t.id === a.tipe) : null)
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

  // ---- mulai ----
  $('modeLabel').textContent = { Tambah: 'Create', Edit: 'Edit', Copy: 'Copy', Read: 'Read' }[mode]
  $('fPenilai').value = ME.nama
  fillTipe()
  fillAssess()
  renderQuestions()

  if (!editable) {
    ['fTanggal', 'fTipe', 'fAssess', 'fCatatan1', 'fCatatan2', 'fCatatan3', 'fCatatan4'].forEach((id) => { $(id).disabled = true })
    $('fKaryawan').style.cursor = 'default'
    $('btnReset').style.display = 'none'
    $('btnSave').style.display = 'none'
    $('hintCtrlS').style.display = 'none'
    $('btnKembali').style.display = ''
  }
  if (!isCreate) loadExisting().catch((e) => { console.error(e); alert('Gagal memuat data: ' + e.message) })
})()