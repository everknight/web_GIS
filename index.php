<html>
  <head>
    <title>CEHI WebMap</title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src='../web_GIS/mapbox.js'></script>
	<script src='../web_GIS/jquery-1.11.1.js'></script>
	<script src='../web_GIS/nprogress.js'></script>
	<script src='../web_GIS/MI_Tract.js'></script>
	<?php include 'MI_layers.php';?>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />
	<link href = '../web_GIS/nprogress.css' rel='stylesheet' />
	<link rel="shortcut icon" href='../web_GIS/cehi.ico' />
	
	<style>
		body { margin:0; padding:0; }
        #map { position:absolute; top:0; bottom:0; width:100%;}
		
		.menu-ui {
			background:#fff;
			position:absolute;
			top:3%;right:3%;
			z-index:1;
			background-image: url(../web_GIS/icons-000000@2x.png);
			background-repeat: no-repeat;
			height: 50px;  
			width:50px; 
			border-radius:25px;  
			background-size: 50px 50px;  
			box-shadow: 2px 2px 1.5px #777777;
		}
		
		.logoBox{
			position:absolute;
			left:2%;
			bottom:3%;
			visibility:visible;
		}
		
		.legend label,
		.legend span {
			visibility:visible;
			display:block;
			float:left;
			height:15px;
			width: 70px;
			line-height: 15px;
			text-align:center;
			font-size:9px;
			color:#808080;
			margin-bottom:5px;
		 }
		.legend{
			width: 350px;
			visibility:visible;
			position:absolute;
			bottom:5%;
			padding: 10px 10px 3px 10px;
			right: 2%;
			background:#FFF;
			border-radius:5px;
		}
		.colors{
			margin-top:8px;
		}
		.colors,  .labels{
			text-align:center;
		}
		select {
			font-size:120%;
		}
		
	</style>
   </head>
   <body>
    <nav id='variables' class='menu-ui'></nav>
    <div id='map'></div>
	<div id='logo' class='logobox'><a href="http://cehi.snre.umich.edu" target = '_blank'><img class = 'logopng' src="../web_GIS/LOGO.png" style="border: none; bottom: 10px; left: 10px"></a></div>
	<div id='legendbox'>
	  <nav class='legend clearfix'>
		<strong id='legend_title'>We need a title</strong>
		<div class = 'colors'>
			<span style='background:#ffffb2;'></span>
			<span style='background:#fecc5c;'></span>
			<span style='background:#fd8d3c;'></span>
			<span style='background:#f03b20;'></span>
			<span style='background:#bd0026;'></span>
		</div>
		<div class = 'labels'>
			<label id = 'label1'>0-20%</label>
			<label id = 'label2'>40%</label>
			<label id = 'label3'>60%</label>
			<label id = 'label4'>80%</label>
			<label id = 'label5'>100%</label>
		</div>
	</div>
	
	
	<script>
		//Layer Control
		NProgress.set(0.1);
		var layers = document.getElementById('menu-ui');
		var closeTooltip;
		
		//Display area setting
        var southWest = L.latLng(41.277972, -92.275139),
                northEast = L.latLng(48.458498, -79.926514),
                bounds = L.latLngBounds(southWest, northEast);
        //Initialize Map Object
		var map = L.mapbox.map('map', 'lruiyang.ieap3p3j', {  //'lruiyang.ieap3p3j'
            accessToken: 'pk.eyJ1IjoibHJ1aXlhbmciLCJhIjoic0lZREI2VSJ9.ZEoYy45fcxMV6RBqseIWoQ',
			minZoom: 6,
            maxZoom: 17,
            maxBounds: bounds
        }).setView([43.609805, -84.694873], 7);
		
		////map.legendControl.addLegend(document.getElementById('legend').innerHTML);
		
		// Be nice and credit data source, American Fact Finder, NPPES.
		map.attributionControl.addAttribution('Data Source: ' +
		  '<a href="http://factfinder2.census.gov/">US Census Bureau</a> and <a href="https://nppes.cms.hhs.gov">NPPES</a>');
		
		//Prepare for Pop_up
		var popup = new L.Popup({ autoPan: false });
		
		var variables = [
			'Population Density of Michigan in year 2010',
			'Population of Michigan by Census Tract in year 2010',
			'Number of National Plan Providers in 2014',
			'National Plan Providers per Person Estimate in 2014'];
		var ranges = {};
		addselect = true;
 
		//Set Logo interaction to help User to redirect themselves to CEHI website 
		$("#logo a img")
		  .mouseover(function(){
			$(this).attr('src', '../web_GIS/LOGO_shadow.png');
		  })
		  .mouseleave(function(){
			$(this).attr('src', '../web_GIS/LOGO.png');
		  });
		
		
		//Initialize the layer
		NProgress.set(0.1);
		var progress= 0;
		var loaded = 0;
		var totaln = jsondata.length;
				
		function initlayers(init){
			window.loaded = 1;
		}
		
		function getStyle(feature,defaultOptions) {
			if(loaded == 0){
				var i = 0; 
				notquit = true;
				while(feature.properties.nums == 0 & i<jsondata.length & notquit ){
					if(jsondata[i].GEOID10 == feature.properties.GEOID10){  ///Need to change if Prime Key changes
						feature.properties.nums = jsondata[i][thefield]; ///Need to change if Prime Key changes
						notquit = false;
						jsondata.splice(i,1);
					}
					i++;
				}
				progress += 1;
				if(progress % 100 == 0){
					NProgress.set(progress/totaln);
				}
			}
						
			return {
				weight: 0.2,
				opacity: 1,
				color: getColor(feature.properties.nums, thefield),
				width: 0.1,
				fillOpacity: 0.4,
				fillColor: getColor(feature.properties.nums, thefield)
			};
		}
			
		function getColor(d, fieldname) {
			if(fieldname == 'Pop_den'){
				return d < 139.86 ?  '#ffffb2':
					  d < 1057.5 ? '#fecc5c':
					  d < 2618.1 ? '#fd8d3c':
					  d < 4801 ? '#f03b20':
					  '#bd0026';
			} else if(fieldname == 'HD01_VD01') {
				return d < 2289 ?  '#ffffb2':
					  d < 3011 ? '#fecc5c':
					  d < 3772 ? '#fd8d3c':
					  d < 4771 ? '#f03b20':
					  '#bd0026';
			} else if(fieldname == 'Join_Count') {
				return d < 5 ?  '#ffffb2':
					  d < 13 ? '#fecc5c':
					  d < 29 ? '#fd8d3c':
					  d < 63 ? '#f03b20':
					  '#bd0026';
			} else {
				return d < 0.00152 ?  '#ffffb2':
					  d < 0.0036 ? '#fecc5c':
					  d < 0.0076 ? '#fd8d3c':
					  d < 0.017 ? '#f03b20':
					  '#bd0026';
			}
		}
						
		function onEachFeature(feature, layer) {
			layer.on({
				mousemove: mousemove,
				mouseout: mouseout,
				click: zoomToFeature
			});
				
			function mousemove(e) {
				var layer = e.target;
				if(layer._map.getZoom() <= 10){
					popup.setLatLng(e.latlng);
					if(thefield == 'Pop_den'){
						fieldlabel = 'population density in 2010';
						unit = 'people/(square mile)';
					} else if(thefield == 'HD01_VD01'){
						fieldlabel = 'population in 2010';
						unit = 'people';
					} else if(thefield == 'Join_Count'){
						fieldlabel = 'Number of National Plan Provider in 2014';
						unit = 'Provider(s)';
					} else {
						fieldlabel = 'Number of National Plan Provider Per Person in 2014';
						unit = 'Provider/person';
					}
					popup.setContent('<div class="marker-title"> The ' + fieldlabel + ' is</br><h2>' + layer.feature.properties.nums + '</h2> '+ unit + '</div>');

					if (!popup._map) popup.openOn(map);
					window.clearTimeout(closeTooltip);
				}
				
				// highlight feature
				layer.setStyle({
					weight: 3,
					opacity: 0.3,
					fillOpacity: 0.7
				});

				if (!L.Browser.ie && !L.Browser.opera) {
					layer.bringToFront();
				}
			}
		
			//Mouse out events need to Add all layers
			function mouseout(e) {
				if(map.hasLayer(AGLayer)){
					AGLayer.resetStyle(e.target);
				}
				if(layer._map.getZoom() <= 10){
					closeTooltip = window.setTimeout(function() {
						map.closePopup();
					}, 100);
				}
			}	
			
			function zoomToFeature(e) {
				if(layer._map.getZoom() > 10){
					popup.setLatLng(e.latlng);
					if(thefield == 'Pop_den'){
						fieldlabel = 'population density in 2010';
						unit = 'people/(square mile)';
					} else if(thefield == 'HD01_VD01'){
						fieldlabel = 'population in 2010';
						unit = 'people';
					} else if(thefield == 'Join_Count'){
						fieldlabel = 'Number of National Plan Provider in 2014';
						unit = 'Provider(s)';
					} else {
						fieldlabel = 'Number of National Plan Provider Per Person in 2014';
						unit = 'Provider/person';
					}
					popup.setContent('<div class="marker-title"> The ' + fieldlabel + ' is</br><h2>' + layer.feature.properties.nums + '</h2> '+ unit + '</div>');

					if (!popup._map) popup.openOn(map);
					window.clearTimeout(closeTooltip);
				} else {
					map.fitBounds(e.target.getBounds());
				}
			}
		}
				
		
			//onRemove: function(map){
				//map.removeLayer(geolayer);
				//geolayer = this.geolayer;
			//};
		
		var AGLayer = new  L.geoJson(mi_tract, {
						style: getStyle,
						onEachFeature: onEachFeature
					});
		AGLayer.on('layeradd', initlayers(AGLayer))
		//"Pop_den", 'Population Density of Michigan in year 2010');
		
		function setMapLayer(name){
			var fieldname;
			if(name == 'Population of Michigan by Census Tract in year 2010'){
				fieldname = 'HD01_VD01';
				colorlable = [2289, 3011, 3772, 4771];
			} else if(name == 'Population Density of Michigan in year 2010'){
				fieldname = 'Pop_den';
				colorlable = [139.86, 1057.5, 2618.1, 4801];
			} else if(name == 'Number of National Plan Providers in 2014'){
				fieldname = 'Join_Count';
				colorlable = [5,13,29,63];
			} else {
				fieldname = 'NPIperPers';
				colorlable = [0.00152,0.0036,0.0076,0.017];
			}
			if (map.hasLayer(AGLayer)) {
				AGLayer._field = fieldname;
				AGLayer._title = name;
				map.removeLayer(AGLayer);
				map.addLayer(AGLayer);
			}
			map.addLayer(AGLayer);
			document.getElementById("legend_title").innerHTML = name;
			document.getElementById("label1").innerHTML = '< '+colorlable[0];
			document.getElementById("label2").innerHTML = colorlable[0]+' - '+colorlable[1];
			document.getElementById("label3").innerHTML = colorlable[1]+' - '+colorlable[2];
			document.getElementById("label4").innerHTML = colorlable[2]+' - '+colorlable[3];
			document.getElementById("label5").innerHTML = '> ' + colorlable[3];
		}
		
		
		map.addLayer(AGLayer);
		NProgress.done();
		colorlable = [139.86, 1057.5, 2618.1, 4801];
		document.getElementById("legend_title").innerHTML = 'Population Density of Michigan in year 2010';
		document.getElementById("label1").innerHTML = '< '+colorlable[0];
		document.getElementById("label2").innerHTML = colorlable[0]+' - '+colorlable[1];
		document.getElementById("label3").innerHTML = colorlable[1]+' - '+colorlable[2];
		document.getElementById("label4").innerHTML = colorlable[2]+' - '+colorlable[3];
		document.getElementById("label5").innerHTML = '> ' + colorlable[3];
	
    </script>
   </body>