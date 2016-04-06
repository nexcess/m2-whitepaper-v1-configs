/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';

var tasks = {},
    _ = require('underscore');

function init(config) {
    var grunt  = require('grunt'),
        expand = grunt.file.expand.bind(grunt.file),
        themes, root, host, port, files;

    root         = config.root;
    port         = config.port;
    files        = config.files;
    host         = _.template(config.host)({ port: port });
    themes       = config.themes;

    _.each(themes, function (themeData, themeName) {
        var specs,
            configs,
            render;

        _.extend(themeData, { root: root });

        render  = renderTemplate.bind(null, themeData);
        specs   = files.specs.map(render);
        specs   = expand(specs).map(cutJsExtension);
        configs = files.requirejsConfigs.map(render);

        tasks[themeName] = {
            src: configs,
            options: {
                host: host,
                template: render(files.template),
                vendor: files.requireJs,

                /**
                 * @todo rename "helpers" to "specs" (implies overriding grunt-contrib-jasmine code)
                 */
                helpers: specs
            }
        }
    });
}

function renderTemplate(data, template) {
    return _.template(template)(data);
}

function cutJsExtension(path) {
    return path.replace(/\.js$/, '');
}

function getTasks() {
    return tasks;
}

module.exports = {
    init: init,
    getTasks: getTasks
};