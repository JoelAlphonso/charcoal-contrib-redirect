module.exports = {
    options: {
        // banner: '/*! <%= package.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
    },
    app: {
        files: {
            '<%= paths.js.dist %>/charcoal.redirection-list.min.js': [
                '<%= paths.js.dist %>/charcoal.redirection-list.js'
            ]
        }
    }
};
