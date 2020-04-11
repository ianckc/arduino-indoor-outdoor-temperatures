<?php

require('../vendor/autoload.php');

use GuzzleHttp\Client;

// Database connection settings.
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// Open Weather Map settings.
define('CITY_ID', '');
define('API_KEY', '');

// Basic security check key.
define('BASIC_KEY', '');

// Basic security check.
if (!isset($_GET['key']) || $_GET['key'] != BASIC_KEY) {

  // Try to connect to the database.
  try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }

  // Get the outdoor temperature from Open Weather Map API.
  $client = new GuzzleHttp\Client(['base_uri' => 'api.openweathermap.org/']);

  $params = [
    'query' => [
      'id' => CITY_ID,
      'appid' => API_KEY,
      'units' => 'metric',
    ],
  ];

  $request = $client->request('GET', 'data/2.5/weather', $params);

  $response = json_decode((string)$request->getBody());

  $outdoorTemperature = $response->main->temp;

  // Add the data to the database table.
  $data = [
    'indoor_temperature' => number_format((float)$_GET['temp'], 2),
    'outdoor_temperature' => $outdoorTemperature,
  ];

  $sql = "INSERT INTO data (indoor_temperature, outdoor_temperature) VALUES (:indoor_temperature, :outdoor_temperature)";

  $stmt= $db->prepare($sql);

  $stmt->execute($data);

  // Return a 201 created response.
  header('HTTP/1.1 201 Unauthorized');
  exit;

} else {
  // Return a 401 Unauthorized response.
  header('HTTP/1.1 401 Unauthorized');
  exit;
}
