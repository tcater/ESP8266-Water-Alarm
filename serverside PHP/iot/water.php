<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Arial, Helvetica, sans-serif;
			text-align: center;
			background-color: lightgrey;
			width:700px;
			height:700px;
		}
		.device{
			background-color: darkgrey;
			width: 250px;
			height: 250px;
			border: 2px solid black;
			border-radius: 20px;
			margin:5px;
		}
		.age{
			font-size:1em;
		}
		.title{
			font-size:2em;
		}
	</style>
	<script>
		function getTime(){
			var t = Math.floor((new Date()/1000-eventtime+10));
			var seconds = Math.floor( t % 60 );
			var minutes = Math.floor( (t/60) % 60 );
			var hours = Math.floor( (t/(60*60)) % 24 );
			var days = Math.floor( t/(60*60*24) );
			var s = "";
			if(days>0){
				if(days>1){s="s";}
				document.getElementById("age").innerHTML = days + ' day'+s+' ago';
				setTimeout("getTime()", 7200000);
			}else if(hours>0){
				if(hours>1){s="s";}
				document.getElementById("age").innerHTML = hours + ' hour'+s+' ago';
				setTimeout("getTime()", 900000);
			}else if(minutes>0){
				if(minutes>1){s="s";}
				document.getElementById("age").innerHTML = minutes + ' minute'+s+' ago';
				setTimeout("getTime()", 30000);
			}else{
				document.getElementById("age").innerHTML = seconds + ' seconds ago';
				setTimeout("getTime()", 1000);
			}
		}
	</script>
</head>
<body onload=getTime();>

<?php
function humanTiming ($time)
{
    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
}

$file = 'logs/WaterAlarm.txt';
if (file_exists($file)) {
	echo ("<div class='device'><h1>Water Alarm</h1>");
	if(time()-filemtime($file)<7200){ //less then 2 hours
		echo ("<div id='state'><img src=images/wet.png></div>");	
	}else if(time()-filemtime($file)<86400){//less then 1 day
		echo ("<div id='state'><img src=images/damp.png></div>");
	}else{//older then 1 day
		echo ("<div id='state1'><img src=images/dry.png></div>");
	}
	echo ("<div class='age' id='age'>".humanTiming(filemtime($file))." ago.</div> </div>");
}
?>

</body>
</html>