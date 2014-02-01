module.exports = function(grunt) {

    // 1. All configuration goes here 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),


watch: {
    css: {
        files: ['css/*.scss'],
        tasks: ['sass'],
        options: {
            spawn: false,
        }
    }
},

sass: {
    dist: {
        options: {
            style: 'nested'
        },
        files: {
            'css/build.css':'css/global.scss'
        }
    } 
}


    });

    // Load All NPM Dependencies
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);


    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['sass', 'watch']);

};