<?php
/**
 * PHP example for creating HMAC Hashes
 */

// Example POST data
$jsonString = '{
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
}';

/**
 * @param $JSONString   JSON string representing the data you want to send to the endpoint
 * @param $endpoint     The endpoint you want to contact without trailing slash, i.e. "/clients/yourClientId/contracts"
 * @param $secretKey    Your API-Secret. Treat confidentially!
 * @return string       Returns a Base64 encoded hash
 */
function getHMAC($JSONString, $endpoint, $secretKey)
{
    function flatten($obj, $key = null, $path = "")
    {
        $aggregate = array();
        if (is_object($obj)) {

            foreach ($obj as $key => $value) {
                $aggregate = array_merge($aggregate, flatten($value, $key, $path ? $path . "." . $key : $key));
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

    function appendApiKey($payload, $path)
    {
        return $payload . $path;
    }

    $array = flatten(json_decode($JSONString));
    ksort($array);
    $request = appendApiKey(createString($array), $endpoint);
    return base64_encode(hash_hmac('sha512', $request, $secretKey, true));
};

// get a new HMAC hash
echo(getHMAC($jsonString, "/clients/yourClientId/contracts", "yourApiKey"));