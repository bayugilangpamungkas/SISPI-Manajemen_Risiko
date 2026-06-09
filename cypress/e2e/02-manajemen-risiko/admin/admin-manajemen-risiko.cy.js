describe('Admin - Manajemen Risiko', () => {

  beforeEach(() => {
    cy.login('admin1', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
    cy.get('h1', { timeout: 8000 }).should('be.visible')
  })

  it('TEST 1: Menampilkan halaman data manajemen risiko', () => {
    cy.visit('/manajemen-risiko/data')
    cy.get('h1', { timeout: 8000 }).should('be.visible').should('contain', 'Data Manajemen Risiko')
    cy.get('form#filterForm', { timeout: 5000 }).should('be.visible')
    cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)
  })

  it('TEST 2: Pilih unit kerja dan tampilkan risiko di halaman manajemen risiko', () => {
    cy.visit('/manajemen-risiko/data')
    cy.get('h1', { timeout: 8000 }).should('contain', 'Data Manajemen Risiko')
    cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)

    cy.get('select[name="unit_kerja"]').then((select) => {
      if (select.find('option').length > 1) {
        const optionValue = select.find('option').eq(1).val()
        cy.get('select[name="unit_kerja"]').select(optionValue)
        cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)
        cy.get('a.btn.btn-primary').filter(':has(i.fa-list-ul)').first().as('linkPilihKegiatan')
        cy.wait(500)
        cy.get('@linkPilihKegiatan').should('be.visible').click({ force: true })
        cy.url({ timeout: 8000 }).should('include', '/manajemen-risiko/detail-unit')
        cy.get('h1', { timeout: 8000 }).should('contain', 'Detail Unit Kerja')
        cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)
        cy.get('input[type="checkbox"][name="peta_ids[]"]', { timeout: 8000 }).first().check({ force: true })
        cy.get('button#btn-update-kegiatan', { timeout: 5000 }).should('be.visible').click()
        cy.get('.swal2-confirm', { timeout: 5000 }).should('be.visible').click()
        cy.url({ timeout: 8000 }).should('include', '/manajemen-risiko').should('not.include', '/data')
        cy.get('h1', { timeout: 8000 }).should('contain', 'Manajemen Risiko')
        cy.get('.alert-success', { timeout: 5000 }).should('be.visible')
      }
    })
  })

  it('TEST 3: Assign auditor ke risiko yang belum ditugaskan', () => {
    cy.visit('/manajemen-risiko')
    cy.get('h1', { timeout: 8000 }).should('contain', 'Manajemen Risiko')
    cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)

    // Cari tombol Tugaskan dengan strategi yang lebih robust
    // Strategi 1: Cari di seluruh halaman (bukan hanya dengan class selector)
    cy.get('body').then((body) => {
      const btnTugaskan = body.find('button:contains("Tugaskan")').length

      if (btnTugaskan > 0) {
        // Tombol ditemukan - gunakan selector yang lebih flexible
        cy.contains('button', 'Tugaskan').first().as('btnTugaskan')
        cy.wait(300)
        cy.get('@btnTugaskan').scrollIntoView().should('be.visible')
        cy.get('@btnTugaskan').click({ force: true })

        // Tunggu modal
        cy.get('.modal.show', { timeout: 5000 }).should('be.visible')
        cy.get('.modal.show .modal-title').should('contain', 'Tugaskan Auditor')

        // Pilih auditor
        cy.get('.modal.show select[name="auditor_id"]', { timeout: 5000 }).should('be.visible').select(8)

        // Simpan
        cy.get('.modal.show button[type="submit"]').should('be.visible').click()

        // Validasi success
        cy.get('.modal.show', { timeout: 5000 }).should('not.exist')
        cy.get('.alert-success', { timeout: 5000 }).should('be.visible').should('contain', 'Auditor berhasil ditugaskan')
      } else {
        // Tombol tidak ditemukan - log dan skip test
        cy.log('SKIP: Tidak ada risiko tanpa auditor di halaman ini')
        cy.log('Catatan: Semua risiko mungkin sudah ditugaskan auditor')
      }
    })
  })

  it('TEST 4: Assign auditor dengan API intercept', () => {
    cy.intercept('GET', /\/manajemen-risiko($|[?#]|\/[^\/]*$)/, (req) => {
      req.continue()
    }).as('getManajemenRisiko')

    cy.intercept('POST', /\/manajemen-risiko\/\d+\/assign-auditor/, (req) => {
      req.continue()
    }).as('postAssignAuditor')

    cy.visit('/manajemen-risiko')
    cy.wait('@getManajemenRisiko', { timeout: 8000 })

    cy.get('h1', { timeout: 8000 }).should('contain', 'Manajemen Risiko')
    cy.get('table.table tbody tr', { timeout: 8000 }).should('have.length.greaterThan', 0)

    // Strategi yang sama dengan TEST 3
    cy.get('body').then((body) => {
      const btnTugaskan = body.find('button:contains("Tugaskan")').length

      if (btnTugaskan > 0) {
        cy.contains('button', 'Tugaskan').first().as('btnTugaskan')
        cy.wait(300)
        cy.get('@btnTugaskan').scrollIntoView().should('be.visible')
        cy.get('@btnTugaskan').click({ force: true })

        cy.get('.modal.show select[name="auditor_id"]', { timeout: 5000 }).should('be.visible').select(8)
        cy.get('.modal.show button[type="submit"]').click()

        cy.wait('@postAssignAuditor', { timeout: 5000 })
        cy.get('.modal.show', { timeout: 5000 }).should('not.exist')
        cy.get('.alert-success', { timeout: 5000 }).should('be.visible').should('contain', 'Auditor berhasil ditugaskan')
      } else {
        cy.log('SKIP: Tidak ada risiko tanpa auditor di halaman ini')
        cy.log('Catatan: Semua risiko mungkin sudah ditugaskan auditor')
      }
    })
  })

})
