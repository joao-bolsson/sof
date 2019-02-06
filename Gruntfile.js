module.exports = function (grunt) {

    grunt.initConfig({

        uglify: {
            options: {
                compress: false,
                mangle: false
            },

            my_target: {
                files: {
                    'ini.min.js': ['ini.js'],
                    'iniLTE.min.js': ['iniLTE.js'],
                    'lte/js/hora.min.js': ['lte/js/hora.js'],
                    'lte/admin/js/admin.min.js': ['lte/admin/js/admin.js'],
                    'lte/admin/js/modals-pedidos.min.js': ['lte/admin/js/modals-pedidos.js'],
                    'lte/admin/js/saldos.min.js': ['lte/admin/js/saldos.js'],
                    'lte/admin/js/recepcao.min.js': ['lte/admin/js/recepcao.js'],
                    'lte/admin/js/login.min.js': ['lte/admin/js/login.js'],
                    'lte/admin/js/body-pedidos.min.js': ['lte/admin/js/body-pedidos.js'],
                    'lte/js/rows.min.js': ['lte/js/rows.js'],
                    'lte/js/modals-geral.min.js': ['lte/js/modals-geral.js'],
                    'lte/js/util_lte.min.js': ['lte/js/util_lte.js'],
                    'lte/js/editMode.min.js': ['lte/js/editMode.js'],
                    'lte/posts/posts.min.js': ['lte/posts/posts.js'],
                    'lte/apoio/apoio.min.js': ['lte/apoio/apoio.js'],
                    'lte/js/contratos.min.js': ['lte/js/contratos.js']
                }
            }
        } // uglify
    });


    // Plugins do Grunt
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Tarefas que serão executadas
    grunt.registerTask('default', ['uglify']);


};
