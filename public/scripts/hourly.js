//var harmonic;
//$.getJSON("/api/Data/Get?id=1", { })
//  .done(function (json) {
//    //console.log("JSON Data: " + json.users[3].name);
//    harmonic = json;
//  })
//  .fail(function (jqxhr, textStatus, error) {
//    var err = textStatus + ", " + error;
//    console.log("Request Failed: " + err);
//  });

var margin = {top: 20, right: 70, bottom: 50, left: 50},
    width = window.innerWidth - margin.left - margin.right,
    height = window.innerHeight - margin.top - margin.bottom - 40;

// Edited & working with C#: "d-MMM-yy hh:mm:ss"
var parseDate = d3.time.format("%d-%b-%y %H:%M:%S").parse,
    timeFormat = d3.time.format('%Y-%m-%d %H:%M:%S'),
    valueFormat = d3.format(',.5fs');

var x = techan.scale.financetime()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var candlestick = techan.plot.candlestick()
    .xScale(x)
    .yScale(y);

var trendline = techan.plot.trendline()
    .xScale(x)
    .yScale(y)
    .on("mouseenter", enter)
    .on("mouseout", out)
    .on("drag", drag);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var valueText = svg.append('text')
    .style("text-anchor", "end")
    .attr("class", "coords")
    .attr("x", width - 5)
    .attr("y", 15);

// Crosshairs

var xTopAxis = d3.svg.axis()
    .scale(x)
    .orient("top");

var yRightAxis = d3.svg.axis()
    .scale(y)
    .orient("right");

var ohlcAnnotation = techan.plot.axisannotation()
    .axis(yAxis)
    .format(d3.format(',.5fs'));

var ohlcRightAnnotation = techan.plot.axisannotation()
    .axis(yRightAxis)
    .translate([width, 0]);

var timeAnnotation = techan.plot.axisannotation()
    .axis(xAxis)
    .format(d3.time.format('%Y-%m-%d %H:%M'))
    .width(90)
    .translate([0, height + 20]);

var timeTopAnnotation = techan.plot.axisannotation()
    .axis(xTopAxis);

var crosshair = techan.plot.crosshair()
    .xScale(x)
    .yScale(y)
    .xAnnotation([timeAnnotation, timeTopAnnotation])
    .yAnnotation([ohlcAnnotation, ohlcRightAnnotation])
    .on("enter", enter)
    .on("out", out)
    .on("move", move);

var coordsText = svg.append('text')
    .style("text-anchor", "end")
    .attr("class", "coords")
    .attr("x", width - 5)
    .attr("y", 15);

//End cross hairs

d3.csv("/data.csv", function (error, data) {
    //d3.csv("/api/Data", function (error, data) {
    var accessor = candlestick.accessor();

    data = data.slice(0, 200).map(function (d) {
        return {
            date: parseDate(d.Date),
            open: +d.Open,
            high: +d.High,
            low: +d.Low,
            close: +d.Close,
            volume: +d.Volume
        };
    }).sort(function (a, b) {
        return d3.ascending(accessor.d(a), accessor.d(b));
    });

    x.domain(data.map(accessor.d));
    y.domain(techan.scale.plot.ohlc(data, accessor).domain());

    svg.append("g")
        .datum(data)
        .attr("class", "candlestick")
        .call(candlestick);

    d3.svg.axis().scale()

    //////////////////////////////////////////////////

    ////Trendline
    //var d1 = new Date(harmonic.XDateTime); // "2015-08-24 19:00:00");
    //d1 = parseDate(harmonic.XDateTime);
    ////d1 = new Date("Mon Aug 24 2015 21:00:00 GMT+0");
    ////d1 = new Date("24-Aug-15 19:00:00");
    ////d1 = new Date("2015-08-24 07:00:00");
    //var d2 = new Date(harmonic.ADateTime);
    //d2 = parseDate(harmonic.ADateTime);

    //// Put me back in
    //var points = {};
    //points.xX = x(d1);
    //points.xY = y(harmonic.XPrice);
    //points.aX = x(d2);
    //points.aY = y(harmonic.APrice);
    //points.bX = x(new Date(harmonic.BDateTime));
    //points.bY = y(harmonic.BPrice);
    //var path = "M " + points.xX + "," + points.xY
    //  + " L " + points.aX + "," + points.aY
    //  + " L " + points.bX + "," + points.bY
    //  + " Z";

    //// Put me back in
    //svg.append("path")          // attach a path
    //  .style("stroke", "black")  // colour the line
    //  .attr("stroke-width", 1)
    //  .style("fill", "blue")     // remove any fill colour
    //  .style("opacity", 0.8)
    //  .attr("d", path);  // path commands

    //////////////////

    svg.append("g").attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
        .append("text")
        //.attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end");
    //.text(harmonic.Symbol);


    // Crosshairs
    svg.append('g')
        .attr("class", "crosshair")
        .call(crosshair);
    // end crosshairs
});

function drag(d) {
    refreshText(d);
}

function refreshText(d) {
    valueText.text(
        "Start: [" + timeFormat(d.start.date) + ", " + valueFormat(d.start.value) +
        "] End: [" + timeFormat(d.end.date) + ", " + valueFormat(d.end.value) + "]"
    );
}

function getDate(d) {
    return new Date(d.jsonDate);
}

function enter() {
    coordsText.style("display", "inline");
}

function out() {
    coordsText.style("display", "none");
}

function move(coords) {
    coordsText.text(
        timeAnnotation.format()(coords[0]) + ", " + ohlcAnnotation.format()(coords[1])
    );
}