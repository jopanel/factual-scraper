<html>
<head>
<title>Factual Wordpress MapBox Example</title>
<script src="https://api.tiles.mapbox.com/mapbox.js/v2.2.1/mapbox.js"></script>
<link href="https://api.tiles.mapbox.com/mapbox.js/v2.2.1/mapbox.css" rel="stylesheet" />
<!-- no fancy styling or boostrap. trying to minimize framework dependencies for a simple example. -->
<style>
body { margin:0; padding:0; }
#map { position:absolute; top:0; bottom:0; width:100%; }
#searchcontent { position: absolute; top: 10px; left: 50px; z-index: 99; background: rgba(255, 255, 255, 0.75);padding:10px;}
.disabled {color: #aaa;}
</style>
</head>
<body>
<?php

/** load factual driver. Make sure the path below matches what you have! */
require_once('./wp-content/plugins/factual-php-driver-master/Factual.php');

/** credentials for Factual and Mapbox */
$factual_api_key     = "YOUR_FACTUAL_API_KEY";
$factual_api_sec     = "YOUR_FACTUAL_API_SECRET";
$mapbox_access_token = "YOUR_MAPBOX_ACCESS_TOKEN";
$mapbox_map_id       = "YOUR_MAP_ID";

/** instantiate Factual driver **/
$factual = new Factual($factual_api_key, $factual_api_sec);

/** GET params */
define(PAGE_SIZE, 10);
$zip    = htmlspecialchars($_GET["postcode"]);
$q      = htmlspecialchars($_GET["q"]);
$page   = htmlspecialchars($_GET["page"]);
$cats   = htmlspecialchars($_GET["categories"]);
$filter = $cats ? "checked" : "";

/** default to first page */
if (!$page || $page < 0){
  $page = 0;
}

/** simple search interface */
print("<div id=\"searchcontent\">\r\n");

/** form */
print("<form class=\"searchform\" name=\"input\" action=\"./\" method=\"get\">\r\n");
print("<b>search</b> for <input class=\"search\" type=\"text\" value=\"" . $q . "\" name=\"q\"/> in ");
print("<b>postcode</b> <input class=\"postcode\" type=\"text\" value=\"" . $zip . "\" name=\"postcode\"/>");
print("<input type=\"submit\" value=\"search\">\r\n");
print("<input type=\"checkbox\" name=\"categories\" value=\"312,347\"" . $filter . "/>Only Search Restaurants &amp; Bars");
print("</form>\r\n");

try{

  /** make the query */
  $query = new FactualQuery;
  $query->search($q);
  $query->offset($page * PAGE_SIZE);
  $query->limit(PAGE_SIZE);
  $query->includeRowCount();
  $query->threshold("default"); // see http://developer.factual.com/search-placerank-and-boost/#existence

  if ($zip){
    $query->field("postcode")->equal($zip);
  }
  if ($cats){
    $query->field("category_ids")->includesAny(explode(',',$cats));
  }
  $res      = $factual->fetch("places-us", $query);
  $data     = $res->getData();
  $firstRow = ($page * PAGE_SIZE) + 1;
  $lastRow  = $firstRow + $res->getIncludedRowCount() - 1;
  $totRows  = count($data);

  /** search result counts */
  print("<div class=\"placecounts\">showing " .  number_format($firstRow) . " through " . number_format($lastRow) . " of " . number_format($res->getTotalRowCount()) . " rows:</div>\r\n");

  /** search result data **/
  print("<div class=\"results\">\r\n");
  $cent_lat = 0.0;
  $cent_lng = 0.0;
  foreach ($data as $datarow){
    print("<p class=\"place\"><span class=\"placename\">" . $datarow[name] . "</span> - ");
    print("<span class=\"placeaddress\">" . $datarow[address] . ", " . $datarow[locality] . "</span></p>\r\n");
    $cent_lat += $datarow[latitude];
    $cent_lng += $datarow[longitude];
  }
  $cent_lat = $cent_lat / $totRows;
  $cent_lng = $cent_lng / $totRows;
  print("</div>\r\n");

  /** paging controls */
  print("<div class=\"paging\">");
  $requiresPrev = (!$page == 0);
  $requiresNext = $totRows == PAGE_SIZE && $totRows != $res->getTotalRowCount();

  /** small bug: paging controls don't real-time check to see if 'Only Find Restaurants' is selected when being clicked. */
  if ($requiresPrev){
    /** first link */
    print("<a href=\"./?q=" . urlencode($q) . "&postcode=" . $zip ."&categories=" . $cats . "&page=0\">first</a>");

    print(" - ");

    /** prev link */
    print("<a href=\"./?q=" . urlencode($q) . "&postcode=" . $zip ."&categories=" . $cats . "&page=" . ($page - 1) . "\">prev</a>");
  }else{
    print("<span class=\"disabled\">first</span> - <span class=\"disabled\">prev</span>");
  }

  print(" - ");

  if ($requiresNext){
    /** next link */
    print("<a href=\"./?q=" . urlencode($q) . "&postcode=" . $zip . "&categories=" . $cats . "&page=" . ($page + 1) . "\">next</a>");
  }else{
    print("<span class=\"disabled\">next</span>");
  }
  print("</div>\r\n");
  print("</div>\r\n");

  print("<div id=\"map\"></div>\r\n");
  print("<script>\r\n");

  /** geoJson for MapBox */
  print("  var geoJson = \r\n");
  print("  {\r\n");
  print("    type: 'FeatureCollection',\r\n");
  print("    features: \r\n");
  print("    [\r\n");

  $i = 0;
  foreach ($data as $datarow){
    print("      {\r\n");
    print("        type: 'Feature',\r\n");
    print("        properties: \r\n");
    print("        {\r\n");
    print("          title: \"" . $datarow[name] . "\",\r\n");
    print("          'marker-color': '#f0a'\r\n");
    print("        },\r\n");
    print("        geometry:\r\n");
    print("        {\r\n");
    print("          type: 'Point',\r\n");
    print("          coordinates: [" . $datarow[longitude] . "," . $datarow[latitude] . "]\r\n");
    print("        }\r\n");
    if (++$i === $totRows){
      print("      }\r\n");
    }else{
      print("      },\r\n");
    }
  }
  print("    ]\r\n");
  print("  };\r\n");

  /** Be sure to register your own mapbox account and populate your key here */
  print("  L.mapbox.accessToken = '".$mapbox_access_token."';\r\n");
  print("  var map = L.mapbox.map('map', '".$mapbox_map_id."');\r\n");
  print("  var featureLayer = L.mapbox.featureLayer().addTo(map);\r\n");
  print("  featureLayer.on('layeradd', function(e) {\r\n");
  print("      var marker = e.layer,\r\n");
  print("          feature = marker.feature;\r\n");
  print("      var popupContent = feature.properties.name + '<br/>' + feature.properties.address;\r\n");

  print("      marker.bindPopup(popupContent,{\r\n");
  print("          closeButton: false\r\n");
  print("      });\r\n");
  print("  });\r\n");

  print("  featureLayer.setGeoJSON(geoJson);\r\n");
  print("  map.fitBounds(featureLayer.getBounds());\r\n");

  print("</script>\r\n");
} catch (Exception $e){
  print("<div class=\"error\">" . $e->getMessage() . "</div>");
}

/** garbage collection (is anything other than datarow really required?) */
unset($factual);
unset($query);
unset($zip);
unset($q);
unset($page);
unset($res);
unset($firstRow);
unset($lastRow);
unset($data);
unset($datarow);
unset($requiresPrev);
unset($requiresNext);
?>

</body>
</html>
