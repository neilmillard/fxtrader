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

var atrtrailingstop = techan.plot.atrtrailingstop()
    .xScale(x)
    .yScale(y);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .tickFormat(d3.format(",.3s"));

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

d3.csv("data.csv", function (error, data) {
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

    var atrtrailingstopData = techan.indicator.atrtrailingstop()(data);
    x.domain(data.map(accessor.d));
    y.domain(techan.scale.plot.atrtrailingstop(atrtrailingstopData).domain());

    svg.append("g")
        .datum(data)
        .attr("class", "candlestick")
        .attr("clip-path", "url(#clip)")
        .call(candlestick);

    svg.append("g")
        .datum(atrtrailingstopData)
        .attr("class", "atrtrailingstop")
        .call(atrtrailingstop);

    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
        .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text("ATR Trailing Stop");
});