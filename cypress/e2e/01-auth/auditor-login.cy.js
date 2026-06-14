// 01-auth/auditor-login.cy.js
// Pengujian login untuk role Auditor

describe('Login Auditor - Positive & Negative Testing', () => {

    beforeEach(() => {
        cy.clearCookies()
        cy.clearLocalStorage()
        cy.wait(500)
    })

    // =====================================================
    // POSITIVE TEST CASES (Login Berhasil)
    // =====================================================

    it('TC-AUDITOR-01: Login berhasil dengan username dan password valid', () => {
        cy.login('198609232015041001', '123456')
        cy.url({ timeout: 10000 }).should('include', '/dashboard')
        cy.log('✓ Login Auditor berhasil dengan kredensial valid')
    })

    it('TC-AUDITOR-02: Login berhasil dengan username Dr. Drs. Sumiadji, Ak., M.SA.', () => {
        cy.login('196405121993031000', '123456')
        cy.url({ timeout: 10000 }).should('include', '/dashboard')
        cy.log('✓ Login Auditor berhasil dengan username audit_test')
    })

    // =====================================================
    // NEGATIVE TEST CASES (Login Gagal)
    // =====================================================

    it('TC-AUDITOR-03: Login gagal dengan password salah', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('198609232015041001')
        cy.get('input[name="password"]').clear().type('salah123')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITOR-03: Login gagal dengan password salah - sesuai harapan')
    })

    it('TC-AUDITOR-04: Login gagal dengan username tidak terdaftar', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('auditor_tidak_ada')
        cy.get('input[name="password"]').clear().type('123456')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITOR-04: Login gagal dengan username tidak terdaftar - sesuai harapan')
    })

    it('TC-AUDITOR-05: Login gagal dengan username kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear()
        cy.get('input[name="password"]').clear().type('123456')
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url().should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITOR-05: Login gagal dengan username kosong - validasi berfungsi')
    })

    it('TC-AUDITOR-06: Login gagal dengan password kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear().type('198609232015041001')
        cy.get('input[name="password"]').clear()
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url().should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITOR-06: Login gagal dengan password kosong - validasi berfungsi')
    })

    it('TC-AUDITOR-07: Login gagal dengan username dan password kosong', () => {
        cy.visit('/login')
        cy.get('input[name="username"]').clear()
        cy.get('input[name="password"]').clear()
        cy.get('button[type="submit"]').click()

        cy.wait(2000)
        cy.url({ timeout: 5000 }).should('include', '/login')
        cy.url().should('not.include', '/dashboard')

        cy.log('✓ TC-AUDITOR-07: Login gagal dengan username dan password kosong - validasi berfungsi')
    })
})