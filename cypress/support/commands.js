// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })



Cypress.Commands.add('login', (username, password) => {

  cy.clearCookies()
  cy.clearLocalStorage()

  cy.visit('/login')

  cy.url({ timeout: 10000 })
    .should('include', '/login')

  cy.get('input[name="username"]', { timeout: 10000 })
    .should('be.visible')
    .clear()
    .type(username)

  cy.get('input[name="password"]')
    .should('be.visible')
    .clear()
    .type(password)

  cy.get('button[type="submit"]')
    .should('be.visible')
    .click()

  cy.url({ timeout: 15000 })
    .should('include', '/dashboard')

})

Cypress.Commands.add('logout', () => {

  cy.visit('/logout', {
    failOnStatusCode: false
  })

  cy.clearCookies()
  cy.clearLocalStorage()

  cy.visit('/login')

  cy.url({ timeout: 10000 })
    .should('include', '/login')

})