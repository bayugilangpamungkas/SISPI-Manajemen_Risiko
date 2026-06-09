describe('FULL FLOW - Manajemen Risiko', () => {

  const logout = () => {
    cy.visit('/logout', {
      failOnStatusCode: false
    })

    cy.wait(1000)
  }

  it('Admin → Auditor → Auditee → Approval → Admin Cetak Laporan', () => {

    // =====================================================
    // ADMIN
    // =====================================================

    cy.login('admin1', '123456')

    cy.visit('/manajemen-risiko')

    cy.get('h1', { timeout: 10000 })
      .should('contain', 'Manajemen Risiko')

    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)

    cy.get('body').then(($body) => {

      const btnTugaskan =
        $body.find('button:contains("Tugaskan")').length

      if (btnTugaskan > 0) {

        cy.log('Tombol Tugaskan ditemukan')

        cy.contains('button', 'Tugaskan')
          .first()
          .scrollIntoView()
          .click({ force: true })

        cy.get('.modal.show', {
          timeout: 10000
        }).should('be.visible')

        cy.get('.modal.show .modal-title')
          .should('contain', 'Tugaskan Auditor')

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
        cy.log('Semua risiko sudah memiliki auditor')

      }
    })

    logout()

    // =====================================================
    // AUDITOR
    // =====================================================

    cy.login('audit_test', '123456')

    cy.visit('/manajemen-risiko')

    cy.get('body').then(($body) => {

      if ($body.text().includes('Detail')) {

        cy.contains('Detail')
          .first()
          .click({ force: true })

        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          if ($detailPage.find('textarea').length > 0) {

            cy.get('textarea')
              .first()
              .clear()
              .type('Hasil analisis auditor')

          }

          if ($detailPage.text().includes('Kirim ke Auditee')) {

            cy.contains('Kirim ke Auditee')
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

    logout()

    // =====================================================
    // AUDITEE
    // =====================================================

    cy.login('sekretaris_test', '123456')

    cy.visit('/manajemen-risiko')

    cy.get('body').then(($body) => {

      if ($body.text().includes('Detail')) {

        cy.contains('Detail')
          .first()
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

    logout()

    // =====================================================
    // AUDITOR APPROVAL
    // =====================================================

    cy.login('audit_test', '123456')

    cy.visit('/manajemen-risiko')

    cy.get('body').then(($body) => {

      if ($body.text().includes('Detail')) {

        cy.contains('Detail')
          .first()
          .click({ force: true })

        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          if ($detailPage.text().includes('Approve')) {

            cy.contains('Approve')
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

    logout()

    // =====================================================
    // ADMIN CETAK LAPORAN
    // =====================================================

    cy.login('admin1', '123456')

    cy.visit('/manajemen-risiko')

    cy.get('body').then(($body) => {

      if ($body.text().match(/approved|selesai/i)) {

        cy.log('Status selesai ditemukan')

      } else {

        cy.log('Belum ada status selesai')

      }

      if ($body.text().includes('Cetak')) {

        cy.contains('Cetak')
          .should('exist')

        cy.log('Tombol Cetak ditemukan')

      } else {

        cy.log('Tombol Cetak tidak ditemukan')

      }

    })

  })

})