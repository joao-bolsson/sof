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
                    'iniLTE.min.js': ['iniLTE.js']
                }
            }
        }, // uglify
        
       'ftp-deploy': {
            build: {
                auth: {
                    host: 'ftp.sofhusm.net.br',
                    port: 21,
                    authKey: 'key'
                },
                src: './',
                dest: './web/',
                exclusions: ['./ini.js', './iniLTE.js', './node_modules', './*.sql', '.git', '.gitignore', '.ftppass', 'package.json', 'Gruntfile.js', './*.md']
            }
        }

    });


    // Plugins do Grunt
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-ftp-deploy');

    // Tarefas que serão executadas
    grunt.registerTask('default', ['uglify']);
    
    // Tarefa para deploy
    grunt.registerTask('f', ['ftp-deploy']);

};
