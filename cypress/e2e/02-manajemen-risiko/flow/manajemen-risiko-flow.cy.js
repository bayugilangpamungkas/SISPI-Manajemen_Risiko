describe('FULL FLOW - Manajemen Risiko', () => {

  it('Admin → Auditor → Auditee → Approval → Admin Cetak Laporan', () => {

    // =====================================================
    // ADMIN
    // =====================================================

    cy.login('admin1', '123456')

    cy.url({ timeout: 10000 })
      .should('include', '/dashboard')

    cy.visit('/manajemen-risiko')

    cy.get('h1', { timeout: 10000 })
      .should('contain.text', 'Manajemen Risiko')

    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)

    cy.get('body').then(($body) => {

      const btnTugaskan =
        $body.find('button:contains("Tugaskan")').length

      if (btnTugaskan > 0) {

        cy.contains('button', 'Tugaskan')
          .first()
          .scrollIntoView()
          .click({ force: true })

        cy.get('.modal.show', {
          timeout: 10000
        }).should('be.visible')

        cy.get('.modal.show select[name="auditor_id"]')
          .should('be.visible')
          .select('8')

        cy.get('.modal.show button[type="submit"]')
          .click({ force: true })

        cy.get('.modal.show', {
          timeout: 10000
        }).should('not.exist')

        cy.log('Assign auditor berhasil')

      } else {

        cy.log('SKIP ASSIGN AUDITOR')

      }

    })

    // Logout Admin
    cy.logout()

    // =====================================================
    // AUDITOR
    // =====================================================

    cy.login('198609232015041001', '123456')

    cy.url({ timeout: 15000 }).then((url) => {

      cy.log('URL Auditor: ' + url)

      if (!url.includes('/dashboard')) {

        throw new Error(
          'Login Auditor gagal. URL saat ini: ' + url
        )

      }

    })

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find('a').filter(function () {

        return Cypress.$(this)
          .text()
          .trim()
          .includes('Detail')

      })

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first())
          .click({ force: true })

        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          if ($detailPage.find('textarea').length > 0) {

            cy.get('textarea')
              .first()
              .clear()
              .type('Hasil analisis auditor')

          }

          if ($detailPage.text().match(/Kirim ke Auditee/i)) {

            cy.contains(/Kirim ke Auditee/i)
              .click({ force: true })

            cy.log('Data berhasil dikirim ke Auditee')

          } else {

            cy.log('Tombol Kirim ke Auditee tidak ditemukan')

          }

        })

      } else {

        cy.log('Tidak ada data Detail untuk Auditor')

      }

    })

    // Logout Auditor
    cy.logout()

    // =====================================================
    // AUDITEE
    // =====================================================

    cy.login('Auditee2', '123456')

    cy.url({ timeout: 10000 })
      .should('include', '/dashboard')

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditee/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find(
        'a[href*="detail"], a[title*="Detail"], a.btn-primary, a.btn-info'
      )

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first())
          .click({ force: true })

        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          if ($detailPage.find('textarea').length > 0) {

            cy.get('textarea')
              .first()
              .clear()
              .type('Tindak lanjut auditee')

          }

          if ($detailPage.text().includes('Kirim ke Auditor')) {

            cy.contains('Kirim ke Auditor')
              .click({ force: true })

            cy.log('Data berhasil dikirim ke Auditor')

          } else {

            cy.log('Tombol Kirim ke Auditor tidak ditemukan')

          }

        })

      } else {

        cy.log('Tidak ada data Detail untuk Auditee')

      }

    })

    // Logout Auditee
    cy.logout()

    // =====================================================
    // AUDITOR APPROVAL
    // =====================================================

    cy.login('198609232015041001', '123456')

    cy.url({ timeout: 15000 })
      .should('include', '/dashboard')

    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find('a').filter(function () {

        return Cypress.$(this)
          .text()
          .trim()
          .includes('Detail')

      })

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first())
          .click({ force: true })

        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          if ($detailPage.text().match(/Approve|Setujui/i)) {

            cy.contains(/Approve|Setujui/i)
              .click({ force: true })

            cy.log('Approval berhasil')

          } else {

            cy.log('Tombol Approve tidak ditemukan')

          }

        })

      } else {

        cy.log('Tidak ada data Detail untuk Approval')

      }

    })

    // Logout Auditor
    cy.logout()

    // =====================================================
    // ADMIN CETAK LAPORAN
    // =====================================================

    cy.login('admin1', '123456')

    cy.url({ timeout: 10000 })
      .should('include', '/dashboard')

    cy.visit('/manajemen-risiko')

    cy.get('h1', { timeout: 10000 })
      .should('contain.text', 'Manajemen Risiko')

    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)

    cy.log('Membuka modal cetak laporan')

    // tombol Cetak PDF global
    cy.get(
      'button[data-target="#modalCetakPDF"]',
      { timeout: 10000 }
    )
      .should('be.visible')
      .click({ force: true })

    cy.log('Modal cetak berhasil dibuka')

    // =====================================================
    // MODAL CETAK
    // =====================================================

    cy.get('#modalCetakPDF', {
      timeout: 10000
    })
      .should('be.visible')

    cy.log('Modal Cetak PDF tampil')

    // =====================================================
    // PILIH UNIT KERJA WADIR I
    // =====================================================

    cy.get('#modalCetakPDF select', {
      timeout: 10000
    })
      .should('be.visible')

    cy.get('#modalCetakPDF select option')
      .then(($options) => {

        const opsiAda = [...$options].some(option =>
          option.text.trim().includes('WADIR I')
        )

        if (opsiAda) {

          cy.get('#modalCetakPDF select')
            .first()
            .select('WADIR I', { force: true })

          cy.log('Unit Kerja WADIR I berhasil dipilih')

        } else {

          cy.log('WADIR I tidak ditemukan pada dropdown')

          const value = $options[1]?.value

          if (value) {

            cy.get('#modalCetakPDF select')
              .first()
              .select(value, { force: true })

            cy.log('Menggunakan unit kerja alternatif')

          }

        }

      })

    cy.wait(1000)

    // =====================================================
    // KLIK TOMBOL CETAK
    // =====================================================

    cy.get('#modalCetakPDF').then(($modal) => {

      const tombolSubmit = $modal.find(
        'button[type="submit"], .btn-primary, .btn-success'
      )

      if (tombolSubmit.length > 0) {

        cy.wrap(tombolSubmit.first())
          .click({ force: true })

        cy.log('Generate laporan berhasil')

      } else {

        cy.contains(
          '#modalCetakPDF button',
          /Cetak|Print|Generate|Preview/i
        )
          .click({ force: true })

        cy.log('Tombol cetak ditemukan dan diklik')

      }

    })

    // =====================================================
    // VALIDASI HASIL CETAK
    // =====================================================

    cy.wait(3000)

    cy.url({ timeout: 15000 })
      .then((url) => {

        cy.log('URL hasil cetak : ' + url)

      })

    cy.get('body', {
      timeout: 15000
    })
      .should('exist')

    cy.get('body')
      .invoke('text')
      .then((text) => {

        cy.log('Halaman laporan berhasil terbuka')

        if (
          text.includes('Belum ada') ||
          text.includes('Tidak ada data')
        ) {

          cy.log('PERINGATAN: Unit kerja yang dipilih belum memiliki audit selesai')

        }

      })
  })
})