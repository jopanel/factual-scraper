<?php
		

		set_time_limit(0);
		ignore_user_abort(true);
		require_once("_inc.php");
		require_once('Factual.php');
    	$thelimit = 0;
    	$sqlcheck = "SELECT count(id) as 'total' from pings WHERE date >= DATE_ADD(CURDATE(), INTERVAL -1 DAY)";
    	$resultcheck = mysql_query($sqlcheck) or die(mysql_error());
    	while ($rowz = mysql_fetch_array($resultcheck)) {
    	$hasleft = $rowz["total"];
    	} 
    	if ($hasleft == 10000 || $hasleft > 10000) {
				exit();
				die();
    	} else {
    		$hasleft = (10000 - $hasleft);
    		if ($hasleft > 500 ) { $thelimit = 500; } else {$thelimit = $hasleft;}
    	}
    	$key = "";
		$secret = "";
		$data = "";
		$haskid = 0;
		$hasexclusions = 0;
		$excludeIDs = [];
		$totalcalls = 0;
		$buildcount = 0;
		$duplicatesarr = [];
		$deletefilters = 0;
		$buildziparray = [];
		$sql101 = "SELECT  `zip_code_id` ,  `lon` ,  `lat` 
					FROM  `zip_code` 
					WHERE  `zip_code_id` NOT 
					IN (

					SELECT  `zipid` 
					FROM  `pings`
					) AND `lon` != '0' AND `lat` != '0' LIMIT ".$thelimit;
		$hasrows = 0;
		$result101 = mysql_query($sql101) or die(mysql_error());
		if($result101 == FALSE) { 
    		die(mysql_error()); 
		} else {
		while($row000 = mysql_fetch_array($result101)){
			$hasrows = 1;
			$zipid = $row000["zip_code_id"];
			$lon = $row000['lon'];
			$lat = $row000['lat'];
			$hasoffset = 0;
			$newoffsetcount = 0;
			$buildziparray[] = array("lon"=>$lon, "lat"=>$lat, "zipid"=>$zipid, "offsets"=>0);
		}
	}
	if ($hasrows == 0) {
		$sql411 = "select z.zip_code_id, z.lon, z.lat, max(p.offsets) as 'offsets'
					from `zip_code` z 
					join (select zipid,records, MAX(offsets) as 'offsets' from pings group by zipid) p ON p.zipid = z.zip_code_id 
					where p.records IN (50,100,150,200,250,300,350,400,450) AND p.offsets < 500
					group by zip_code LIMIT ".$thelimit;
		$result411 = mysql_query($sql411) or die(mysql_error());
		if($result411 == FALSE) { 
	    		die(mysql_error()); 
			} else {
		while($row123 = mysql_fetch_array($result411)){
				$zipid = $row123["zip_code_id"];
				$lon = $row123['lon'];
				$lat = $row123['lat'];
				$hasoffset = 1;
				$offset = $row123["offsets"];
				$buildziparray[] = array("lon"=>$lon, "lat"=>$lat, "zipid"=>$zipid, "offsets"=>$offset);
			}
		}
	}
	$buildcount = 0;
		foreach ($buildziparray as $call) {
			$lon = $call["lon"];
			$lat = $call["lat"];
			$zipid = $call["zipid"];
			$offset = $call["offsets"];
			$buildcount += 1;
			if ($buildcount == 498) {
				sleep(61);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://localhost/factualscraper/test.php");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_exec($ch);
				curl_close($ch);
				exit();
				die();
				$buildcount = 0;
			}
			$factual = new Factual($key,$secret);	
			$query = new FactualQuery;
			$query->limit(50);
			if ($hasoffset == 1) {
				$newoffsetcount = $offset + 50;
				$query->offset($offset);
			} else {
				$newoffsetcount = 0;
			}
			$query->within(new FactualCircle($lat, $lon, 5000));
			$query->field("category_ids")->in(292); 
			// 144 is 	Retail > Fashion > Jewelry and Watches [done]
			// 172 is 	Retail > Tobacco [done]
			// 287 is 	Businesses and Services > Personal Care > Tattooing [done]
			// 124 is   Adult [done]
			// 128 is   Beauty Products
			// 138 is   Convenience Stores
			// 143 is   Clothing & Accessories
			// 442 is   Discount Stores
			// 292 is real estate companies
	  		$entity = $factual->fetch("places-us", $query);
			$entity = $entity->getData();
			$totalcalls += 1;
			$recordsretrieved = 0;
			foreach ($entity as $res){
					if (!isset($res["wifi"]) || empty($res["wifi"])) {$wifi="NULL";} else {$wifi="'".mysql_real_escape_string($res["wifi"])."'";}
					if (!isset($res["website"]) || empty($res["website"])) {$website="NULL";} else {$website="'".mysql_real_escape_string($res["website"])."'";}
					if (!isset($res["tel"]) || empty($res["tel"])) {$tel="NULL";} else {$tel="'".mysql_real_escape_string($res["tel"])."'";}
					 if (!isset($res["region"]) || empty($res["region"])) {$region="NULL";} else {$region="'".mysql_real_escape_string($res["region"])."'";}
					if (!isset($res["rating"]) || empty($res["rating"])) {$rating="NULL";} else {$rating="'".mysql_real_escape_string($res["rating"])."'";}
					if (!isset($res["postcode"]) || empty($res["postcode"])) {$postcode="NULL";} else {$postcode="'".mysql_real_escape_string($res["postcode"])."'";}
					if (!isset($res["neighborhood"]) || empty($res["neighborhood"])) {$neighborhood="NULL";} else {$neighborhood="'".mysql_real_escape_string(json_encode($res["neighborhood"]))."'";}
					if (!isset($res["name"]) || empty($res["name"])) {$name="NULL";} else {$name="'".mysql_real_escape_string($res["name"])."'";}
					if (!isset($res["longitude"]) || empty($res["longitude"])) {$longitude="NULL";} else {$longitude="'".mysql_real_escape_string($res["longitude"])."'";}
					if (!isset($res["latitude"]) || empty($res["latitude"])) {$latitude="NULL";} else {$latitude="'".mysql_real_escape_string($res["latitude"])."'";}
					if (!isset($res["hours_display"]) || empty($res["hours_display"])) {$hours_display="NULL";} else {$hours_display="'".mysql_real_escape_string($res["hours_display"])."'";}
					if (!isset($res["hours"]) || empty($res["hours"])) {$hours="NULL";} else {$hours="'".json_encode($res["hours"])."'";}
					if (!isset($res["fax"]) || empty($res["fax"])) {$fax="NULL";} else {$fax="'".mysql_real_escape_string($res["fax"])."'";}
					if (!isset($res["factual_id"]) || empty($res["factual_id"])) {$factual_id="NULL";} else { $factual_id="'".$res["factual_id"]."'"; }
					if (!isset($res["email"]) || empty($res["email"])) {$email="NULL";} else {$email="'".mysql_real_escape_string($res["email"])."'";}
					if (!isset($res["country"]) || empty($res["country"])) {$country="NULL";} else {$country="'".mysql_real_escape_string($res["country"])."'";}
					if (!isset($res["category_labels"]) || empty($res["category_labels"])) {$category_labels="NULL";} else {$category_labels="'".json_encode($res["category_labels"])."'";}
					if (!isset($res["category_ids"]) || empty($res["category_ids"])) {$category_ids="NULL";} else {$category_ids="'".json_encode($res["category_ids"])."'";}
					if (!isset($res["address"]) || empty($res["address"])) {$address="'NA'";} else {$address="'".mysql_real_escape_string($res["address"])."'"; }
						$isfuckingthere = 0;
		    			$sql = "SELECT factual_id FROM leads WHERE factual_id = '".$res["factual_id"]."'";
		    			$result = mysql_query($sql) or (mysql_error());
		    			while ($rowz = mysql_fetch_array($result)) {
		    				$isfuckingthere = 1;
		    			}
		    			if ($isfuckingthere == 0) {
		    				$recordsretrieved += 1;
		    			$sql = "INSERT INTO leads (address,category_ids,category_labels,country,email,factual_id,fax,hours,hours_display,latitude,longitude,name,neighborhood,postcode,rating,region,tel,website) VALUES ( $address , $category_ids , $category_labels , $country , $email , $factual_id , $fax , $hours , $hours_display , $latitude , $longitude , $name , $neighborhood , $postcode , $rating , $region , $tel , $website )";
		    			mysql_query($sql) or die(mysql_error());
		    			if (!isset($res["address"]) || empty($res["address"])) {
		    			 	} else {
		    					$duplicatesarr[] = $res['address']." ".$res['name'];
		    				}
		    			}
    		}
    		$sql = "INSERT INTO pings (date,zipid,offsets,records) VALUES (NOW(),".$zipid.",".$newoffsetcount.",".$recordsretrieved.")";
    		mysql_query($sql) or die(mysql_error());
  		} 
  		echo $totalcalls;
?>
