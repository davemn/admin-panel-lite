/*!
 * Gruntfile!
 */

module.exports = function (grunt) {
  'use strict';

  // Project configuration.
  grunt.initConfig({

    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    
    clean: {
      site: ['dist', '<%= pkg.name %>.zip', '<%= pkg.main %>.css', '<%= pkg.main %>.haml', '<%= pkg.main %>.php']
    },
    
    // handlebars: {
    //   site: {
    //     options: {
    //       namespace: "AdminPanel.templates",
    //       processName: function(filepath){
    //         // Example:
    //         // filepath := "views/table_item.hbs"
    //         //   => "table_item"
    //         
    //         var filename = filepath.replace(/views\/(.*)\.hbs/, '$1');
    //         return filename;
    //       }
    //     },
    //     files: {
    //       'views/select_division.hbs.js': 'views/select_division.hbs'
    //     }
    //   }
    // },
    
    'compile-handlebars': {
      site: {
        files: [{
          src: '<%= pkg.main %>.haml.hbs',
          dest: '<%= pkg.main %>.haml'
        }],
        templateData: 'view-data/<%= pkg.main %>.json'
      }
    },
    
    haml: {
      site: {
        src: ['<%= pkg.main %>.haml'],
        dest: '<%= pkg.main %>.php'
      }
    },
    
    less: {
      site: {
        src: ['less/<%= pkg.main %>.less'],
        dest: '<%= pkg.main %>.css'
      }
    },
    
    copy: {
      site: {
        expand: true,
        src: [
          '<%= pkg.main %>.php', 'login.php', '<%= pkg.main %>.css', '.htaccess',
          'response.util.php','RecordSet.php','delete.php','replace.php','save.php','save-attachment.php',
          'bower_components/jquery/dist/**', 
          'bower_components/underscore/*.js',
          'bower_components/bootstrap/dist/**',
          'bower_components/backbone/backbone-min.js', 
          'bower_components/font-awesome/css/**', 'bower_components/font-awesome/fonts/**', 
          'bower_components/html5shiv/dist/**',
          'bower_components/respond/dest/**',
          'bower_components/shared-styles/**',
          'bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.*',
          'bower_components/datatables/media/js/jquery.dataTables.js',
          'bower_components/selectize/dist/css/selectize.bootstrap3.css',
          'bower_components/selectize/dist/js/standalone/selectize.min.js'
        ],
        dest: 'dist'
      }
    },
    
    zip: {
      site: {
        cwd: 'dist/',
        src: ['dist/**/*.*', 'dist/**/.*'],
        dest: '<%= pkg.name %>.zip'
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-haml');
  grunt.loadNpmTasks('grunt-compile-handlebars');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-zip');

  // Build only.
  grunt.registerTask('build', ['clean', 'less', 'compile-handlebars', 'haml']);
  
  // Full distribution task.
  grunt.registerTask('dist', ['build', 'copy:site', 'zip']);

  // Default task.
  grunt.registerTask('default', ['dist']);
};
