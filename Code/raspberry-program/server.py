import sys
sys.path.append('/home/pi/Irrigation')
import time
import requests
from gsm_module.sim800l import SIM800L
import RPi.GPIO as GPIO

import config


GPIO.setmode(GPIO.BCM)
mySerialDevice = None
        

def initializeSim():
    global mySerialDevice
    mySerialDevice=SIM800L('/dev/serial0')
    mySerialDevice.setup()
    mySerialDevice.read_and_delete_all()
    mySerialDevice.callback_msg(respond_to_incoming_sms)
    

def respond_to_incoming_sms(msg_id):
    global mySerialDevice
    sms=mySerialDevice.read_sms(msg_id)
    
    body = sms[3].lower()
    if ("initialize" in body):
        print "Received initialization command"
        validateClient(sms[0])
    elif ("log" in body):
        print "Received log command"
        logClientData(sms[0],sms[3])
    else:
        print "Received bad command"
    

def checkNewMessage() :
    global mySerialDevice
    mySerialDevice.check_incoming()


def sendMessage(phone_number, message):
        mySerialDevice.send_sms(phone_number, message)
        

def validateClient(phone_number):
    
    url = "http://irrigation-project.orgfree.com/web_service/authenticate.php"
    payload = dict(clientNumber=phone_number, serverNumber=config.serverNumber)
    res = requests.post(url, data=payload)
    
    results = res.text;
    if "Warning" in results:
        print("Bad params received : "+results)
    else:
        print("Good params received : "+results)
        sendMessage(phone_number, results)

def logClientData(phone_number, message):
    moistureVal = message[message.index("M=")+2:message.index(";")]
    batteryVal = message[message.index("B=")+2:]
    
    
    url = "http://irrigation-project.orgfree.com/web_service/post_log.php"
    payload = dict(clientNumber=phone_number, serverNumber=config.serverNumber, moisture=moistureVal, battery=batteryVal)
    res = requests.post(url, data=payload)
    
    results = res.text;
    if "Warning" in results:
        print("Data logging failed : "+results)
    else:
        print("Data logging successful")
        sendMessage(phone_number, results)
    

def main():
    
    initializeSim();

    while True:
        checkNewMessage();
        time.sleep(4);
    
if __name__ == '__main__':
    main()


