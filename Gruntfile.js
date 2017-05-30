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
                    'hora.min.js': ['hora.js'],
                    'lte/admin/js/admin.min.js': ['lte/admin/js/admin.js'],
                    'lte/admin/js/modals-pedidos.min.js': ['lte/admin/js/modals-pedidos.js'],
                    'lte/admin/js/saldos.min.js': ['lte/admin/js/saldos.js'],
                    'lte/admin/js/recepcao.min.js': ['lte/admin/js/recepcao.js'],
                    'lte/admin/js/login.min.js': ['lte/admin/js/login.js'],
                    'lte/admin/js/body-pedidos.min.js': ['lte/admin/js/body-pedidos.js'],
                    'lte/solicitacoes/js/rows.min.js': ['lte/solicitacoes/js/rows.js']
                }
            }
        } // uglify
    });


    // Plugins do Grunt
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Tarefas que serão executadas
    grunt.registerTask('default', ['uglify']);


};
