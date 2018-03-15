module.exports = function (grunt) {
    require('jit-grunt')(grunt);

	colorSchemes = {
		blue: {
			primaryColor: '#1d5f7b',
			breadcrumbHighlight: '#113849',
			darkHighlight: '#113849',
			lightHighlight: '#8ccae4',
			linkHighlightColor: '#1d5f7b',
			coloredBackgroundTextColor: '#fff',
			loginForegroundColor: '#1d5f7b',
			loginBackgroundColor: '#8ccae4',
			loginTextColor: '#fff',
			siteLinkColor: '#444'
		},
		green: {
			primaryColor: '#b6d498',
			breadcrumbHighlight: '#d4e6c3',
			darkHighlight: '#9fd764',
			lightHighlight: '#9fd764',
			linkHighlightColor: '#b6d498',
			coloredBackgroundTextColor: '#333',
			loginForegroundColor: '#d4e6c3',
			loginBackgroundColor: '#b6d498',
			loginTextColor: '#333',
			siteLinkColor: '#444'
		},
		red: {
			primaryColor: '#a9280e',
			breadcrumbHighlight: '#95230c',
			darkHighlight: '#8c210b',
			lightHighlight: '#fbd8d1',
			linkHighlightColor: '#a9280e',
			coloredBackgroundTextColor: '#fff',
			loginForegroundColor: '#a9280e',
			loginBackgroundColor: '#c42e10',
			loginTextColor: '#fff',
			siteLinkColor: '#444'
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
                files: ['*.less', 'less/*.less'], // which files to watch
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
