document.addEventListener('DOMContentLoaded', function() {

    var map = document.getElementById(cssID);

    // if map is not visible, do not init datamap
    if (map.offsetWidth == 0) {

        // check if bootstrap 3 is loaded
        if ((typeof $().emulateTransitionEnd == 'function')) {
            // bootstrap collapse
            $('.collapse').on('shown.bs.collapse', function() {
                initDataMap();
            });
        }
        return false;
    }

    initDataMap();

    function initDataMap() {
        var mapConfig = config;

        mapConfig.element = map;

        if (states) {
            mapConfig.data = states;
        }

        mapConfig.done = function(datamap) {

            if (bubbles) {

                datamap.bubbles(bubbles);

                //standard plugin interface. Function takes 3 params - a layer ('<g>', data, and the options object)
                function handleBombLabels(layer, data, options) {
                    var self = this;
                    options = options || {};

                    d3.selectAll('.datamaps-bubble').attr('data-foo', function(info) {

                        if (info === undefined) return false;
                        if (info.addLabel != true) return false;

                        //convert lat/lng into x/y
                        var center = self.latLngToXY(info.latitude, info.longitude);

                        var xOffset = info.labelOffsetX ? parseFloat(info.labelOffsetX) : 0,
                            yOffset = info.labelOffsetY ? parseFloat(info.labelOffsetY) : -5;

                        var x, y;

                        x = center[0] + xOffset;
                        y = center[1] + yOffset;

                        layer.append('text').
                            attr('x', x) //this could use a little massaging
                            .attr('y', y).
                            style('font-size', (options.fontSize || 10) + 'px').
                            style('text-shadow', '0px 0px 2px rgba(255,255,255,0.7)').
                            style('fill', options.labelColor || '#000').
                            attr('class', 'datamaps-bubble-label datamap-label').
                            attr('data-info', JSON.stringify(info)).
                            text(info.title);
                    });
                }

                //register the plugin to datamaps
                datamap.addPlugin('bombLabels', handleBombLabels);

                //call the plugin. The 2nd param is options and it will be sent as `options` to the plugin function.
                //Feel free to add to these options, change them, etc
                datamap.bombLabels(bubbles, {labelColor: '#000', labelKey: 'fillKey'});

                // go to url on click
                $('.datamaps-bubble, .datamaps-bubble-label').click(function(e) {
                    console.log($(e.target).data('info'));
                    if ($(e.target).data('info').link == null) {
                        return false;
                    }
                    if ($(e.target).data('info').target) {
                        window.open($(e.target).data('info').link.replace(/^\/|\/$/g, ''), '_blank');
                    } else {
                        window.location.href = $(e.target).data('info').link.replace(/^\/|\/$/g, '');
                    }
                });
            }

            if (states) {
                //standard plugin interface. Function takes 3 params - a layer ('<g>', data, and the options object)
                function handleStateLabels(layer, data, options) {
                    var self = this;
                    options = options || {};
                    d3.selectAll('.datamaps-subunit').attr('data-foo', function(d) {
                        var center = self.path.centroid(d);
                        var info = data[d.id];

                        if (info === undefined) return false;
                        if (info.addLabel != true) return false;

                        var xOffset = info.labelOffsetX ? parseFloat(info.labelOffsetX) : -7.5,
                            yOffset = info.labelOffsetY ? parseFloat(info.labelOffsetY) : 5;

                        var x, y;

                        x = center[0] + xOffset;
                        y = center[1] + yOffset;

                        if (info.smallState) {
                            layer.append('line').
                                attr('x1', x - 3).
                                attr('y1', y - 5).
                                attr('x2', center[0]).
                                attr('y2', center[1]).
                                style('stroke', options.labelColor || '#000').
                                style('stroke-width', options.lineWidth || 1);
                        }

                        layer.append('text').
                            attr('x', x).
                            attr('y', y).
                            style('font-size', (options.fontSize || 10) + 'px').
                            style('fill', options.labelColor || '#000').
                            attr('class', 'datamaps-subunit-label datamap-label').
                            attr('data-info', JSON.stringify(info)).
                            text(info ? info.title : d.id);

                        return 'bar';
                    });
                }

                //register the plugin to datamaps
                datamap.addPlugin('stateLabels', handleStateLabels);

                datamap.stateLabels(states);

                datamap.svg.selectAll('.datamaps-subunit').on('click', function() {
                    var info = JSON.parse($(this).attr('data-info'));

                    if (info.link == null) return false;

                    window.location.href = '/' + info.link.replace(/^\/|\/$/g, '');
                });
            }
        };

        datamap = new Datamap(mapConfig);

        d3.select(window).on('resize', function() {
            datamap.resize();
        });
    }
});


