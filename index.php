<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Place favicon.ico and apple-touch-icon(s) in the root directory -->
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <?php
/*wunderground API*/
$weather = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/conditions/q/CA/San_Francisco.xml");
$astronomy = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/astronomy/q/CA/San_Francisco.xml");
$hourly = simplexml_load_file("http://api.wunderground.com/api/87df596a569c0e60/hourly/q/CA/San_Francisco.xml");



//Testing files
//$weather = simplexml_load_file("xml/conditions.xml"); /* gets current observation */
//$astronomy = simplexml_load_file("xml/astronomy.xml"); /* gets sunrise and sunset times */
//$hourly = simplexml_load_file("xml/hourly.xml"); /* gets hourly forecast */


$weather_info = $weather->xpath('current_observation'); 
$astronomy_info = $astronomy->xpath('moon_phase');
$realtemp = $weather_info[0]->temp_f ;
$feels = $weather_info[0]->feelslike_f;
$icon;

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
    if($condition == "cloudy" || $condition == "Fog" || $condition == "Partly Cloudy" || $condition == "Scattered Clouds"){
      $hrIcon = "A";
    } else if( $condition == "Rain" || $condition == "Drizzle" || $condition == "Light Drizzle" || $condition == "Heavy Drizzle" || $condition == "Light Rain" || $condition == "Heavy Rain") {
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
} else {
  $icon = "B";
    $bike = "It's okay to bike in";
}
/* warns for bad weather in 8hr forecast */

function checkForecast($checkthis){

global $bike;
   if ($bike == "Yes" && (
    forecast($checkthis) == "Freezing Rain" ||
      forecast($checkthis) == "Rain" ||
      forecast($checkthis) == "Sleet" ||
      forecast($checkthis) == "Snow" ||
      forecast($checkthis) == "Rain Showers") ){
global $icon;
global $why;
$icon = "R";
 $why = forecast($checkthis) . " at " . forecastTime($checkthis);
 $bike = "Maybe, consider the forecast in";
}
  if (forecast($checkthis) == "Chance of Rain" ||
      forecast($checkthis) == "Chance of Freezing Rain" ||
      forecast($checkthis) == "Chance of Sleet" ||
      forecast($checkthis) == "Chance of Snow"){
    global $why;
  global $icon;
  $icon = "T";
    $why = "It's okay to bike, but consider" . forecast($checkthis). " at " . forecastTime($checkthis); 
    }
}

/*run the 8hr forecast bad weather check*/
for ($i=0;$i<9;$i++){
checkForecast($i);
}


/*checks next hourly forecast*/
 if (forecast(0) == "Rain" || forecast(0) == "Light Drizzle" || forecast(0) == "Heavy Drizzle" || forecast(0) == "Drizzle" ||  forecast(0) == "Rain Showers") {
global $bike;
global $icon;
  $icon = "R";
    $bike =  "Don't bike in";
    $why = "It's gonna rain soon!";

}

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
      <h1> <?php echo $bike;?> <em>San Francisco.</em> <span class="arrow">+</span></h1>
  
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
        for ($x=0; $x<10; $x++){
          echo hourlyforecast($x);
        }
        ?>
            </ul>
        </div>
        </div>  
     
    <script>
    $(".arrow").on ("click", function(){
      $(".data").slideToggle();
    });
    </script>
    </body>
</html>
