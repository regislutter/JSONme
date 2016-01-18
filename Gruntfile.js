/**
 * Created by rlutter on 15-11-09.
 */
module.exports = function(grunt) {
    // Report the task-execution time in the command line
    require('time-grunt')(grunt);
    // Task configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'assets/css/main.css': 'assets/css/sass/main.scss'
                }
            }
        },
        exec: {
            package: {
                command: 'cd app && zip -r ../neulasta-biosimilar-visual-aid.zip *'
            }
        },
        compress: {
            main: {
                options: {
                    archive: 'export/neulasta-bios-corporate.zip',
                    pretty: true
                },
                files: [                {
                    expand: true,
                    cwd: 'bower_components/',
                    src: ['jquery/dist/jquery.min.js','jquery-ui/jquery-ui.min.js','jqueryui-touch-punch/jquery.ui.touch-punch.min.js'],
                    dest: 'bower_components/'
                },{
                    expand: true,
                    cwd: 'assets/css/',
                    src: ['**/*'],
                    dest: 'assets/css/'
                }, {
                    expand: true,
                    cwd: 'flows/',
                    src: ['**/*'],
                    dest: 'flows/'
                }, {
                    expand: true,
                    cwd: 'assets/images/',
                    src: ['**/*'],
                    dest: 'assets/images/'
                },
                    {
                        expand: true,
                        cwd: 'assets/js/',
                        src: ['**/*'],
                        dest: 'assets/js/'
                    },{
                        expand: true,
                        cwd: '',
                        src: ['*.html','*.plist']
                    }]
            }
        },
        concat: {
            options: {
                separator: ''
            },
            dist: {
                src: ['assets/js/*.js', '!assets/js/libs.js', '!assets/js/app.js', '!assets/js/app.min.js'],
                dest: 'assets/js/app.js'
            },
            bower: {
                src: ['bower_components/jquery/dist/*.min.js', 'bower_components/**/*.min.js'],
                dest: 'js/libs.js'
            }
        },
        uglify: {
            options: {
                mangle: {
                    except: ['jQuery']
                }
            },
            my_target: {
                files: {
                    'assets/js/app.min.js': ['assets/js/app.js']
                }
            }
        },
        csso: {
            dynamic_mappings: {
                expand: true,
                src: ['assets/css/*.css', 'assets/css/!*.min.css'],
                ext: '.min.css'
            }
        },
        watch: {
            scripts: {
                files: ['assets/js/*.js', 'assets/css/sass/*.scss', '*.plist'],
                tasks: ['sass', 'concat:dist', 'uglify', 'compress'],
                options: {
                    livereload: true,
                    spawn: false
                }
            }
        }
    });

    // Load the plugins
    grunt.loadNpmTasks('grunt-contrib-compress'); // compress
    grunt.loadNpmTasks('grunt-csso'); // ccso
    grunt.loadNpmTasks('grunt-concat-css'); // concat_css
    grunt.loadNpmTasks('grunt-contrib-concat'); // concat
    grunt.loadNpmTasks('grunt-contrib-uglify'); // uglify
    grunt.loadNpmTasks('grunt-contrib-sass'); // sass
    grunt.loadNpmTasks('grunt-contrib-watch'); // watch

    // Use `grunt` command to run the task
    grunt.registerTask('default', ['sass']);
    grunt.registerTask('serve', ['watch']);
    grunt.registerTask('comprime', ['sass', 'concat:bower', 'uglify', 'csso']);
};