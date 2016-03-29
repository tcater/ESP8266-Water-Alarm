#include <ESP8266WiFi.h>
#include <WiFiClient.h>

// ----------------wifi values--------------------------
const char* ssid = "**YOUR SSID HERE**";
const char* password = "**YOUR PASSWORD HERE**";

// ----------------pin values--------------------------
//const int shutdownPin = 14; // defines the hold pin (set High to trip the MOSFET and power down).
const int lowBat = 13; //define low battery circuit

// ----------------iot values--------------------------
String iotID = "WaterAlarm";
const char* iotNotify = "0";

// ----------------host values--------------------------
const char *host = "**Your server side host**";

String iotMSG = "The+water+detector+has+activated.";

int sleepMinutes = 1; //minutes

void goWifi(){
  WiFi.begin ( ssid, password );
  Serial.println ( "" );       
  // Wait for connection
  int i=0;
  while ( WiFi.status() != WL_CONNECTED ) {
    delay ( 500 );
    Serial.print ( "." );
    i++;
    if(i>60){return;}
  }
  Serial.println ( "" );
  Serial.print ( "Connected to " );
  Serial.println ( ssid );
  Serial.print ( "IP address: " );
  Serial.println ( WiFi.localIP() );
}

void report(String sndMSG ){
  Serial.print("connecting to ");
  Serial.println(host);

  // Use WiFiClient class to create TCP connections
  WiFiClient client;
  const int httpPort = 80;
  while (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    goWifi(); //if the connection fails assume it's because the module has lost it's connection and reestablish the connection
  }
  
  // We now create a URI for the request
    String url = "/iot/server.php?iotID=" + iotID + "&iotMSG=" + sndMSG + "&iotNotify=" + iotNotify  ;
    
  Serial.print("Requesting URL: ");
  Serial.println(url);
  
  // This will send the request to the server
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
  "Host: " + host + "\r\n" + 
  "Connection: close\r\n\r\n");
  delay(10);
  
  // Read all the lines of the reply from server and print them to Serial
  while(client.available()){
  String line = client.readStringUntil('\r');
  Serial.print(line);
  }
  
  Serial.println();
  Serial.println("closing connection");  
}

void setup () {
  pinMode(lowBat, INPUT);
  Serial.begin ( 74880 );
  goWifi();
  Serial.print("->");
  if(digitalRead(lowBat)==1){
    Serial.print("Low Battery!");
    iotMSG = "Low+Battery";
  }
  Serial.println("<-"); 
  report ( iotMSG );
  ESP.deepSleep(sleepMinutes*60000000, WAKE_RF_DEFAULT); 
}

void loop () {
}


