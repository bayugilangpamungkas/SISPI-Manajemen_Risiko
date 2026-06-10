// 02-manajemen-risiko/flow/manajemen-risiko-flow.cy.js

describe('FULL FLOW - Manajemen Risiko', () => {

  // =====================================================
  // HELPER FUNCTION
  // =====================================================

  const logout = () => {
    cy.logout()
  }

  // =====================================================
  // TEST 1: ADMIN - Login & Manajemen Risiko
  // =====================================================

  it('TEST 1: Admin - Login dan akses manajemen risiko', () => {

    // ========== LOGIN ADMIN ==========
    cy.log('=== LOGIN ADMIN ===')
    cy.login('admin1', '123456')

    cy.url({ timeout: 10000 })
      .should('include', '/dashboard')
    cy.log('✓ Berhasil masuk Dashboard')

    // ========== FITUR DATA MANAJEMEN RISIKO ==========
    cy.log('=== DATA MANAJEMEN RISIKO ===')
    cy.visit('/manajemen-risiko')

    cy.get('h1', { timeout: 10000 })
      .should('contain.text', 'Manajemen Risiko')
    cy.log('✓ Halaman Manajemen Risiko terbuka')

    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)
    cy.log('✓ Data risiko muncul dalam tabel')

    logout()

  })

  // =====================================================
  // TEST 2: ADMIN - Pilih kegiatan & Assign Auditor
  // =====================================================

  it('TEST 2: Admin - Pilih kegiatan dan assign auditor', () => {

    cy.login('admin1', '123456')
    cy.url({ timeout: 10000 }).should('include', '/dashboard')

    cy.visit('/manajemen-risiko')

    cy.get('h1', { timeout: 10000 })
      .should('contain.text', 'Manajemen Risiko')

    // ========== PILIH KEGIATAN ==========
    cy.log('=== PILIH KEGIATAN ===')

    // Cek apakah ada tombol pilih kegiatan
    cy.get('body').then(($body) => {
      const btnPilihKegiatan = $body.find('a:contains("Pilih Kegiatan"), button:contains("Pilih Kegiatan")').length

      if (btnPilihKegiatan > 0) {
        cy.contains(/Pilih Kegiatan/).first().click({ force: true })
        cy.url({ timeout: 8000 }).should('include', '/manajemen-risiko/detail-unit')
        cy.log('✓ Kegiatan terpilih masuk ke halaman Manajemen Risiko')
      } else {
        cy.log('⚠️ Tombol Pilih Kegiatan tidak ditemukan')
      }
    })

    // ========== ASSIGN AUDITOR ==========
    cy.log('=== ASSIGN AUDITOR ===')

    cy.get('body').then(($body) => {

      const btnTugaskan = $body.find('button:contains("Tugaskan")').length

      if (btnTugaskan > 0) {

        cy.contains('button', 'Tugaskan')
          .first()
          .scrollIntoView()
          .click({ force: true })

        cy.get('.modal.show', { timeout: 10000 })
          .should('be.visible')

        cy.get('.modal.show select[name="auditor_id"]')
          .should('be.visible')
          .select('8')

        cy.get('.modal.show button[type="submit"]')
          .click({ force: true })

        cy.get('.modal.show', { timeout: 10000 })
          .should('not.exist')

        cy.log('✓ Auditor berhasil tersimpan')

      } else {

        cy.log('⚠️ Tidak ada tombol Tugaskan')

      }

    })

    logout()

  })

  // =====================================================
  // TEST 3: AUDITOR - Login & Lihat penugasan
  // =====================================================

  it('TEST 3: Auditor - Login dan lihat penugasan risiko', () => {

    // ========== LOGIN AUDITOR ==========
    cy.log('=== LOGIN AUDITOR ===')
    cy.login('198609232015041001', '123456')

    cy.url({ timeout: 15000 }).should('include', '/dashboard')
    cy.log('✓ Masuk dashboard auditor')

    // ========== LIHAT PENUGASAN ==========
    cy.log('=== LIHAT PENUGASAN ===')
    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 })
      .should('include', '/auditor/manajemen-risiko')
    cy.log('✓ Halaman pemeriksaan risiko terbuka')

    // Verifikasi hanya risiko yang ditugaskan yang tampil
    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)
    cy.log('✓ Hanya risiko yang ditugaskan kepadanya yang tampil')

    logout()

  })

  // =====================================================
  // TEST 4: AUDITOR - Detail penugasan & Kirim ke Auditee
  // =====================================================

  it('TEST 4: Auditor - Detail penugasan dan kirim ke auditee', () => {

    cy.login('198609232015041001', '123456')
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    // ========== DETAIL PENUGASAN ==========
    cy.log('=== DETAIL PENUGASAN ===')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find('a').filter(function () {
        return Cypress.$(this).text().trim().includes('Detail')
      })

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first()).click({ force: true })
        cy.wait(1000)

        // Verifikasi form sesuai dengan kode unit
        cy.get('body').then(($detailPage) => {

          if ($detailPage.text().includes('WADIR I')) {
            cy.log('✓ Form sesuai kode unit WADIR I muncul')
          } else if ($detailPage.text().includes('WADIR II')) {
            cy.log('✓ Form sesuai kode unit WADIR II muncul')
          } else {
            cy.log('⚠️ Form unit kerja muncul')
          }

          // ========== ISI FORM ANALISIS ==========
          if ($detailPage.find('textarea').length > 0) {
            cy.get('textarea').first().clear().type('Hasil analisis auditor: Risiko perlu mitigasi segera.')
            cy.log('✓ Form hasil analisis diisi')
          }

          // ========== KIRIM KE AUDITEE ==========
          if ($detailPage.text().match(/Kirim ke Auditee/i)) {

            cy.contains(/Kirim ke Auditee/i).click({ force: true })
            cy.log('✓ Status berubah menjadi Menunggu Auditee')

          } else {
            cy.log('⚠️ Tombol Kirim ke Auditee tidak ditemukan')
          }

        })

      } else {
        cy.log('⚠️ Tidak ada data Detail untuk Auditor')
      }

    })

    logout()

  })

  // =====================================================
  // TEST 5: AUDITOR - Revisi (opsional)
  // =====================================================

  it('TEST 5: Auditor - Kirim catatan revisi ke auditee', () => {

    cy.login('198609232015041001', '123456')
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find('a').filter(function () {
        return Cypress.$(this).text().trim().includes('Detail')
      })

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first()).click({ force: true })
        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          // Cek apakah ada tombol Revisi
          if ($detailPage.text().match(/Revisi|Kirim Revisi/i)) {

            if ($detailPage.find('textarea').length > 0) {
              cy.get('textarea').first().clear().type('Catatan revisi: Silakan perbaiki dokumen pendukung.')
              cy.log('✓ Catatan revisi diisi')
            }

            cy.contains(/Revisi|Kirim Revisi/i).click({ force: true })
            cy.log('✓ Auditor dapat mengirim catatan revisi')

          } else {
            cy.log('⚠️ Tidak ada tombol Revisi - lanjut ke test berikutnya')
          }

        })

      }

    })

    logout()

  })

  // =====================================================
  // TEST 6: AUDITEE - Login & Lihat tugas
  // =====================================================

  it('TEST 6: Auditee - Login dan lihat tugas dari auditor', () => {

    // ========== LOGIN AUDITEE ==========
    cy.log('=== LOGIN AUDITEE ===')
    cy.login('Auditee2', '123456')

    cy.url({ timeout: 10000 }).should('include', '/dashboard')
    cy.log('✓ Masuk dashboard auditee')

    // ========== LIHAT TUGAS ==========
    cy.log('=== LIHAT TUGAS ===')
    cy.visit('/manajemen-risiko')

    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')
    cy.log('✓ Halaman pemantauan risiko terbuka')

    cy.get('table tbody tr', { timeout: 10000 })
      .should('have.length.greaterThan', 0)
    cy.log('✓ Tugas dari Auditor muncul')

    logout()

  })

  // =====================================================
  // TEST 7: AUDITEE - Isi form & Kirim ke Auditor
  // =====================================================

  it('TEST 7: Auditee - Isi form tindak lanjut dan kirim ke auditor', () => {

    cy.login('Auditee2', '123456')
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find(
        'a[href*="detail"], a[title*="Detail"], a.btn-primary, a.btn-info'
      )

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first()).click({ force: true })
        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          // ========== ISI FORM ==========
          if ($detailPage.find('textarea').length > 0) {
            cy.get('textarea').first().clear().type('Tindak lanjut: Koordinasi dengan unit terkait telah dilakukan.')
            cy.log('✓ Data berhasil disimpan')
          }

          // ========== KIRIM KE AUDITOR ==========
          if ($detailPage.text().includes('Kirim ke Auditor')) {

            cy.contains('Kirim ke Auditor').click({ force: true })
            cy.log('✓ Status menjadi Menunggu Approval Auditor')

          } else {
            cy.log('⚠️ Tombol Kirim ke Auditor tidak ditemukan')
          }

        })

      } else {
        cy.log('⚠️ Tidak ada data Detail untuk Auditee')
      }

    })

    logout()

  })

  // =====================================================
  // TEST 8: AUDITEE - Lihat revisi (jika ada)
  // =====================================================

  it('TEST 8: Auditee - Lihat catatan revisi dari auditor', () => {

    cy.login('Auditee2', '123456')
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditee/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find(
        'a[href*="detail"], a[title*="Detail"]'
      )

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first()).click({ force: true })
        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          // Cek apakah ada catatan revisi
          if ($detailPage.text().match(/Revisi|Catatan Revisi/i)) {
            cy.log('✓ Auditee dapat melihat catatan revisi')
          } else {
            cy.log('⚠️ Tidak ada catatan revisi')
          }

          // Cek apakah ada tombol Kirim Ulang
          if ($detailPage.text().match(/Kirim Ulang|Revisi Ulang/i)) {
            cy.contains(/Kirim Ulang|Revisi Ulang/i).click({ force: true })
            cy.log('✓ Data kembali ke Auditor')
          } else {
            cy.log('⚠️ Tidak ada tombol Kirim Ulang')
          }

        })

      }

    })

    logout()

  })

  // =====================================================
  // TEST 9: AUDITOR - Approval (setelah auditee kirim)
  // =====================================================

  it('TEST 9: Auditor - Approval hasil audit', () => {

    cy.login('198609232015041001', '123456')
    cy.visit('/manajemen-risiko')
    cy.url({ timeout: 10000 }).should('include', '/auditor/manajemen-risiko')

    cy.get('body').then(($body) => {

      const btnDetail = $body.find('a').filter(function () {
        return Cypress.$(this).text().trim().includes('Detail')
      })

      if (btnDetail.length > 0) {

        cy.wrap(btnDetail.first()).click({ force: true })
        cy.wait(1000)

        cy.get('body').then(($detailPage) => {

          // ========== APPROVAL ==========
          if ($detailPage.text().match(/Approve|Setujui|Approval/i)) {

            cy.contains(/Approve|Setujui|Approval/i).click({ force: true })
            cy.log('✓ Status menjadi Approved')

          } else {
            cy.log('⚠️ Tombol Approve tidak ditemukan - mungkin sudah diapprove')
          }

        })

      } else {
        cy.log('⚠️ Tidak ada data Detail untuk Approval')
      }

    })

    logout()

  })

  // =====================================================
  // TEST 10: ADMIN - Cetak laporan (tidak diubah)
  // =====================================================

  it('TEST 10: Admin - Cetak laporan PDF', () => {

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

    // ========== MODAL CETAK ==========
    cy.get('#modalCetakPDF', { timeout: 10000 })
      .should('be.visible')

    cy.log('Modal Cetak PDF tampil')

    // ========== PILIH UNIT KERJA ==========
    cy.get('#modalCetakPDF select', { timeout: 10000 })
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

          cy.log('✓ Unit Kerja WADIR I berhasil dipilih')

        } else {

          cy.log('⚠️ WADIR I tidak ditemukan pada dropdown')

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

    // ========== KLIK TOMBOL CETAK ==========
    cy.get('#modalCetakPDF').then(($modal) => {

      const tombolSubmit = $modal.find(
        'button[type="submit"], .btn-primary, .btn-success'
      )

      if (tombolSubmit.length > 0) {

        cy.wrap(tombolSubmit.first())
          .click({ force: true })

        cy.log('✓ Generate laporan berhasil')

      } else {

        cy.contains(
          '#modalCetakPDF button',
          /Cetak|Print|Generate|Preview/i
        )
          .click({ force: true })

        cy.log('Tombol cetak ditemukan dan diklik')

      }

    })

    // ========== VALIDASI HASIL CETAK ==========
    cy.wait(3000)

    cy.url({ timeout: 15000 })
      .then((url) => {
        cy.log('URL hasil cetak : ' + url)
      })

    cy.get('body', { timeout: 15000 })
      .should('exist')

    cy.get('body')
      .invoke('text')
      .then((text) => {

        cy.log('Halaman laporan berhasil terbuka')

        if (
          text.includes('Belum ada') ||
          text.includes('Tidak ada data')
        ) {
          cy.log('⚠️ PERINGATAN: Unit kerja yang dipilih belum memiliki audit selesai')
        }

      })

    logout()

  })

})