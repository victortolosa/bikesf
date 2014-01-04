<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
<link rel='stylesheet' href='css/build.css' />
<title>Should I Bike?</title>

<?php
/*wunderground API

$weather = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/conditions/q/CA/San_Francisco.xml");
$astronomy = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/astronomy/q/CA/San_Francisco.xml");
$hourly = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/hourly/q/CA/San_Francisco.xml");

$weather_info = $weather->xpath('current_observation'); 
$astronomy_info = $astronomy->xpath('moon_phase');
$realtemp = $weather_info[0]->temp_f ;
$feels = $weather_info[0]->feelslike_f;

*/

//Testing files
  $weather = simplexml_load_file("xml/conditions.xml"); /* gets current observation */
  $astronomy = simplexml_load_file("xml/astronomy.xml"); /* gets sunrise and sunset times */
  $hourly = simplexml_load_file("xml/hourly.xml"); /* gets hourly forecast */

/*Sunrise fixed 24clock*/
$sunrise = $astronomy_info[0]->sunrise->hour;
if ($sunrise > 12) {
  $sunrise = $sunrise - 12;
}
if ($astronomy_info[0]->sunrise->hour < 11) {
  $sunriseTime = "AM";
}
if ($astronomy_info[0]->sunrise->hour > 11) {
  $sunriseTime = "PM";
}
/*sunset fixed 24clock*/
$sunset = $astronomy_info[0]->sunset->hour;
if ($sunset > 12) {
  $sunset = $sunset - 12;
}
if ($astronomy_info[0]->sunset->hour < 11) {
  $sunsetTime = "AM";
}
if ($astronomy_info[0]->sunset->hour > 11) {
  $sunsetTime = "PM";
}

/*checks and returns percipitation*/
$perciptoday = $weather_info[0]->precip_today_in;
$perciphour = $weather_info[0]->precip_1hr_in;

if ($perciptoday == "0.00"){
    $showperciptoday = "";
} else {
    $showperciptoday = "Percipitation: " . $perciptoday . " In";
}
if ($perciphour == "0.00"){
    $showperciphour = "";
} else {
    $showperciphour = "Percip Per Hr: " . $perciphour . " In";
}

/*checks and returns windspeed*/
$gust = $weather_info[0]->wind_gust_mph;
if ($gust == "0"){
     $showgust = "";
} else {
    $showgust = "Wind Gust: " . $gust . "MPH";
}
if ($windchill = "NA") {
     $windchill = "None";
 } else {
     $windchill = "Windchill: " . $weather_info[0]->windchill_f . "&deg";
 }

/*writes hourly forecast*/
function hourlyforecast($i){
  global $hourly;
    $time = $hourly->hourly_forecast->forecast[$i]->FCTTIME->civil;
    $condition = $hourly->hourly_forecast->forecast[$i]->condition;
    $temp = $hourly->hourly_forecast->forecast[$i]->temp->english;
    return "<p>" . $time . " - " . $condition . " - " . $temp . "&degF </p>";
}

/* start - declaring 8hr forecast variables */
function forecast($condition){
	global $hourly;
	$hourlyForecast = $hourly->hourly_forecast->forecast[$condition]->condition;
	return $hourlyForecast;
}

function forecastTime($time){
	global $hourly;
	$hourlyForecastTime = $hourly->hourly_forecast->forecast[$time]->FCTTIME->civil;
	return $hourlyForecastTime;
}
/* end - declaring 8hr forecast variables */

/* warns for bad weather in 8hr forecast */
function checkForecast($checkthis){
	
	 if (forecast($checkthis) == "Freezing Rain" ||
      forecast($checkthis) == "Rain" ||
      forecast($checkthis) == "Sleet" ||
      forecast($checkthis) == "Snow" ||
      forecast($checkthis) == "Rain Showers" ){
global $why;
 $why = forecast($checkthis) . " at " . forecastTime($checkthis - 1);
}
  if (forecast($checkthis) == "Chance of Rain" ||
      forecast($checkthis) == "Chance of Freezing Rain" ||
      forecast($checkthis) == "Chance of Sleet" ||
      forecast($checkthis) == "Chance of Snow"){
    global $why;
    $why = "It's okay to bike, but consider" . forecast($checkthis). " at " . forecastTime($checkthis); 
    }
}

/*run the bad weather check*/
for ($i=0;$i<9;$i++){
checkForecast($i);
}

/*checks current weather*/
  $currentWeather = $weather_info[0]->weather;
if ($currentWeather == "Drizzle" || $currentWeather == "Rain" || $currentWeather == "Light Drizzle" ||$currentWeather == "Heavy Drizzle" || $currentWeather == "Rain" || $currentWeather == "Light Rain" || $currentWeather == "Heavy Rain" || $currentWeather == "Rain"){
    $bike = "No";
} else {
    $bike = "Yes";
}
/*checks next hourly forecast*/
 if (forecast(0) == "Rain" || forecast(0) == "Light Drizzle" || forecast(0) == "Heavy Drizzle" || forecast(0) == "Drizzle" ||  forecast(0) == "Rain Showers") {
global $bike;
    $bike =  "No";
    $why = "It's gonna rain soon!";
}

?>
</head>
<html>
<body> 
  <div id="container">
    <div id="main">
      <h1>Should I Bike?</h1>
      <h3>-<?php echo $weather_info[0]->display_location->city; ?>-</h3>
      <p> <?php echo $bike;?> </p>
      <p> <?php echo $why;?> </p>
      <p>Current Weather:  <?php echo $weather_info[0]->weather ?></p>
    </div>
      <div id="details">
        <p>Temp: <?php echo $weather_info[0]->temp_f ?> &degF</p>
        <p>Humidity: <?php echo $weather_info[0]->relative_humidity ?></p>
        <p>Wind Direction: <?php echo $weather_info[0]->wind_dir ?></p>
        <p>Wind Speed: <?php echo $weather_info[0]->wind_mph ?>MPH</p>
        <p><?php echo $showgust ?> </p>
        <p>Wind Chill: <?php echo $windchill; ?></p> 
        <p><?php echo $showperciptoday; ?></p>
        <p><?php echo $showperciphour; ?></p>
        <p>Sunrise at: <?php echo $sunrise?>:<?php echo $astronomy_info[0]->sunrise->minute;?> <?php echo $sunriseTime?></p>
        <p>Sunset at: <?php echo $sunset ?>:<?php echo $astronomy_info[0]->sunset->minute;?> <?php echo $sunsetTime?></p>
      </div>
      <div id="bottom">  
        <div id="forecast">
         <p><strong>8hr Forecast:</strong><p>
        <?php
        for ($x=0; $x<8; $x++){
        	echo hourlyforecast($x);
        }
        ?>
        </div>
      </div>
  </div>
</body>
</html>