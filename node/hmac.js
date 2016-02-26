/**
 * Node example for creating HMAC Hashes
 */

var flatten = require('flat');
var CryptoJS = require("crypto-js");

// This is your API-Key. Treat it confidentially!
var apiKey = 'yourApiKey';

// This is the API endpoint for the POST request
var apiEndpoint = '/clients/yourClientId/contracts';

// This is the example JSON data you want to send in your POST request
var newContractData = {
  "customId" : "yourCustomContractId" ,
  "description" : "Rent for office in Millerstreet" ,
  "repeatable" : false ,
  "callbackUrl" : "https://yoursite.de/callbacks?orderid=yourOrderId",
  "sender": {
    "customId": "yourCustomSenderId",
    "firstName": "John",
    "surName": "Doe",
    "email": "john@doe.com"
  },
  "recipient": {
    "customId": "yourCustomRecipientId",
    "firstName": "Flash",
    "surName": "Gordon",
    "email": "flash@gordon.com",
    "accounts": [
      {
        "customId": "yourCustomRecipientAccountId",
        "firstName": "Flash",
        "surname": "Gordon",
        "bankName": "Huge Bank AG",
        "iban" : "DE1200012030200359100100",
        "bic" : "COBADEFFXXX"
      }
    ]
  }
};

/**
 * @param json        JSON string representing the data you want to send to the endpoint
 * @param endpoint    The endpoint you want to contact without trailing slash, i.e. "/clients/yourClientId/contracts"
 * @param secretKey   Your API-Secret. Treat confidentially!
 * @return string     Returns a Base64 encoded hash
 */
function getHMAC(json, endpoint, secretKey) {
  // flatten nested JSON
  var flattenedJSON = flatten(json);
  // get attribute names as array
  var keys = Object.keys(flattenedJSON);
  // sort array alphabetically
  keys.sort();
  // concat values as json string
  var jsonString = "";
  for (var i = 0; i < keys.length; i++) {
    jsonString += flattenedJSON[keys[i]];
  }
  // generate HMAC
  var hash = CryptoJS.HmacSHA512(jsonString + endpoint, secretKey);
  // Base64 encode and return
  return CryptoJS.enc.Base64.stringify(hash);

}

// `hmac` is the value you have to set for the POST's Authorization Header
var hmac = getHMAC(newContractData, apiEndpoint, apiKey);
// The output for the above example should be '7rn6yj2rSl3ET3oIqGDQ9jOzULPvKr7rOTat7pBWOpy8mvRSQn55NgyD2QN1xNdGLXX94Tqobb3q2mTdXPS3YQ=='
console.log(hmac);
