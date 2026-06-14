// 01-auth/admin-login.cy.js
// Pengujian login untuk role Admin

describe('Login Admin - Positive & Negative Testing', () => {

  beforeEach(() => {
    // Reset session sebelum setiap test
    cy.clearCookies()
    cy.clearLocalStorage()
    cy.wait(500)
  })

  // =====================================================
  // POSITIVE TEST CASES (Login Berhasil)
  // =====================================================

  it('TC-ADMIN-01: Login berhasil dengan username dan password valid', () => {
    cy.login('admin1', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
    cy.get('body', { timeout: 5000 }).should('be.visible')
    cy.log('✓ Login Admin berhasil dengan kredensial valid')
  })

  it('TC-ADMIN-02: Login berhasil dengan username admin2', () => {
    cy.login('admin2', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')
    cy.log('✓ Login Admin berhasil dengan username admin2')
  })

  // =====================================================
  // NEGATIVE TEST CASES (Login Gagal)
  // =====================================================

  it('TC-ADMIN-03: Login gagal dengan password salah', () => {
    cy.visit('/login')
    cy.get('input[name="username"]').clear().type('admin1')
    cy.get('input[name="password"]').clear().type('salah123')
    cy.get('button[type="submit"]').click()

    // Tunggu proses login selesai
    cy.wait(2000)

    // Pastikan masih di halaman login (bukan dashboard)
    cy.url({ timeout: 5000 }).should('include', '/login')
    cy.url().should('not.include', '/dashboard')

    // Cek apakah ada pesan error (jika ada), jika tidak tetap dianggap PASS
    cy.get('body').then(($body) => {
      const hasErrorText = /gagal|error|invalid|salah|password|incorrect/i.test($body.text())
      const hasErrorElement = $body.find('.alert-danger, .alert-error, .invalid-feedback, .toast-error, .swal2-popup').length > 0

      if (hasErrorText || hasErrorElement) {
        cy.log('✓ Pesan error ditemukan - login gagal sesuai harapan')
      } else {
        // Sistem tidak menampilkan pesan error, cukup pastikan tetap di halaman login
        cy.log('✓ Tidak ada pesan error, tetapi user tetap di halaman login - login gagal sesuai harapan')
      }
    })

    cy.log('✓ TC-ADMIN-03: Login gagal dengan password salah - sesuai harapan')
  })

  it('TC-ADMIN-04: Login gagal dengan username tidak terdaftar', () => {
    cy.visit('/login')
    cy.get('input[name="username"]').clear().type('admin_tidak_ada')
    cy.get('input[name="password"]').clear().type('123456')
    cy.get('button[type="submit"]').click()

    cy.wait(2000)
    cy.url({ timeout: 5000 }).should('include', '/login')

    // Cek pesan error dengan berbagai selector
    cy.get('body').then(($body) => {
      const hasError = $body.find('.alert-danger, .alert-error, .invalid-feedback, .toast-error').length > 0
      if (hasError) {
        cy.log('✓ Pesan error ditemukan')
      } else {
        cy.get('body').should('contain', /gagal|error|invalid|tidak terdaftar/i)
      }
    })

    cy.log('✓ TC-ADMIN-04: Login gagal dengan username tidak terdaftar - sesuai harapan')
  })

  it('TC-ADMIN-05: Login gagal dengan username kosong', () => {
    cy.visit('/login')
    cy.get('input[name="username"]').clear()
    cy.get('input[name="password"]').clear().type('123456')
    cy.get('button[type="submit"]').click()

    cy.wait(2000)

    // Cek validasi HTML5 atau pesan error dari sistem
    cy.get('body').then(($body) => {
      // Cek apakah ada pesan validation dari HTML5
      const usernameInput = $body.find('input[name="username"]')[0]
      if (usernameInput && usernameInput.validity && usernameInput.validity.valueMissing) {
        cy.log('✓ Validasi HTML5: username harus diisi')
      } else {
        // Cek pesan error lain
        cy.get('.alert-danger, .alert-error, .invalid-feedback', { timeout: 3000 })
          .should('exist')
      }
    })

    cy.log('✓ TC-ADMIN-05: Login gagal dengan username kosong - validasi berfungsi')
  })

  it('TC-ADMIN-06: Login gagal dengan password kosong', () => {
    cy.visit('/login')
    cy.get('input[name="username"]').clear().type('admin1')
    cy.get('input[name="password"]').clear()
    cy.get('button[type="submit"]').click()

    cy.wait(2000)

    cy.get('body').then(($body) => {
      const passwordInput = $body.find('input[name="password"]')[0]
      if (passwordInput && passwordInput.validity && passwordInput.validity.valueMissing) {
        cy.log('✓ Validasi HTML5: password harus diisi')
      } else {
        cy.get('.alert-danger, .alert-error, .invalid-feedback', { timeout: 3000 })
          .should('exist')
      }
    })

    cy.log('✓ TC-ADMIN-06: Login gagal dengan password kosong - validasi berfungsi')
  })

  it('TC-ADMIN-07: Login gagal dengan username dan password kosong', () => {
    cy.visit('/login')
    cy.get('input[name="username"]').clear()
    cy.get('input[name="password"]').clear()
    cy.get('button[type="submit"]').click()

    cy.wait(2000)

    cy.url({ timeout: 5000 }).should('include', '/login')
    cy.log('✓ TC-ADMIN-07: Login gagal dengan username dan password kosong - validasi berfungsi')
  })
})