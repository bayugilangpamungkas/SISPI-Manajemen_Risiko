describe('Login Auditee', () => {
    it('Berhasil login sebagai Auditee', () => {
        cy.login('Auditee2', '123456')
        cy.url().should('include', '/dashboard')
    })
})