module.exports = function(grunt) {
  grunt.initConfig({
    less: {
      patternfly: {
        files: {
          'web/libs/patternfly/css/patternfly.css': 'node_modules/patternfly/src/less/patternfly.less'
        },
        options: {
          paths: [
            'node_modules/patternfly/src/less',
            'node_modules/patternfly/node_modules'
          ],
          compress: true
        }
      },
      patternflyAdditions: {
        files: {
          'web/libs/patternfly/css/patternfly-additions.css': 'node_modules/patternfly/src/less/patternfly-additions.less'
        },
        options: {
          paths: [
            'node_modules/patternfly/src/less',
            'node_modules/patternfly/node_modules'
          ],
          compress: true
        }
      }
    },
    'cssmin': {
      production: {
        files: [{
          expand: true,
          cwd: 'web/libs/patternfly/css',
          src: ['patternfly*.css', '!*.min.css'],
          dest: 'web/libs/patternfly/css',
          ext: '.min.css'
        }]
      }
    },
    'copy': {
      production: {
        files: [
          { src: 'node_modules/patternfly/node_modules/jquery/dist/jquery.min.js', dest: 'web/libs/jquery.min.js' },
          { src: 'node_modules/bootstrap-3-typeahead/bootstrap3-typeahead.min.js', dest: 'web/libs/bootstrap3-typeahead.min.js' },
          { src: 'node_modules/datatables.net/js/jquery.dataTables.js', dest: 'web/libs/dataTables/jquery.dataTables.js' },
          { src: 'node_modules/datatables.net-responsive/js/dataTables.responsive.min.js', dest: 'web/libs/dataTables/dataTables.responsive.min.js' },
          { src: 'node_modules/signature_pad/dist/signature_pad.min.js', dest: 'web/libs/signature_pad.min.js' },
          { src: 'node_modules/js-marker-clusterer/src/markerclusterer.js', dest: 'web/libs/markerclusterer/markerclusterer.js' },
          { expand: true, cwd: 'node_modules/js-marker-clusterer/images/', src: 'm*', dest: 'web/libs/markerclusterer/images/' },
          { expand: true, cwd: 'node_modules/patternfly/node_modules/bootstrap/dist/', src: '**', dest: 'web/libs/bootstrap/' },
          { expand: true, cwd: 'node_modules/patternfly/node_modules/bootstrap-select/dist/', src: '**', dest: 'web/libs/bootstrap-select/' },
          { expand: true, cwd: 'node_modules/patternfly/node_modules/bootstrap-datepicker/dist/', src: '**', dest: 'web/libs/bootstrap-datepicker/' },
          { expand: true, cwd: 'node_modules/patternfly/dist/', src: '**', dest: 'web/libs/patternfly/' },
          { expand: true, cwd: 'node_modules/patternfly-bootstrap-combobox/', src: '**', dest: 'web/libs/patternfly-bootstrap-combobox/' },
          { expand: true, cwd: 'node_modules/patternfly-bootstrap-treeview/dist', src: '**', dest: 'web/libs/patternfly-bootstrap-treeview/' },
          { expand: true, cwd: 'node_modules/blueimp-file-upload/css/', src: '**', dest: 'web/libs/jquery-file-upload/css' },
          { expand: true, cwd: 'node_modules/blueimp-file-upload/js/', src: '**', dest: 'web/libs/jquery-file-upload/js' },
          { expand: true, cwd: 'node_modules/magnific-popup/dist/', src: '**', dest: 'web/libs/magnific-popup'}
        ]
      }
    },
    'clean': {
      libs: ['web/libs/']
    }
  });

  grunt.registerTask('default', ['clean', 'copy']);
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-clean');
};
