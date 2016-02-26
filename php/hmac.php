<?php
/**
 * PHP example for creating HMAC Hashes
 */

// This is your API-Key. Treat it confidentially!
$apiKey = 'yourApiKey';

// This is the API endpoint for the POST request
$apiEndpoint = '/clients/yourClientId/contracts';

// This is the example JSON data you want to send in your POST request
$newContractData = '{
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
}';

/**
 * @param $jsonString  string   JSON string representing the data you want to send to the endpoint
 * @param $endpoint    string   The endpoint you want to contact without trailing slash, i.e. "/clients/yourClientId/contracts"
 * @param $secretKey   string   Your API-Secret. Treat confidentially!
 * @return string               Returns a Base64 encoded hash
 */
function getHMAC($jsonString, $endpoint, $secretKey)
{
    function flatten($obj, $path = "")
    {
        $aggregate = array();
        if (is_object($obj) || is_array($obj)) {

            foreach ($obj as $key => $value) {
                $aggregate = array_merge($aggregate, flatten($value, $path ? $path . "." . $key : $key));
            }
        } else {
            if (is_bool($obj)) {
                $obj = $obj ? 'true' : 'false';
            }
            //var_dump($path);
            $aggregate[$path] = $obj;
        }
        return $aggregate;
    }

    function createString($array)
    {
        $result = "";
        foreach ($array as $key => $value) {
            $result .= $value;
        }
        return $result;
    }

    function appendApiKey($payload, $endpoint)
    {
        return $payload . $endpoint;
    }

    $array = flatten(json_decode($jsonString));
    ksort($array);
    $string = createString($array);
    $string = appendApiKey($string, $endpoint);
    return base64_encode(hash_hmac('sha512', $string, $secretKey, true));
}

// `hmac` is the value you have to set for the POST's Authorization Header
$hash = getHMAC($newContractData, $apiEndpoint, $apiKey);
// The output for the above example should be '7rn6yj2rSl3ET3oIqGDQ9jOzULPvKr7rOTat7pBWOpy8mvRSQn55NgyD2QN1xNdGLXX94Tqobb3q2mTdXPS3YQ=='
echo($hash);
