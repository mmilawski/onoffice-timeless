require('dotenv').config();

const styles = [
  // PARENT THEME
  {
    expand: true,
    cwd: 'src/sass/',
    src: '*.scss',
    ext: '.css',
    dest: 'build/css/',
  },

  // SHARED CSS
  {
    expand: true,
    cwd: 'shared/src/sass/',
    src: ['*.scss', '**/*.scss', '!vars.scss', '*.css', '**/*.css'],
    dest: 'shared/build/css/',
    ext: '.css',
  },

  {
    expand: true,
    cwd: 'shared/src/css/',
    src: ['*.css', '**/*.css'],
    dest: 'shared/build/css/',
    ext: '.css',
  },

  // BLOCKS CSS
  {
    expand: true,
    cwd: 'shared/blocks',
    src: '**/src/*.scss',
    dest: 'shared/blocks/',
    ext: '.css',
    rename: function (dest, src) {
      let folder = src.substring(0, src.indexOf('/'));
      let file = src.substring(src.lastIndexOf('/'));

      return dest + folder + '/build' + file;
    },
  },

  // PARENT BLOCKS CSS
  {
    expand: true,
    cwd: 'blocks',
    src: '**/src/*.scss',
    dest: 'blocks/',
    ext: '.css',
    rename: function (dest, src) {
      let folder = src.substring(0, src.indexOf('/'));
      let file = src.substring(src.lastIndexOf('/'));

      return dest + folder + '/build' + file;
    },
  },

  // CHILD BLOCKS CSS
  {
    expand: true,
    cwd: 'child-blocks',
    src: '**/src/*.scss',
    dest: 'blocks/',
    ext: '.css',
    rename: function (dest, src) {
      let folder = src.substring(0, src.indexOf('/'));
      let file = src.substring(src.lastIndexOf('/'));

      return dest + folder + '/build' + file;
    },
  },
];

const releaseFiles = [
  // CHILD OVERRIDE
  'child-images/**',
  'child-modules/**',
  'child-components/**',
  'child-blocks/**/*.php',
  'child-blocks/**/*.json',
  'child-blocks/**/build/**/*.css',
  'child-blocks/**/build/**/*.js',

  // PARENT OVERRIDE
  'images/**',
  'modules/**',
  'templates/**',
  'components/**',
  'blocks/**/*.php',
  'blocks/**/*.json',
  'blocks/**/build/**/*.css',
  'blocks/**/build/**/*.js',
  'acfe-php/**',

  // MAIN
  'build/**',
  'onoffice-theme/**',
  'shared/*.php',
  'shared/acfe-php/**',
  'shared/blocks/**/*.php',
  'shared/blocks/**/*.json',
  'shared/blocks/**/build/**/*.css',
  'shared/blocks/**/build/**/*.js',
  'shared/build/**',
  'shared/components/**',
  'shared/fonts/**',
  'shared/images/**',
  'shared/includes/**',
  'shared/modules/**',
  'shared/templates/**',
  'shared/languages/**',
  'shared/vendor/**',
  'template-parts/**',
  '*.css',
  '*.png',
  '*.php',
];

