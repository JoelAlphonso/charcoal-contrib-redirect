module.exports = {
    notify_hooks: {
        options: {
            enabled:  true,
            success:  true,
            duration: 3,
            title:    '<%= package.name %>',
            max_jshint_notifications: 5
        }
    },
    build: {
        options: {
            message: 'Redirect Contrib assets are compiled'
        }
    },
    javascript: {
        options: {
            message: 'JavaScript is compiled'
        }
    },
    json: {
        options: {
            message: 'JSON is linted'
        }
    },
    sass: {
        options: {
            message: 'CSS is compiled'
        }
    },
    svg: {
        options: {
            message: 'SVG is concatenated'
        }
    },
    watch: {
        options: {
            message: 'Assets are being watched for changes'
        }
    }
};
