module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    dirs: {
      src: 'test/skins/default',
      dest: 'test/skins/default',
    },
    concat: {
      options: {
        //separator: ';'
      },
      basic_and_extras: {
        files: {
          'module/editor/editor.min.css' : ['module/editor/editor.css'],
          'module/editor/editor.min.js' : ['module/editor/editor.js'],
          'module/board/tpl/board.min.css' : ['module/board/tpl/board.css'],
          'module/board/tpl/board.min.js' : ['module/board/tpl/board.js'],
          'module/page/tpl/page.min.css' : ['module/page/tpl/page.css'],
          'module/page/tpl/page.min.js' : ['module/page/tpl/page.js'],
          'theme/default/index.min.js' : ['theme/default/index.js'],
          'theme/default/index.min.css' : ['theme/default/index.css'],
          'theme/default/login.min.css' : ['theme/default/login.css'],
          'common/js/common.min.js' : ['common/js/common.js'],
          'common/css/common.min.css' : ['common/css/common.css']
        }
      }

      // dist: {
      //   src: [
      //     '<%= dirs.src %>/js/board.*.js',
      //     '<%= dirs.src %>/js/modal.*.js'
      //   ],
      //   dest: '<%= dirs.dest %>/js/common.min.js'
      // }
    },
    uglify: {
      minify: {
        files: [{
          expand: true,
          cwd: 'module/editor/',
          src: ['editor.min.js'],
          dest: 'module/editor/',
          ext: '.min.js'
        }, {
          expand: true,
          cwd: 'module/board/tpl/',
          src: ['board.min.js'],
          dest: 'module/board/tpl/',
          ext: '.min.js'
        }, {
          expand: true,
          cwd: 'module/page/tpl/',
          src: ['page.min.js'],
          dest: 'module/page/tpl/',
          ext: '.min.js'
        }, {
          expand: true,
          cwd: 'theme/default/',
          src: ['index.min.js'],
          dest: 'theme/default/',
          ext: '.min.js'
        }, {
          expand: true,
          cwd: 'common/js/',
          src: ['common.min.js'],
          dest: 'common/js/',
          ext: '.min.js'
        }]
      }

      // dist: {
      //     src: '<%= concat.dist.dest %>',
      //     dest: static_path + 'js/common.min.js'
      //   }
    },
    cssmin: {
      minify: {
        files: [{
          expand: true,
          cwd: 'module/editor/',
          src: ['editor.min.css'],
          dest: 'module/editor/',
          ext: '.min.css'
        }, {
          expand: true,
          cwd: 'module/board/tpl/',
          src: ['board.min.css'],
          dest: 'module/board/tpl/',
          ext: '.min.css'
        }, {
          expand: true,
          cwd: 'module/page/tpl/',
          src: ['page.min.css'],
          dest: 'module/page/tpl/',
          ext: '.min.css'
        }, {
          expand: true,
          cwd: 'theme/default/',
          src: ['index.min.css'],
          dest: 'theme/default/',
          ext: '.min.css'
        }, {
          expand: true,
          cwd: 'theme/default/',
          src: ['login.min.css'],
          dest: 'theme/default/',
          ext: '.min.css'
        }, {
          expand: true,
          cwd: 'common/css/',
          src: ['common.min.css'],
          dest: 'common/css/',
          ext: '.min.css'
        }]
      }
    },
    jshint: {
      files: [
          'module/editor/editor.js',
          'module/board/tpl/board.js',
          'module/page/tpl/page.js',
          'theme/default/index.js',
          'common/js/common.js'
      ],
      options: {
        sub:true,
        evil: true,
        loopfunc: true,
        // options here to override JSHint defaults
        globals: {
          jQuery: true,
          document: true
        }
      }
    },
    jsbeautifier: {
      files: [
          'module/editor/editor.css',
          'module/editor/editor.js',
          'module/board/tpl/board.css',
          'module/board/tpl/board.js',
          'module/page/tpl/page.css',
          'module/page/tpl/page.js',
          'theme/default/login.css',
          'theme/default/index.css',
          'theme/default/index.js',
          'common/css/common.css',
          'common/js/common.js'
      ],
      options: {
        html: {
            indentScripts: "keep",
            indentWithTabs: true,
            unformatted: ["block","a"]
        },
        css: {
            indentWithTabs: true
        },
        js: {
            indentWithTabs: true,
            evalCode: false
        }
      }
    }
    // ,
    // watch: {
    //   files: ['<%= jshint.files %>'],
    //   tasks: ['jshint', 'qunit']
    // }
  });

  //grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks("grunt-jsbeautifier");
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  grunt.registerTask('default', ['jsbeautifier', 'jshint', 'concat', 'uglify', 'cssmin']);

};