module.exports = function (grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    'dart-sass': {
      target: {
        options: {
          outputStyle: 'compressed',
          'no-source-map': '',
        },
        files: styles,
      },
    },
    uglify: {
      dist: {
        files: [
          // PARENT THEME JS
          {
            expand: true,
            cwd: 'src/js/',
            src: '*.js',
            ext: '.js',
            dest: 'build/js/',
          },

          // SHARED JS
          {
            expand: true,
            cwd: 'shared/src/js/',
            src: [
              '*.js',
              '**/*.js',
              '!*.min.js',
              '!**/*.min.js',
              '!*.full.js',
              '!**/*.full.js',
            ],
            dest: 'shared/build/js/',
            ext: '.js',
          },

          // SHARED JS MIN
          {
            expand: true,
            cwd: 'shared/src/js/',
            src: ['*.min.js', '**/*.min.js'],
            dest: 'shared/build/js/',
            ext: '.min.js',
          },

          // SHARED JS FULL
          {
            expand: true,
            cwd: 'shared/src/js/',
            src: ['*.full.js', '**/*.full.js'],
            dest: 'shared/build/js/',
            ext: '.full.js',
          },
        ],
      },
    },
    watch: {
      sass: {
        files: [
          '*.scss',
          'src/sass/*.scss',
          'src/sass/**/*.scss',
          'src/sass/**/**/*.scss',
          'shared/src/sass/*.scss',
          'shared/src/sass/**/*.scss',
          'shared/src/sass/**/**/*.scss',
          'shared/src/sass/*.css',
          'shared/src/sass/**/*.css',
          'shared/src/sass/**/**/*.css',
          'shared/src/css/*.css',
          'shared/src/css/**/*.css',
          'shared/src/css/**/**/*.css',
          'shared/blocks/**/src/*.scss',
          'shared/blocks/**/src/**/*.scss',
          'blocks/**/src/**/*.scss',
          'blocks/**/*.scss',
          'blocks/**/src/sass/*.scss',
          'child-blocks/**/src/sass/*.scss',
        ],
        tasks: ['dart-sass'],
      },
      js: {
        files: [
          'blocks/**/src/js/*.js',
          'child-blocks/**/src/js/*.js',
          'src/js/*.js',
          'src/js/**/*.js',
          'src/js/**/**/*.js',
          'shared/src/js/*.js',
          'shared/src/js/**/*.js',
          'shared/src/js/**/*',
          'shared/src/js/**/**/*.js',
        ],
        tasks: ['uglify'],
      },
    },

    // Autoprefixer
    postcss: {
      options: {
        processors: [require('autoprefixer')],
      },
      dist: {
        src: 'build/css/style.css',
        dest: 'build/css/style.css',
      },
    },

    // BrowserSync
    browserSync: {
      dev: {
        bsFiles: {
          // Add your CSS Files here
          src: [
            'build/css/*.css',
            'build/js/*.js',
            'shared/build/css/**/*.css',
            'shared/build/js/**/*.js',
            'blocks/**/src/sass/*.scss',
            'blocks/**/src/js/*.js',
            'child-blocks/**/src/sass/*.scss',
            'child-blocks/**/src/js/*.js',
            '*.php',
            '*.css',
          ],
        },
        options: {
          watchTask: true,
          https: true,
          proxy: {
            target: process.env.LOCAL_DEV_URI,
            proxyOptions: {
              secure: false
            }
          }
        },
      },
    },

    // ZIP RELEASE
    compress: {
      main: {
        options: {
          archive: '<%= pkg.name %>.zip',
        },
        files: [
          // FILES
          {
            src: releaseFiles,
            dest: '<%= pkg.name %>',
          },
        ],
      },
    },

    // CLEANUP DEPLOYMENT
    clean: {
      build: [
        '.git',
        '.github',
        'src',
        'node_modules',
        'README.md',
        '.gitignore',
        '.gitmodules',
        '.prettierignore',
        '.prettierrc.js',
        'blocks/**/src',
        'package.json',
        'package-lock.json',
        'Gruntfile.js',
        'shared/.git',
        'shared/.github',
        'shared/.gitignore',
        'shared/.prettierignore',
        'shared/.prettierrc.js',
        'shared/package.json',
        'shared/package-lock.json',
        'shared/build/css/vars.css',
        'shared/src',
        'shared/node_modules',
        'shared/blocks/**/src',
      ],
    },
  });

  // Load tasks
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-dart-sass');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-browser-sync');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-clean');

  // Register tasks
  grunt.registerTask('default', ['dart-sass', 'postcss', 'uglify']);
  grunt.registerTask('dev', ['browserSync', 'watch']);
  grunt.registerTask('release', ['sass', 'postcss', 'uglify']);
};
