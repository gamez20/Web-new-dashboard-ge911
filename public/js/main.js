/**
 * Created by payores-03 on 23/02/18.
 */
/************************************************

 User Stories :

 * I can see a Force-directed Graph that shows which campers are posting links on Camper News to which domains.
 * I can see each camper's icon on their node.
 * I can see the relationship between the campers and the domains they're posting.
 * I can tell approximately many times campers have linked to a specific domain from it's node size.
 * I can tell approximately how many times a specific camper has posted a link from their node's size.


 DOUBLE CLICK ON A NODE TO OPEN THE LINK !!!!
 ************************************************/


/** GLOBALS *********************************/

/**
 * freeCodeCamp Camper News is going to be closed. I saved a snapshot of one of the
 * last datasets, in order to keep this legacy project working.
 * Links to discussion maybe inactive.
 */

// var dataUrl = 'https://www.freecodecamp.com/news/hot';
//var dataUrl = 'http://localhost/GE911/js/arbor.json';
var dataUrl = 'http://34.218.86.122/GE911/emergency/emergencyGraphicData';

// SETUP
var width = 780,
    height = 780;

var repulsion = -120;
var linkLength = 80;

var dimFactor = 9;

var legendPos = {'right' : 120, 'bottom' : 35}

/********************************************/

var buildGraph = function (data) {
    var graph = {
        'nodes': [],
        'links': []
    };

    var urlTest =/^https?:\/\/([\da-z\.-]+)/;

    data.forEach(function(el){
        //var domain = el.link.match(urlTest)[1];
        var domain = el.emergency;
        //var user = el.author.username;
        var user = el.userName;

        var domainInd = graph.nodes.findIndex(function(node) {
            return node.name === domain;
        });
        var userInd = graph.nodes.findIndex(function(node) {
            return node.name === user;
        });

        if (userInd === -1) {
            userInd = graph.nodes.length;
            var newNode = {
                'name': user,
                'id_user': el.id_user,
                'count': 1,
                //'picture': el.author.picture,
                'picture': el.photo,
                'isUser': true
            };
            graph.nodes.push(newNode);
        } else {
            graph.nodes[userInd].count++;
        };

        if (domainInd === -1) {
            domainInd = graph.nodes.length;
            var newNode = {
                'name': domain,
                'id_user': el.id_user,
                'count': 1,
                'emergency': el.emergency,
                'date': el.date
            };
            graph.nodes.push(newNode);
        } else {
            graph.nodes[domainInd].count++;
        };


        graph.links.push({
            'source': userInd,
            'target': domainInd
        });

    });
    return graph;
};
var fccUrl = 'https://www.freecodecamp.com/';
var draw = function(graph) {

    var maxUserCount = d3.max(graph.nodes, function(d) {return d.isUser ? d.count : 0 })
    var maxDomainCount = d3.max(graph.nodes, function(d) {return d.isUser ? 0 : d.count})

    var force = d3.layout.force()
        .charge(repulsion)
        .linkDistance(linkLength)
        .size([width, height]);

    var svg = d3.select("#graph").append("svg")
        .attr("width", width)
        .attr("height", height);
    force
        .nodes(graph.nodes)
        .links(graph.links)
        .start();

    var link = svg.selectAll(".link")
        .data(graph.links)
        .enter().append("line")
        .attr("class", "link")
    var node = svg.selectAll(".node")
        .data(graph.nodes)
        .enter().append("g")
        .attr("class", function(d) { return d.isUser ? 'node user' : 'node domain'})
        .call(force.drag);
    var domainNode = d3.selectAll('.node.domain')
        .append('circle')
        .attr({
            "r": function (d) {return Math.sqrt(50*d.count)},
            "fill" :  '#00f0f0'
        })
    var pi4 = 0.7535;
    var userNode = d3.selectAll('.node.user')
        .append('rect')
        .attr({
            "x": function (d) {return -pi4*dimFactor*Math.sqrt(d.count)},
            'y': function(d) {return -pi4*dimFactor*Math.sqrt(d.count) },
            'height':  function(d) {return pi4*dimFactor*2*Math.sqrt(d.count) },
            'width':  function(d) {return pi4*dimFactor*2*Math.sqrt(d.count) },
            'fill': '#e7e0e7'
        })
    node.append("image")
        .attr("xlink:href", function(d) {return d.picture})
        .attr("x", -10)
        .attr("y", -10)
        .attr("width", 30)
        .attr("height", 30);
    node
        .on("mouseover", function(d) {
            document.getElementById('id_user').value = d.id_user;

            d3.select("#tooltipName")
                .text(d.name);
            d3.select("#tooltipDate")
                .text(d.date);
            d3.select('#tooltip')
                .style("left", Math.max(0, d3.event.pageX + 10) + "px")
                .style("top", (d3.event.pageY) + "px")
                .style("opacity", 0.8) ;
        })
        .on('mouseout', function () {
            d3.select('#tooltip')
                .style('opacity', 0);
        })
        .on('dblclick', function(d) {
            document.getElementById('btn_user').click();
        });

    force.on("tick", function() {
        link.attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });

        node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
    });
};
d3.json(dataUrl, function(err,json) {
    if (err) {
        console.log(err);
        return;
    }
    var graph = buildGraph (json)
    draw(graph);
});