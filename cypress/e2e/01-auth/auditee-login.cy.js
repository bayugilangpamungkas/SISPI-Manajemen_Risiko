// 01-auth/auditee-login.cy.js
// Pengujian login untuk role Auditee

describe('Login Auditee - Positive & Negative Testing', () => {

    beforeEach(() => {
        cy.clearCookies()
        cy.clearLocalStorage()
        cy.wait(500)
    })

    // =====================================================
    // POSITIVE TEST CASES (Login Berhasil)
    // =====================================================

    it('TC-AUDITEE-01: Login berhasil dengan username dan password valid', () => {
        cy.login('Auditee2', '123456')
        cy.url({ timeout: 10000 }).should('include', '/dashboard')
        cy.log('✓ Login Auditee berhasil dengan kredensial valid')
    })

    it('TC-AUDITEE-02: Login berhasil dengan username auditee alternatif', () => {
        cy.login('audit_test', '123456')
        cy.url({ timeout: 10000 }).should('include', '/dashboard')
        cy.log('✓ Login Auditee berhasil dengan username auditee1')
    })

    // =====================================================
    // NEGATIVE TEST CASES (Login Gagal)
    // =====================================================

    it('TC-AUDITEE-03: Login gagal dengan password salah', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('Auditee2')
        cy.get('input[name="password"]').clear().type('salah123')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITEE-03: Login gagal dengan password salah - sesuai harapan')
    })

    it('TC-AUDITEE-04: Login gagal dengan username tidak terdaftar', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('auditee_tidak_ada')
        cy.get('input[name="password"]').clear().type('123456')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITEE-04: Login gagal dengan username tidak terdaftar - sesuai harapan')
    })

    it('TC-AUDITEE-05: Login gagal dengan username kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear()
        cy.get('input[name="password"]').clear().type('123456')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url().should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITEE-05: Login gagal dengan username kosong - validasi berfungsi')
    })

    it('TC-AUDITEE-06: Login gagal dengan password kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('Auditee2')
        cy.get('input[name="password"]').clear()
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url().should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITEE-06: Login gagal dengan password kosong - validasi berfungsi')
    })

    it('TC-AUDITEE-07: Login gagal dengan username dan password kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear()
        cy.get('input[name="password"]').clear()
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITEE-07: Login gagal dengan username dan password kosong - validasi berfungsi')
    })
})