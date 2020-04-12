/**
 * Arduino Indoor Outdoor Temperatures
 * 
 * Sends the TMP36 data to a webserver that in turn calls the Open Weather Map API to get an outdoor temperature.
 */

#include <UnoWiFiDevEd.h>

#define CONNECTOR "rest"
#define SERVER_ADDRESS "YOUR_SERVER_ADDRESS"
#define METHOD "GET"
#define KEY "YOUR_KEY"

int temperaturePin = 0;

void setup() {
  Ciao.begin();
}

void loop() {

  // Calculate 5 minutes between recordings and set last sample time to zero so there is a recording on the first run of the loop.
  const unsigned long fiveMinutes = 5 * 60 * 1000UL;
  static unsigned long lastSampleTime = 0 - fiveMinutes;
  
  unsigned long now = millis();
  if (now - lastSampleTime >= fiveMinutes)
  {
    lastSampleTime += fiveMinutes;
    

    // Getting the voltage temperature reading from the sensor.
    int temperatureReading = analogRead(temperaturePin);
  
    // Converting that temperature reading to voltage, for 3.3v arduino use 3.3.
    float voltage = temperatureReading * 5.0;
    voltage /= 1024.0;
  
    // Temperature in degrees celsius.
    float temperatureC = (voltage - 0.5) * 100;  // Converting from 10 mv per degree wit 500 mV offset to degrees ((voltage - 500mV) times 100).
  
    // Build the GET request URI
    String uri = "/data.php?key=";
    uri += String(KEY);
    uri += "&temp=";
    uri += String(temperatureC);

    // Send the data to the webserver.
    CiaoData data = Ciao.write(CONNECTOR, SERVER_ADDRESS, uri);

  }
  
}
