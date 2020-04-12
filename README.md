#Arduino Indoor Outdoor Temperatures

Using an Arduino Uno Wifi to collect indoor temperatures and the Open Weather Map API to collect outdoor temperatures.

## Installing

If you want to recreate this yourself there are a few steps to take.

### Create the database

Create a database and then use the data.sql file to create the data table that will store the recordings.

### Arduino

Use an Arduino Uno Wifi, you could use other Wifi methods with a different Arduino board, wire up a TMP36 sensor. There are plenty of tutorials on the internet, no need for me to reproduce those steps. Modify the settings in the sketch and upload it to the Arduino.

### Server

All the server files are in the www folder. You will need to change all of the settings in the settings.inc file. These include database connection, Open Weather Map API key and city id and a basic security key.

For the Open Weather Map API key you will need to [create an account](https://openweathermap.org/appid) and then register an API key as well as [finding your city id](https://openweathermap.org/find).

Once all this is done power up the Arduino and the data should start appearing in your database.

Once there is sufficient data you can use the index.php file to view a graph of the two temperatures.

## Improving

There are a couple of things I’d like to do to improve this.

1. Security. To allow the data to be sent to a public facing server some more security steps than a basic key should be used.
2. Allow different timeframes on the graph, e.g. past X hours, days or weeks. For this I’d need to group the data as showing data for every 5 minutes of an hour over 7 days would clutter a graph.
