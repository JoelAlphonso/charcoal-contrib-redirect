module.exports = {
    options: {
        spawn:      false,
        livereload: false
    },
    javascript: {
        files: [ '<%= paths.js.src %>/**/*.js', '<%= paths.grunt %>/config/rollup.js' ],
        tasks: [ 'rollup', 'terser', 'notify:javascript' ]
    },
    sass: {
        files: [ '<%= paths.css.src %>/**/*.scss' ],
        tasks: [ 'sass', 'postcss', 'notify:sass' ]
    },
    svg: {
        files: [ '<%= paths.img.src %>/**/*.svg' ],
        tasks: [ 'svg_sprite', 'notify:svg' ]
    },
    tasks: {
        options: {
            reload: true
        },
        files: [ 'Gruntfile.js', '<%= paths.grunt %>/**/*' ]
    }
};
