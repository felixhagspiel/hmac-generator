/**
 * Node example for creating HMAC Hashes
 */

var flatten = require('flat');
var CryptoJS = require("crypto-js");

// Example POST data
var json = {
  "customerId": "0012",
  "seller": {
    "userId": 1006,
    "firstName": "Mira",
    "surName": "Bellenbaum",
    "email": "mira.bellenbaum@gmail.com"
  },
  "buyer": {
    "userId": 2006,
    "firstName": "Gerd",
    "surName": "Nehr",
    "email": "gerd.nehr@gmail.com"
  },
  "description": "Fahrrad Typ ABC",
  "repeatable": false,
  "callbackUrl": "www.blub.de",
  "status": "offen"
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

// `hmac` contains the value you have to set as value for the POST's Authorization Header
var hmac = getHMAC(json, '/clients/yourClientId/contracts', 'yourApiKey');
