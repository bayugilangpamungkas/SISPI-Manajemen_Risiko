describe('Login Admin', () => {
  it('Berhasil login sebagai Admin', () => {
    cy.login('admin1', '123456')
    cy.url().should('include', '/dashboard')
  })
})