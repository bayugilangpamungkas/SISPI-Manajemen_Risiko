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

    cy.url()
      .should('include', '/auditee/manajemen-risiko')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

    cy.get('table.table tbody tr')
      .first()
      .find('td')
      .last()
      .within(() => {

        cy.get('a, button, span, i')
          .should('have.length.greaterThan', 0)

      })

  })

  it('TEST 10: Auditee dapat melihat kolom Status di table', () => {

    cy.visit('/manajemen-risiko')

    cy.url()
      .should('include', '/auditee/manajemen-risiko')

    cy.get('table.table thead')
      .should('contain.text', 'Status')

    cy.get('table.table tbody tr')
      .first()
      .invoke('text')
      .should('not.be.empty')

  })

  it('TEST 11: Auditee dapat melihat skor dan tingkat risiko', () => {

    cy.visit('/manajemen-risiko')

    cy.url()
      .should('include', '/auditee/manajemen-risiko')

    cy.get('table.table thead')
      .should('contain.text', 'Skor')

    cy.get('table.table thead')
      .should('contain.text', 'Tingkat')

    cy.get('table.table tbody tr')
      .first()
      .find('td')
      .then(($td) => {

        const text = $td.text().trim()

        expect(text.length).to.be.greaterThan(0)

      })

  })

  it('TEST 12: Auditee dapat membuka detail risiko dari tombol aksi', () => {

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditee/manajemen-risiko')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

    cy.get('body').then(($body) => {

      const btnDetail = $body.find(
        'a[href*="detail"], a[title*="Detail"], a.btn-primary, a.btn-info'
      )

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first())
          .click({ force: true })

        cy.url({ timeout: 15000 })
          .should('match', /detail|show|view/i)

      } else {

        cy.log('Tidak ditemukan tombol detail')

      }

    })

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
        cy.log('?? Tidak ada notifikasi perbaikan (semua risiko OK)')
      }
    })
  })

  it('TEST 14: Auditee dapat melihat info kegiatan di table', () => {

    cy.visit('/manajemen-risiko')

    cy.url()
      .should('include', '/auditee/manajemen-risiko')

    cy.get('table.table thead')
      .should('contain.text', 'Kegiatan')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

  })
})

it('TEST 15: Auditee dapat melihat info kategori risiko di table', () => {

  cy.login('Auditee2', '123456')

  cy.visit('/manajemen-risiko')

  cy.url({ timeout: 10000 }).then((url) => {

    if (url.includes('/login')) {

      throw new Error(
        'User gagal login atau session hilang sebelum TEST 15'
      )

    }

  })

  cy.url()
    .should('include', '/auditee/manajemen-risiko')

  cy.get('table.table thead')
    .should('contain.text', 'Kategori')

})


