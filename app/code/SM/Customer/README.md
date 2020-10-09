# SmartOSC Customer

## Changelog

* 1.0.0 - Initial
* 1.1.0 - Add verify email feature

## API

### POST /rest/V1/transcustomer/

- Use for register customer (both of regular and social profile)
- Sample request for regular:
```json
{ 
   "customer":{ 
      "email":"etona+11@smartosc.com",
      "firstname":"mili",
      "lastname":"doto",
      "custom_attributes":[ 
         { 
            "attribute_code":"telephone",
            "value":"08886349911"
         }
      ]
   },
   "passwordHash": "fa7312537e1a964cd0cdee2541dc3bf3"
}
```
- Sample request for socila profile:
```json
{ 
   "customer":{ 
      "email":"etona+11@smartosc.com",
      "firstname":"mili",
      "lastname":"doto",
      "custom_attributes":[ 
         { 
            "attribute_code":"telephone",
            "value":"08886349911"
         }
      ],
      "extension_attributes": {
      	"login_type": "Google",
      	"identifier": "aaaaaaaaaaa+11"
      }
   }
}
```

```json
{ 
   "customer":{ 
      "email":"etona+12@smartosc.com",
      "firstname":"mili",
      "lastname":"doto",
      "custom_attributes":[ 
         { 
            "attribute_code":"telephone",
            "value":"08886349912"
         }
      ],
      "extension_attributes": {
      	"login_type": "Facebook",
      	"identifier": "aaaaaaaaaaa+12"
      }
   }
}
```
### POST /rest/V1/transcustomer/social/token
```json
{
	"identifier": "aaaaaaaaaaa+11",
	"type": "Google"
}
```
