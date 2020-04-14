<?php

require './settings.inc';

// Try to connect to the database.
try {
  $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
  echo $e->getMessage();
  exit;
}

// Get all of the recordings in the past 24 hours.
$query = $db->query("SELECT * FROM data WHERE created_at >= now() - INTERVAL 1 DAY ORDER BY created_at ASC;");

while($row = $query->fetch( PDO::FETCH_ASSOC )){
  $recordings[] = $row;
}

?>
<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>Arduino Indoor Outdoor Temperatures</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>

    body {
      font-family: sans-serif;
      color: #444;
    }

    .line {
      fill: none;
      stroke-width: 3;
    }

    .line__indoor {
      stroke: #ffab00;
    }

    .line__outdoor {
      stroke: #34e823;
    }

    .axis path,
    .axis line {
      fill: none;
      stroke: #000;
      shape-rendering: crispEdges;
    }

    .axis text {
      font-size: 10px;
    }

  </style>
</head>
<body>
<script src="https://d3js.org/d3.v5.min.js"></script>
<script>

    // Array of all recordings in the past 24 hours.
    var recordings = <?php echo json_encode($recordings); ?>;

    // Set variables for margins, width and height.
    var margin = {top: 50, right: 50, bottom: 50, left: 50},
        width = window.innerWidth - margin.left - margin.right,
        height = window.innerHeight - margin.top - margin.bottom;

    // Create an svg element and append it to the body element.
    var svg = d3.select('body').append("svg")
        .attr("width",  width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // Set the ranges.
    var timeConv = d3.timeParse("%Y-%m-%d %H:%M:%S");

    var temperatureRange = [];

    // Create an array of all the temperatures so we can get the min and max values.
    recordings.map(function(recording) {
        temperatureRange.push(recording.indoor_temperature);
        temperatureRange.push(recording.outdoor_temperature);
    });

    // Create the scales for x and y axis.
    var xScale = d3.scaleTime().range([0,width]);
    var yScale = d3.scaleLinear().rangeRound([height, 0]);

    xScale.domain(d3.extent(recordings, function(d){
        return timeConv(d.created_at);
    }));

    yScale.domain([parseFloat(d3.min(temperatureRange)) - 4.00, parseFloat(d3.max(temperatureRange)) + 4]);

    // Create the axis.
    var yaxis = d3.axisLeft().scale(yScale);
    var xaxis = d3.axisBottom().scale(xScale);

    svg.append("g")
        .attr("class", "axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xaxis);

    svg.append("g")
        .attr("class", "axis")
        .call(yaxis);

    // Create the indoor line.
    var indoorLine = d3.line()
        .x(function(d) {
            return xScale(timeConv(d.created_at));
        })
        .y(function(d) {
            return yScale(d.indoor_temperature);
        })
        .curve(d3.curveMonotoneX);

    svg.append("path")
        .data([recordings])
        .attr("class", "line line__indoor")
        .attr("d", indoorLine);

    // Create the outdoor line.
    var outdoorLine = d3.line()
        .x(function(d) {
            return xScale(timeConv(d.created_at));
        })
        .y(function(d) {
            return yScale(d.outdoor_temperature);
        })
        .curve(d3.curveMonotoneX);

    svg.append("path")
        .data([recordings])
        .attr("class", "line line__outdoor")
        .attr("d", outdoorLine);

</script>
</body>

</html>

