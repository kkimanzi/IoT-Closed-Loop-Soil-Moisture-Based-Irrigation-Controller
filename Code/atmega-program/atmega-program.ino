
#include <SoftwareSerial.h>
#include <String.h>



/**** FOR GSM******/
#define SIM800_RX_PIN 11     //Pin on which GSM module rx is connected to arduino board
#define SIM800_TX_PIN 10     //Pin on which GSM module tx is connected to arduino board
String notification;
bool notificationPresent = false;
String serverNumber = "+111111111111";

SoftwareSerial serialSIM800(SIM800_TX_PIN,SIM800_RX_PIN);

bool initialized = false;
bool logReceived = false;

/***** FOR SOIL MOISTURE SENSOR ****/
const int airValue = 583;
const int waterValue = 337;

String waitForResponse();
void GSMHandler();

/***** FOR ACTUATOR ******/
#define VALVE_PIN 3
double moistureParam1;
double moistureParam2;

/******   SETUP   ******/
void setup()
{
  pinMode(VALVE_PIN, OUTPUT);

  
  delay(15000);
  
  /******  FOR GSM  ******/
 
  //Being serial communication with Arduino and SIM800
  serialSIM800.begin(9600);
  delay(1000);

  delay(500);
  serialSIM800.println("AT+CMGF=1");  //operate in SMS mode

  delay(500);
  waitForResponse();

  while (!initialized){
    sendGSMMessage("Initialize");
    for (int i = 0; i < 6; i++){
      delay(1000); 
      GSMHandler(); 
      if (initialized){
        // If received initialization params exit loop
        break;  
      }
    }
  }
  
}

/*****Specific incubator functions using core GSM functions ******/
void setToTextMode(){
  delay(500);
  serialSIM800.println("AT+CMGF=1");  //operate in SMS mode
}

void setRecipientNumber(){
  delay(500);
  serialSIM800.println("AT+CMGS=\""+serverNumber+"\"\r");
}

void endTransmission(){
  serialSIM800.write(26);
  serialSIM800.println();
  serialSIM800.flush();
}

//send any random message
void sendGSMMessage(String message){
   setToTextMode();
   setRecipientNumber();
   delay(500);
   serialSIM800.print(message);
   delay(500);
   endTransmission();
}

/*****   SOIL MOISTURE FUNCTION *****/
int readMoisture(){
  
  int soilMoistureValue = analogRead(A0);
  int moisturePercentage = map(soilMoistureValue, airValue, waterValue, 0, 100);

  return moisturePercentage;
}

/**** BATTERY FUNCTION *****/
int readBatteryLevel(){
  int batteryVal = analogRead(A1);
  int batteryPercentage = map(batteryVal, 0, 1023, 0, 100);

  return batteryPercentage;
}


/******  LOOP  *******/
void loop(){
  // Read moisture and battery levels
  int dMoistureVal = readMoisture();
  int iBatteryVal = readBatteryLevel();

    // Send log of data read
  char moistureVal[2];
  sprintf(moistureVal, "%d", dMoistureVal);
  String mVal = moistureVal;
  
  char batteryVal[2];
  sprintf(batteryVal, "%d", iBatteryVal);
  String bVal = batteryVal;

  logReceived = false;
  while (!logReceived){
    String message = "log M="+mVal+";B="+bVal;
    sendGSMMessage(message);
    for (int i = 0; i < 6; i++){
      delay(1000); 
      GSMHandler(); 
      if (logReceived){
        // If received initialization params exit loop
        break;  
      }
    }
    
  }

  // Control valve
  
  for (int i = 0; i < 6; i++){
    // For loop causes the block to be trapped for 30 mins
    // before sending the data log on the next loop.
    
    if (dMoistureVal < moistureParam1){
      // Turn valve on
      digitalWrite(VALVE_PIN, HIGH);  
    }
    if (dMoistureVal >  moistureParam2){
      // Turn valve off
      digitalWrite(VALVE_PIN, LOW);
    }
  // Sleep for 5 minutes
  delay(5*60*1000);
  }

  
}

/******  GSM FUNCTIONS  ******/
String waitForResponse(){
  delay(6000);
   if(serialSIM800.available()){
       notification = serialSIM800.readString();
  }
  else{
      //notification = "None";
      notification = "none";
  }
  
  return notification;
}

