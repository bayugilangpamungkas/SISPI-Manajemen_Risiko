describe('Login Auditor', () => {
    it('Berhasil login sebagai Auditor', () => {
        cy.login('198609232015041001', '123456')
        cy.url().should('include', '/dashboard')
    })
})