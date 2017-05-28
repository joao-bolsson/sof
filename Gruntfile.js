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
                    'lte/admin/js/admin.min.js' : ['lte/admin/js/admin.js'],
                    'lte/admin/js/pedidos.min.js' : ['lte/admin/js/pedidos.js'],
                    'lte/admin/js/saldos.min.js' : ['lte/admin/js/saldos.js']
                }
            }
        } // uglify
    });


    // Plugins do Grunt
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Tarefas que serão executadas
    grunt.registerTask('default', ['uglify']);


};
