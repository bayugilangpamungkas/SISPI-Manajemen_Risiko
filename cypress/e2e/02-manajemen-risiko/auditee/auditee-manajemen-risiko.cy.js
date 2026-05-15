describe('Auditee - Manajemen Risiko (Pemantauan Risiko)', () => {

  beforeEach(() => {
    // Login dengan user yang sudah confirmed sebagai Auditee (role: Auditee, PIC)
    cy.login('Auditee2', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
  })

  it('TEST 1: Auditee ter-redirect ke halaman Pemantauan Risiko', () => {
    cy.visit('/manajemen-risiko')

    // Tunggu redirect ke halaman auditee (route: manajemen-risiko.auditee.index)
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi halaman menampilkan "Pemantauan Risiko"
    cy.get('h1.mb-1', { timeout: 8000 })
      .should('be.visible')
      .should('contain', 'Pemantauan Risiko')
  })

  it('TEST 2: Auditee dapat melihat info profil dan unit kerja', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi ada label "Unit Kerja:"
    cy.contains('small', 'Unit Kerja:').should('exist')

    // Validasi ada nama unit kerja yang ditampilkan
    cy.get('small strong').should('have.length.greaterThan', 0)
  })

  it('TEST 3: Auditee dapat melihat tabel daftar risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi ada tabel dengan header "Daftar Risiko"
    cy.contains('h6', 'Daftar Risiko').should('exist')

    // Validasi ada kolom-kolom penting di header table
    cy.get('table.table thead th').should('have.length.greaterThan', 5)

    // Validasi ada minimal 1 baris data atau pesan "Tidak Ada Data"
    cy.get('table.table tbody tr').should('have.length.greaterThan', 0)
  })

  it('TEST 4: Auditee dapat melihat filter TAHUN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi ada filter dropdown TAHUN
    cy.get('select[name="tahun"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada minimal 1 option tahun
    cy.get('select[name="tahun"] option').should('have.length.greaterThan', 0)
  })

  it('TEST 5: Auditee dapat melihat filter PILIH KEGIATAN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi ada filter dropdown "PILIH KEGIATAN"
    cy.get('select[name="id_kegiatan"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada minimal 1 option kegiatan
    cy.get('select[name="id_kegiatan"] option').should('have.length.greaterThan', 0)
  })

  it('TEST 6: Auditee dapat melihat filter STATUS PEMANTAUAN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Validasi ada filter dropdown "STATUS PEMANTAUAN"
    cy.get('select[name="status_review"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada label untuk status filter
    cy.contains('label', 'STATUS').should('exist')
    cy.contains('label', 'PEMANTAUAN').should('exist')
  })

  it('TEST 7: Auditee dapat menggunakan filter TAHUN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Ambil option tahun kedua jika ada
    cy.get('select[name="tahun"] option').then((options) => {
      if (options.length > 1) {
        const value = options[1].value
        cy.get('select[name="tahun"]').select(value)

        // Tunggu filter diterapkan
        cy.url({ timeout: 10000 })
          .should('include', 'tahun=' + value)

        // Validasi masih di halaman auditee
        cy.url().should('include', '/auditee/manajemen-risiko')
      }
    })
  })

  it('TEST 8: Auditee dapat menggunakan filter KEGIATAN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Ambil option kegiatan kedua jika ada
    cy.get('select[name="id_kegiatan"] option').then((options) => {
      if (options.length > 1) {
        const value = options[1].value
        cy.get('select[name="id_kegiatan"]').select(value)

        // Tunggu filter diterapkan
        cy.url({ timeout: 10000 })
          .should('include', 'id_kegiatan=' + value)

        // Validasi masih di halaman auditee
        cy.url().should('include', '/auditee/manajemen-risiko')
      }
    })
  })

  it('TEST 9: Auditee dapat melihat tombol aksi untuk setiap risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek ada data di tabel
    cy.get('table.table tbody tr').then((rows) => {
      if (rows.length > 0) {
        // Cari tombol aksi di baris pertama
        cy.get('table.table tbody tr').first().within(() => {
          cy.get('a.btn-sm, button.btn-sm').should('have.length.greaterThan', 0)
        })
      }
    })
  })

  it('TEST 10: Auditee dapat melihat kolom Status di table', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek ada kolom Status
    cy.get('table.table thead').should('contain', 'Status')

    // Cek ada badge status di body
    cy.get('table.table tbody .badge').should('have.length.greaterThan', 0)
  })

  it('TEST 11: Auditee dapat melihat skor dan tingkat risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek ada kolom Skor
    cy.get('table.table thead').should('contain', 'Skor')

    // Cek ada kolom Tingkat
    cy.get('table.table thead').should('contain', 'Tingkat')

    // Cek ada data level risiko
    cy.get('table.table tbody').should('satisfy', (el) => {
      const text = el.text()
      return text.includes('High') || text.includes('Moderate') || text.includes('Low') || text.includes('Extreme')
    })
  })

  it('TEST 12: Auditee dapat membuka detail risiko dari tombol aksi', () => {
    // ✅ STEP 1: Visit halaman manajemen risiko auditee
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // ✅ STEP 2: Ambil tombol detail dari baris pertama
    // Berdasarkan gambar Inspect Element, kita gunakan atribut title/data-original-title
    cy.get('table.table tbody tr', { timeout: 8000 })
      .should('have.length.greaterThan', 0) // Pastikan tabel ada isinya
      .first()
      .find('a[data-original-title="Lihat Proses Audit"], a[title="Lihat Proses Audit"], a.btn-primary')
      .first()
      .as('btnDetail')

    // ✅ STEP 3: Klik tombol detail
    // Menggunakan force: true untuk memastikan klik tembus meskipun ada tooltip/overlay
    cy.get('@btnDetail').should('be.visible').click({ force: true })

    // ✅ STEP 4: Validasi URL setelah perpindahan halaman
    // Menggunakan regex \d+ untuk menangkap ID dinamis (seperti ID 35 di gambar Anda)
    cy.url({ timeout: 15000 })
      .should('match', /\/auditee\/manajemen-risiko\/\d+\/detail/)

    // ✅ STEP 5: Validasi konten halaman detail sudah loaded
    // Kita pastikan ada elemen judul (h1/h2) yang munculhallo
    cy.get('h1, h2, .card-title', { timeout: 10000 })
      .should('be.visible')
      .then(($el) => {
        // Memastikan teks judul mengandung kata 'Detail' atau 'Proses' 
        // sesuai dengan konteks tombol "Lihat Proses Audit"
        const text = $el.text().toLowerCase();
        expect(text).to.match(/detail|proses|risiko/);
      })

    // Tambahan: Pastikan body atau container utama tidak kosong
    cy.get('.card-body, .main-card').should('exist').and('not.be.empty')
  })

  it('TEST 13: Auditee dapat melihat notifikasi perbaikan jika ada', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek apakah ada badge notifikasi perbaikan (hanya muncul jika ada risiko perlu perbaikan)
    cy.get('body').then((body) => {
      const notificationBadge = body.find('.badge-warning:contains("Perlu Perbaikan")')

      if (notificationBadge.length > 0) {
        cy.wrap(notificationBadge)
          .should('be.visible')
          .should('contain', 'Perlu Perbaikan')
      } else {
        cy.log('ℹ️ Tidak ada notifikasi perbaikan (semua risiko OK)')
      }
    })
  })

  it('TEST 14: Auditee dapat melihat info kegiatan di table', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek ada data di tabel
    cy.get('table.table tbody tr').then((rows) => {
      if (rows.length > 0) {
        // Validasi ada kolom Kegiatan
        cy.get('table.table thead').should('contain', 'Kegiatan')

        // Validasi ada badge atau info kegiatan di body
        cy.get('table.table tbody .badge').should('have.length.greaterThan', 0)
      }
    })
  })

  it('TEST 15: Auditee dapat melihat info kategori risiko di table', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    // Cek ada kolom Kategori
    cy.get('table.table thead').should('contain', 'Kategori')

    // Validasi ada data kategori di body
    cy.get('table.table tbody .badge-secondary').should('have.length.greaterThan', 0)
  })
})