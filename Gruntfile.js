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
                    'lte/js/admin.min.js': ['lte/js/admin.js'],
                    'lte/js/modals-pedidos.min.js': ['lte/js/modals-pedidos.js'],
                    'lte/js/saldos.min.js': ['lte/js/saldos.js'],
                    'lte/js/recepcao.min.js': ['lte/js/recepcao.js'],
                    'lte/js/login.min.js': ['lte/js/login.js'],
                    'lte/js/body-pedidos.min.js': ['lte/js/body-pedidos.js'],
                    'lte/js/rows.min.js': ['lte/js/rows.js'],
                    'lte/js/modals-geral.min.js': ['lte/js/modals-geral.js'],
                    'lte/js/util_lte.min.js': ['lte/js/util_lte.js'],
                    'lte/js/editMode.min.js': ['lte/js/editMode.js'],
                    'lte/js/posts.min.js': ['lte/js/posts.js'],
                    'lte/js/apoio.min.js': ['lte/js/apoio.js'],
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
