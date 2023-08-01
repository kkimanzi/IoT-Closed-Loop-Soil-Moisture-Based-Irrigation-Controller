import requests
url = "http://127.0.0.1/irrigation/web_service/authenticate.php"
payload = dict(clientNumber="+254791260340", serverNumber="+254791880290")
res = requests.post(url, data=payload)

print(res.text)