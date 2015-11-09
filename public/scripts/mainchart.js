var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%d-%b-%y").parse;

var x = techan.scale.financetime()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var candlestick = techan.plot.candlestick()
    .xScale(x)
    .yScale(y);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .tickFormat(d3.format(",.4f"));

//var ohlcAnnotation = techan.plot.axisannotation()
//    .axis(yAxis)
//    .format(d3.format(',.4f'));
//
//var timeAnnotation = techan.plot.axisannotation()
//    .axis(xAxis)
//    .format(d3.time.format('%Y-%m-%d'))
//    .width(65)
//    .translate([0, height]);
//
//var crosshair = techan.plot.crosshair()
//    .xScale(x)
//    .yScale(y)
//    .xAnnotation(timeAnnotation)
//    .yAnnotation(ohlcAnnotation);
//
//var supstance = techan.plot.supstance()
//    .xScale(x)
//    .yScale(y)
//    .annotation(ohlcAnnotation);

var svg = d3.select("div.chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

svg.append("clipPath")
    .attr("id", "clip")
    .append("rect")
    .attr("x", 0)
    .attr("y", y(1))
    .attr("width", width)
    .attr("height", y(0) - y(1));

svg.append("g")
    .datum(data)
    .attr("class", "candlestick")
    .attr("clip-path", "url(#clip)")
    .call(candlestick);

svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);

svg.append("g")
    .attr("class", "y axis")
    .call(yAxis);

//svg.append("g")
//    .attr("class", "y annotation left")
//    .datum([{value: 74}, {value: 67.5}, {value: 58}, {value:40}]) // 74 should not be rendered
//    .call(ohlcAnnotation);
//
//svg.append("g")
//    .attr("class", "x annotation bottom")
//    .datum([{value: x.domain()[30]}])
//    .call(timeAnnotation);

svg.append('g')
    .attr("class", "crosshair")
    .call(crosshair);

//svg.append("g")
//    .attr("class", "supstances analysis")
//    .attr("clip-path", "url(#ohlcClip)")
//    .datum(supstanceData)
//    .call(supstance);

d3.csv("/api/candles", function (error, data) {
    var accessor = candlestick.accessor();

    data = data.map(function (d) {
        // Open, high, low, close generally not required, is being used here to demonstrate colored volume
        // bars
        return {
            date: parseDate(d.Date),
            volume: +d.Volume,
            open: +d.Open,
            high: +d.High,
            low: +d.Low,
            close: +d.Close
        };
    }).sort(function (a, b) {
        return d3.ascending(accessor.d(a), accessor.d(b));
    });

    x.domain(data.map(accessor.d));
    y.domain(techan.scale.plot.ohlc(data, accessor).domain());
    //var supstanceData = [
    //    { start: new Date(2015, 2, 11), end: new Date(2015, 2, 14), value: 0.9800 },
    //    { start: new Date(2014, 10, 21), end: new Date(2014, 10, 27), value: 0.9450 }
    //];

    svg.select("g.candlestick").datum(data);
});


