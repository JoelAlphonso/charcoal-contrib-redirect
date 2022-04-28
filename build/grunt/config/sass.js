module.exports = {
    options: {
        sourceMap:   false,
        outputStyle: 'expanded'
    },
    app: {
        files: {
            '<%= paths.css.dist %>/charcoal.redirect.css': '<%= paths.css.src %>/**/charcoal.redirect.scss'
        }
    },
    vendors: {
        files: {
            '<%= paths.css.dist %>/charcoal.redirect.vendors.css': '<%= paths.css.src %>/**/charcoal.redirect.vendors.scss'
        }
    }
};