String waitForResponse2(){
  //Wait for a newe alert 
  
  while(true){
     if(serialSIM800.available()){
      String notification = "";
      notification = serialSIM800.readString();
      //notification = serialSIM800.readString();
      //Serial.println(notification);
      //return message alert text
      return notification;
     }
  }
}

bool isNewMessage(String notification){
  //check if new alert is for a new message
  //Serial.print(String(strncmp(content.c_str(), "+CMTI",4)));
    char headerCode[] = {'+','C','M','T','I'};
    //removeSpaces(content.c_str());
    bool mismatch = false;
    int comparison;

    //strangely String notification has char ' ' (i.e blank space) at locations [0] and [1].
    //well then, our counter begins at [2]
    for(int a = 2 ; a < 7; a++){
      comparison = ((String)((char)notification[a]) ==  (String)((char)headerCode[a-2]));
      if(comparison != 1){
        mismatch = true;
        break;
      }
    }

    return (!mismatch);
}

int getNewMessageIndex(String notification){
      //Parse new alert notification to get index at which the incoming message that triggered the alert is stored
      //Serial.println(notification.c_str());
      const char s[2] = ",";
      char *token;

      token = strtok(notification.c_str(), s);
      token = strtok(NULL, s);  //this token represents index

      //readMessageAtIndex(atoi(token));
      return atoi(token);
}


String readMessageAtIndex(int index){
  //Read message stored in memory at index
  serialSIM800.print("AT+CMGR=");
  serialSIM800.println((String)index);
    endTransmission();
    String response = waitForResponse2();
    delay(10);  //prevents race condition to 64byte buffer
    
    return response;
}

String getMessageHeader(String message){
    //Tokenize at '\n'
      //Serial.println(notification.c_str());
      const char s[2] = {'\n','\0'};
      //char *messageBody;
      char *messageHeader;
      
      messageHeader = strtok(message.c_str(), s);
      //Serial.println(messageBody);
      messageHeader = strtok(NULL, s);  //this token represents contains header info
      //Serial.println(messageHeader);
      return messageHeader;
}

String getMessageBody(String message){
  //Tokenize at '\n'
      //Serial.println(notification.c_str());
      const char s[2] = {'\n','\0'};
      char *messageBody;
      //char *messageHeader;
      
      messageBody = strtok(message.c_str(), s);
      //Serial.println(messageBody);
      messageBody = strtok(NULL, s);  //this token represents contains header info(
      //Serial.println(messageBody);
      messageBody = strtok(NULL, s);    //only this instance of messageBody is needed
      return messageBody;
}


void GSMHandler(){
    //Directs execution of all GSM related functionalities
    //notificationPresent = false;
    notification = "";
    notification = waitForResponse();
    if(isNewMessage(notification)){
        int index = getNewMessageIndex(notification);
        String rawMessage = readMessageAtIndex(index);
        //rawMessage comprised of both header info(timestamp, sender....) and body(actual message contents)

        String header = getMessageHeader(rawMessage);
        const char * c_header = header.c_str();
        const char * c_serverNumber = serverNumber.c_str();

        // Check if the message originated from the client's server
        if (strstr(c_header, c_serverNumber)){
          String msgBody = getMessageBody(rawMessage);
          msgBody.trim();

          const char * c_body = msgBody.c_str();
          const char* partial1 = "M=";
          const char * partial2 = "E=";

          const char * logSuccess = "Success";
          
          // Check if the message was in response to initialization command
          if(strstr (c_body, partial1) && strstr(c_body, partial2)){
            // If true then mesaage body was in response to initialization command
            initialized = true;

            char *moistureToken = strtok(msgBody.c_str(), ";");
            char *expiryToken = strtok(NULL, ";");
            
            // Spliting moisture
            char *mHeader = strtok(moistureToken, "=");
            char *mBody = strtok(NULL, "=");
            // Getting moisture params
            char *mParam1 = strtok(mBody, "-");
            char *ptr1;
            moistureParam1 = strtod(mParam1, &ptr1);
            
            char *mParam2 = strtok(NULL, "-");
            char *ptr2;
            moistureParam2 = strtod(mParam2, &ptr2);
          
            // Split expiry token
            char *eHeader = strtok(expiryToken, "=");
            char *eParam = strtok(NULL, "=");
            char *ptr3;
            double expiryParam;
            expiryParam = strtod(eParam, &ptr3);
                        
          } else if (strstr (c_body, logSuccess)){
            logReceived = true;
          } else {
          }
        } else {
        }
    }

    delay(2000);         
}

/******  END GSM FUNCTIONS  ******/
