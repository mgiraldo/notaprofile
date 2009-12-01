<?php

require_once('config/config.php');
require_once 'lib/class/NotAProfile.php';

if(!NotAProfile::estaLogeado()) {
	header("Location: /m?r=" . urlencode("/gps"));
}

?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>not_a_profile : gps key</title>
<link href="/css/estilosMob.css" rel="stylesheet" type="text/css" />
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport"/>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=<?php echo $app["apiKey"] ?>" type="text/javascript"></script>
<script>
var map;
var marker;
function initialize() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("gmap"));
		map.setCenter(new GLatLng(4.620913,-74.083643, true), 13);
		map.setUIToDefault();
		invokeLocation();	
	}
}
function invokeLocation() {
	navigator.geolocation.getCurrentPosition(gMapper);
}
function gMapper (position) {
	var myLatLng = new GLatLng(position.coords.latitude, position.coords.longitude, true);
	map.setCenter(myLatLng, 18);
    marker = new GMarker(myLatLng, {draggable: true, clickable: true, bouncy: false});

	GEvent.addListener(map, "click", function(overlay,pos) {
		if (overlay==null && pos) {
			marker.closeInfoWindow();
			marker.setLatLng(pos);
			marker.openInfoWindowHtml("<div id=\"bubble\"><a href=\"mailto:upload@notaprofile.com?from=<?php echo $_SESSION['username'] ?>&subject="+pos.lat()+","+pos.lng()+"&body=your_message_here_or_delete_for_none\">create a key here</a></div>", {maxWidth:225});
		}
	});
	
    GEvent.addListener(marker, "dragstart", function() {
    	marker.closeInfoWindow();
    });

    GEvent.addListener(marker, "dragend", function(pos) {
							if (pos) {
    							marker.openInfoWindowHtml("<div id=\"bubble\"><a href=\"mailto:upload@notaprofile.com?from=<?php echo $_SESSION['username'] ?>&subject="+pos.lat()+","+pos.lng()+"&body=your_message_here_or_delete_for_none\">create a key here</a></div>", {maxWidth:225});
							}
    });

    GEvent.addListener(marker, "click", function(pos) {
							if (pos) {
    							marker.openInfoWindowHtml("<div id=\"bubble\"><a href=\"mailto:upload@notaprofile.com?from=<?php echo $_SESSION['username'] ?>&subject="+pos.lat()+","+pos.lng()+"&body=your_message_here_or_delete_for_none\">create a key here</a></div>", {maxWidth:225});
							}
    });

    marker.openInfoWindowHtml("<div id=\"bubble\"><a href=\"mailto:upload@notaprofile.com?from=<?php echo $_SESSION['username'] ?>&subject="+position.coords.latitude+","+position.coords.longitude+"&body=your_message_here_or_delete_for_none\">create a key here</a><br />or drag the pointer to your location</div>", {maxWidth:225});

	map.addOverlay(marker);
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<body onload="initialize()" onunload="GUnload()">
<?php include("./inc/cabezoteMob.php"); ?>
<div id="gmap">Location unknown</div>
</body>
</html>