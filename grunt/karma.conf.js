module.exports = function(config) {
  config.set({
    // Base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',

    // Frameworks to use
    frameworks: ['qunit'],

    // List of files / patterns to load in the browser
    files: [
      'node_modules/jquery/dist/jquery.min.js',
      'src/**/*.js',
      'test/**/*_test.js',
      { pattern: 'test/**/*.html', watched: true, included: false, served: true }
    ],

    // List of files to exclude
    exclude: [],

    // Preprocessors that apply transformations before serving to browsers
    preprocessors: {
      'src/**/*.js': ['coverage']
    },

    // Test results reporter to use
    reporters: ['progress', 'coverage'],

    coverageReporter: {
      type: 'html',
      dir: 'coverage/'
    },

    // Web server port
    port: 9876,

    // Enable / disable colors in the output (reporters and logs)
    colors: true,

    // Level of logging
    logLevel: config.LOG_INFO,

    // Enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // Start these browsers
    browsers: ['ChromeHeadless'],

    // Browser launchers
    customLaunchers: {
      ChromeHeadless: {
        base: 'Chrome',
        flags: [
          '--headless',
          '--disable-gpu',
          '--no-sandbox',
          '--remote-debugging-port=9222'
        ]
      }
    },

    // Continuous Integration mode
    singleRun: false,

    // Concurrency level
    concurrency: Infinity
  });
};