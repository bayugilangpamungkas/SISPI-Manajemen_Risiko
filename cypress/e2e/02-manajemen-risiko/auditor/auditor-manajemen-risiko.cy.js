describe('Auditor - Manajemen Risiko (Pemeriksaan Risiko)', () => {

  beforeEach(() => {
    // Login dengan username Auditor (role: Ketua, Anggota, atau Sekretaris)
    cy.login('198609232015041001', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
  })

  it('TEST 1: Auditor ter-redirect ke halaman Pemeriksaan Risiko', () => {
    cy.visit('/manajemen-risiko')

    // Tunggu redirect ke halaman auditor
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi halaman menampilkan "Pemeriksaan Risiko"
    cy.get('h1.mb-1', { timeout: 8000 })
      .should('be.visible')
      .should('contain', 'Pemeriksaan Risiko')
  })

  it('TEST 2: Auditor dapat melihat info profil dan badge level', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi ada label "Auditor:"
    cy.contains('small', 'Auditor:').should('exist')

    // Validasi ada badge dengan LEVEL auditor
    cy.get('.badge-info')
      .should('exist')
      .invoke('text')
      .should('match', /Ketua|Anggota|Sekretaris/)
  })

  it('TEST 3: Auditor dapat melihat tabel daftar risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi ada tabel dengan header "Daftar Risiko"
    cy.contains('h6', 'Daftar Risiko').should('exist')

    // Validasi ada kolom-kolom penting di header table
    cy.get('table.table thead th').should('have.length.greaterThan', 5)

    // Validasi ada minimal 1 baris data
    cy.get('table.table tbody tr').should('have.length.greaterThan', 0)
  })

  it('TEST 4: Auditor dapat melihat filter TAHUN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi ada filter dropdown TAHUN
    cy.get('select[name="tahun"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada minimal 1 option
    cy.get('select[name="tahun"] option').should('have.length.greaterThan', 0)
  })

  it('TEST 5: Auditor dapat melihat filter STATUS PEMERIKSAAN', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi ada filter dropdown "STATUS PEMERIKSAAN"
    cy.get('select[name="status_review"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada label untuk status filter
    cy.contains('label', 'STATUS').should('exist')
  })

  it('TEST 6: Auditor dapat melihat filter UNIT KERJA', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Validasi ada filter dropdown "UNIT KERJA"
    cy.get('select[name="unit_kerja"]', { timeout: 5000 })
      .should('exist')
      .should('be.visible')

    // Validasi ada label untuk unit kerja filter
    cy.contains('label', 'UNIT KERJA').should('exist')
  })

  it('TEST 7: Auditor dapat membuka detail risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Cari tombol Detail di kolom Aksi
    cy.get('table.table tbody tr').first().within(() => {
      cy.get('a.btn-sm.btn-info').first().then((btn) => {
        if (btn.length > 0 && btn.attr('title') === 'Lihat Detail') {
          cy.wrap(btn).click()

          // Tunggu halaman detail terbuka
          cy.url({ timeout: 10000 })
            .should('include', '/manajemen-risiko/auditor/show-detail')
        } else {
          cy.log('ℹ️ Tombol detail tidak tersedia')
        }
      })
    })
  })

  it('TEST 8: Auditor dapat menggunakan filter tahun', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Ambil option tahun kedua jika ada
    cy.get('select[name="tahun"] option').then((options) => {
      if (options.length > 1) {
        const value = options[1].value
        cy.get('select[name="tahun"]').select(value)

        // Tunggu filter diterapkan
        cy.url({ timeout: 10000 })
          .should('include', 'tahun=' + value)

        // Validasi masih di halaman auditor
        cy.url().should('include', '/auditor/manajemen-risiko')
      }
    })
  })

  it('TEST 9: Auditor dapat melihat kolom Status dan Aksi di table', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Cek kolom Status
    cy.get('table.table thead').should('contain', 'Status')

    // Cek kolom Aksi
    cy.get('table.table thead').should('contain', 'Aksi')

    // Cek ada badge status di body
    cy.get('table.table tbody .badge').should('have.length.greaterThan', 0)
  })

  it('TEST 10: Auditor dapat melihat skor dan tingkat risiko', () => {
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // Cek kolom Skor
    cy.get('table.table thead').should('contain', 'Skor')

    // Cek kolom Tingkat
    cy.get('table.table thead').should('contain', 'Tingkat')

    // Cek ada data level risiko (High, Moderate, Low, Extreme)
    cy.get('table.table tbody').should('satisfy', (el) => {
      const text = el.text()
      return text.includes('High') || text.includes('Moderate') || text.includes('Low') || text.includes('Extreme')
    })
  })

})
