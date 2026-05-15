describe('FULL FLOW - Manajemen Risiko', () => {

    it('Admin → Auditor → Auditee → Approve', () => {
  
      // ADMIN
      cy.login('admin1', '123456')
      cy.visit('/manajemen-risiko')
  
      cy.contains('Tugaskan').first().click()
      cy.get('select[name="auditor_id"]').select(1)
      cy.contains('Simpan').click()
      cy.logout()
  
      // AUDITOR
      cy.login('audit_test', '123456')
      cy.visit('/manajemen-risiko')
  
      cy.contains('Detail').first().click()
      cy.get('textarea').first().type('Analisa auditor')
      cy.contains('Kirim ke Auditee').click()
      cy.logout()
  
      // AUDITEE
      cy.login('sekretaris_test', '123456')
      cy.visit('/manajemen-risiko')
  
      cy.contains('Detail').first().click()
      cy.get('textarea').first().clear().type('Revisi auditee')
      cy.contains('Kirim ke Auditor').click()
      cy.logout()
  
      // AUDITOR APPROVE
      cy.login('audit_test', '123456')
      cy.visit('/manajemen-risiko')
  
      cy.contains('Detail').first().click()
      cy.contains('Approve').click()
      cy.contains('Cetak Laporan').should('exist')
  
    })
  
  })