<!DOCTYPE html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Should I Bike?</title>
  <meta name="description" content="Should I Bike?">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/build.css">
  <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-47017712-1', 'victortolosa.com');
  ga('send', 'pageview');
  </script>
<?php

//if(file_exists('http://api.wunderground.com/api/87df596a569c0e60/conditions/q/CA/San_Francisco.xml')) {

$xmlfail = false;
/*wunderground API*/
//$weather = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/conditions/q/CA/San_Francisco.xml");
//$astronomy = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/astronomy/q/CA/San_Francisco.xml");
//$hourly = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/hourly/q/CA/San_Francisco.xml");

//} else {
//$xmlfail = true;
//$location = "API is down.";

//Testing files
$weather = simplexml_load_file("xml/conditions-fail.xml"); /* gets current observation */
$astronomy = simplexml_load_file("xml/astronomy-fail.xml"); /* gets sunrise and sunset times */
$hourly = simplexml_load_file("xml/hourly.xml"); /* gets hourly forecast */
//}

$weather_info = $weather->xpath('current_observation'); 
$astronomy_info = $astronomy->xpath('moon_phase');
$realtemp = $weather_info[0]->temp_f ;
$location =  $weather_info[0]->display_location[0]->city;

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
    if($condition == "Light Fog" || $condition == "Heavy Fog" || $condition == "Fog" || $condition == "Patches of Fog" || $condition == "Haze" || $condition == "Overcast") {
      $hrIcon = "A";
    } else if ( $condition == "Partly Cloudy" || $condition == "Mostly Cloudy" || $condition == "Scattered Clouds" || $condition == "Cloudy"){
      $hrIcon = "H";
    } else if ( $condition == "Snow" || $condition == "Hail"){
      $hrIcon = "V";
    } else if ( $condition == "Rain" || $condition == "Chance of Rain" || $condition == "Rain Showers" || $condition == "Drizzle" || $condition == "Light Drizzle" || $condition == "Heavy Drizzle" || $condition == "Light Rain" || $condition == "Heavy Rain") {
      $hrIcon = "R";
    } else {
      $hrIcon = "B";
    }

    return "<li><span><h1>" . $hrIcon . "</h1><p>" . $time . " </p><p> " . $condition . "</p></span></li>";
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

/*checks current weather*/
  $currentWeather = $weather_info[0]->weather;
if ($currentWeather == "Drizzle" || $currentWeather == "Rain" || $currentWeather == "Light Drizzle" ||$currentWeather == "Heavy Drizzle" || $currentWeather == "Rain" || $currentWeather == "Light Rain" || $currentWeather == "Heavy Rain" || $currentWeather == "Rain"){
    $bike = "Don't bike in";
    $icon = "R";
} else if ($currentWeather == "Light Fog" || $currentWeather == "Mostly Cloudy" || $currentWeather == "Heavy Fog" || $currentWeather == "Fog" || $currentWeather == "Overcast" || $currentWeather == "Partly Cloudy" || $currentWeather == "Mostly Cloudy" || $currentWeather == "Scattered Clouds") {
  $icon = "A";
  $bike = "It's okay to bike in";
} else {
  $icon = "B";
  $bike = "It's okay to bike in";
}

/*checks next hourly forecast*/
 if ($bike == "It's okay to bike in" && forecast(0) == "Rain" || forecast(0) == "Chance of Rain" ||  forecast(0) == "Light Drizzle" || forecast(0) == "Heavy Drizzle" || forecast(0) == "Drizzle" ||  forecast(0) == "Rain Showers") {
global $bike;
global $icon;
 
  $location = "";
  $bike =  "Consider " .  forecast(0) . " at ". forecastTime(0);
}

/* warns for bad weather in hourly forecast */
/*
function checkForecast($checkthis){

   if ($bike == "It's okay to bike in" && (
      forecast($checkthis) == "Freezing Rain" ||
      forecast($checkthis) == "Rain" ||
      forecast($checkthis) == "Sleet" ||
      forecast($checkthis) == "Snow" ||
      forecast($checkthis) == "Rain Showers") ){
  global $bike;
 $bike = "Maybe, consider the forecast in at" . forecastTime($checkthis);
}
  if (forecast($checkthis) == "Chance of Rain" ||
      forecast($checkthis) == "Chance of Freezing Rain" ||
      forecast($checkthis) == "Chance of Sleet" ||
      forecast($checkthis) == "Chance of Snow"){
 
  global $icon;
  $icon = "T";
    $bike = "It's okay to bike, but consider" . forecast($checkthis). " at " . forecastTime($checkthis); 

    }
}

for ($i=0;$i<10;$i++){
checkForecast($i);
}
*/
?>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
      <div class="container">
      <div class="boolean">
      <div class="answer">
      <span class="current-info">
        <h1 class="icon"><?php echo $icon; ?></h1>
        <h3> <?php echo $weather_info[0]->temp_f ?>&deg;F<br><?php echo $weather_info[0]->weather ?></h3>
      </span>
      <h1> 
      <?php
   
       echo $bike;
      
      ?> 
      <em><?php echo $location ?></em><span class="arrow">+</span></h1>
      
      <ul class="data">
      <li>Sunrise at: <?php echo $sunrise?>:<?php echo $astronomy_info[0]->sunrise->minute;?> <?php echo $sunriseTime?></li>
      <li>Sunset at: <?php echo $sunset ?>:<?php echo $astronomy_info[0]->sunset->minute;?> <?php echo $sunsetTime?></li>
      <li>Humidity: <?php echo $weather_info[0]->relative_humidity ?></li>
      <li>Wind Direction: <?php echo $weather_info[0]->wind_dir ?></li>
      <li>Wind Speed: <?php echo $weather_info[0]->wind_mph ?>MPH</li>
      <li>Wind Chill: <?php echo $windchill; ?></li>
      <li><?php echo $showperciptoday; ?></li>
      <li><?php echo $showperciphour; ?></li>
      </ul>
      </div>


        </div>
        <div class="forecast">
            <ul>
                 <?php
      if($xmlfail == false){
        for ($x=0; $x<10; $x++){
          echo hourlyforecast($x);
        }
      } else {
        echo "";
      }
        ?>
            </ul>
        </div>
        </div>  
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script >
   $('.arrow').on ("click", function(){
    var newText = $(this).text() == "+" ? "-" : "+";
    $(this).text(newText);
    $(".data").slideToggle();
 });
    </script>
    </body>
</html>