module.exports = function (grunt) {
    require('jit-grunt')(grunt);

	colorSchemes = {
		blue: {
			primaryColor: '#3170a4',
			darkHighlight: '#98d0ff',
			lightHighlight: '#98d0ff',
			linkHighlightColor: '#3170a4',
			coloredBackgroundTextColor: '#fff'
		},
		gold: {
			primaryColor: '#dab715',
			darkHighlight: '#7a660c',
			lightHighlight: '#7a660c',
			linkHighlightColor: '#dab715',
			coloredBackgroundTextColor: '#333'
		},
		red: {
			primaryColor: '#c42e10',
			darkHighlight: '#95230c',
			lightHighlight: '#f7a898',
			linkHighlightColor: '#c42e10',
			coloredBackgroundTextColor: '#fff'
		}
	};

	var lessTasks = {}
	var colorSchemePath = '';
	var colorSchemeTasks = [];
	for ( color in colorSchemes ) {
		colorSchemePath = 'css/color-schemes/' + color + '.css';
		toolbarColorSchemePath = 'css/color-schemes/toolbar-' + color + '.css';
		lessTasks[ color ] = {
			options: {
				modifyVars: colorSchemes[ color ],
				optimization: 2
			},
			files: {}
		}
		lessTasks[ color ].files[ colorSchemePath ] = 'style.less';
		lessTasks[ color ].files[ toolbarColorSchemePath ] = 'less/openlab-toolbar.less';
		colorSchemeTasks.push( 'less:' + color );
	};

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        htmlclean: {
            options: {
                unprotect: /(<[^\S\f]*\?[^\S\f]*php\b[\s\S]*)/ig,
                protect: /(?:#|\/\/)[^\r\n]*|\/\*[\s\S]*?\*\/\n\r\n\r/ig
            },
            deploy: {
                expand: true,
                cwd: 'parts/source/',
                src: '**/*.php',
                dest: 'parts/'
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            vendor: {
                src: ['js/bootstrap.min.js',
                    'node_modules/jcarousellite/jcarousellite.js',
                    'js/easyaccordion.js',
                    'js/jquery.easing.1.3.js',
                    'js/jquery.mobile.customized.min.js',
                    'js/camera.min.js',
                    'js/jQuery.succinct.mod.js',
                    'js/detect-zoom.js'],
                dest: 'js/dist/vendor.js'
            },
        },
        less: lessTasks,
        watch: {
            styles: {
                files: ['*.less'/*, '../../mu-plugins/css/*.less'*/], // which files to watch
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            },
            scripts: {
                files: ['js/*.js'],
                tasks: ['concat'],
                options: {
                    nospawn: true
                }
            },
            core: {
                files: ['parts/source/**/*.php'], // which files to watch
                tasks: ['htmlclean'],
                options: {
                    nospawn: true
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-htmlclean');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');

	var taskList = ['concat', 'htmlclean', 'watch', 'less'];
	taskList = taskList.concat( colorSchemeTasks );

    grunt.registerTask('default', colorSchemeTasks);
};
