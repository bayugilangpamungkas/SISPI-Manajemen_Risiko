const { defineConfig } = require("cypress");

module.exports = defineConfig({
  projectId: 'h7o29s',
  allowCypressEnv: false,

  e2e: {

    baseUrl: 'http://127.0.0.1:8000',
    setupNodeEvents(on, config) {
      // implement node event listeners here

    },
  },
});
