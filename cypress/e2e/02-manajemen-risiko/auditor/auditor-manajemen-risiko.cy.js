describe('Auditor - Manajemen Risiko (Pemeriksaan Risiko)', () => {

  beforeEach(() => {
    cy.login('198609232015041001', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
  })

  it('TEST 1: Auditor ter-redirect ke halaman Pemeriksaan Risiko', () => {
    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('h1.mb-1', { timeout: 8000 })
      .should('be.visible')
      .should('contain', 'Pemeriksaan Risiko')
  })

  it('TEST 2: Auditor dapat melihat info profil dan badge level', () => {
    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.contains('small', 'Auditor:')
      .should('exist')

    cy.get('.badge-info')
      .should('exist')
      .invoke('text')
      .should('match', /Ketua|Anggota|Sekretaris/)
  })

  it('TEST 3: Auditor dapat melihat tabel daftar risiko', () => {
    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.contains('h6', 'Daftar Risiko')
      .should('exist')

    cy.get('table.table thead th')
      .should('have.length.greaterThan', 5)

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)
  })

  it('TEST 4: Auditor dapat melihat filter TAHUN', () => {
    cy.visit('/manajemen-risiko')

    cy.get('select[name="tahun"]')
      .should('exist')
      .should('be.visible')

    cy.get('select[name="tahun"] option')
      .should('have.length.greaterThan', 0)
  })

  it('TEST 5: Auditor dapat melihat filter STATUS PEMERIKSAAN', () => {
    cy.visit('/manajemen-risiko')

    cy.get('select[name="status_review"]')
      .should('exist')
      .should('be.visible')

    cy.contains('label', 'STATUS')
      .should('exist')
  })

  it('TEST 6: Auditor dapat melihat filter UNIT KERJA', () => {
    cy.visit('/manajemen-risiko')

    cy.get('select[name="unit_kerja"]')
      .should('exist')
      .should('be.visible')

    cy.contains('label', 'UNIT KERJA')
      .should('exist')
  })

  it('TEST 7: Auditor dapat membuka detail risiko', () => {

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

    cy.get('body').then(($body) => {

      const tombolDetail = $body.find('a').filter(function () {
        return Cypress.$(this).text().trim().includes('Detail')
      })

      if (tombolDetail.length > 0) {

        cy.wrap(tombolDetail.first())
          .click({ force: true })

        cy.url({ timeout: 10000 })
          .should('not.include', '/auditor/manajemen-risiko')

      } else {

        cy.log('SKIP: Tombol Detail tidak ditemukan')
      }

    })
  })

  it('TEST 8: Auditor dapat menggunakan filter tahun', () => {

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('select[name="tahun"] option')
      .then((options) => {

        if (options.length > 1) {

          const value = options[1].value

          cy.get('select[name="tahun"]')
            .select(value)

          cy.url({ timeout: 10000 })
            .should('include', 'tahun=' + value)

          cy.url()
            .should('include', '/auditor/manajemen-risiko')
        }
      })
  })

  it('TEST 9: Auditor dapat melihat kolom Status dan Aksi di tabel', () => {

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('table.table thead')
      .should('contain.text', 'Status')

    cy.get('table.table thead')
      .should('contain.text', 'Aksi')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

    cy.get('table.table tbody')
      .invoke('text')
      .should('not.be.empty')
  })

  it('TEST 10: Auditor dapat melihat skor dan tingkat risiko', () => {

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('table.table thead')
      .should('contain.text', 'Skor')

    cy.get('table.table thead')
      .should('contain.text', 'Tingkat')

    cy.get('table.table tbody tr')
      .should('have.length.greaterThan', 0)

    cy.get('table.table tbody')
      .invoke('text')
      .should('not.be.empty')
  })

})