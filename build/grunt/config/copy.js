module.exports = {
    tabulator: {
        files: [
            {
                src: '<%= paths.npm %>/tabulator-tables/dist/js/tabulator.min.js',
                dest: '<%= paths.js.dist %>/tabulator.min.js'
            },
            {
                src: '<%= paths.npm %>/tabulator-tables/dist/css/tabulator_bootstrap4.min.css',
                dest: '<%= paths.css.dist %>/tabulator.min.css'
            }
        ]
    }
}
