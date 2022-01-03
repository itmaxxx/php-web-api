<h1>Users e2e</h1>
<pre>
<?php

class TestException extends Exception
{
  protected $exceptionMessage;

  public function __construct($message, $exceptionMessage, $code = 0, Throwable $previous = null) {
    $this->exceptionMessage = $exceptionMessage;
    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    return __CLASS__ . ": [{$this->exceptionMessage}]: {$this->message}\n";
  }

  public function getExceptionMessage() {
    return $this->exceptionMessage;
  }
}

function describe($description, $func) {
  try {
    echo "describe() $description</br>";
    $func();
  } catch (TestException $ex) {
    echo "[FAIL] describe() $description -> " . $ex->getMessage() . "  at " . $ex->getExceptionMessage() . "</br>";
  }
}

function it($description, $func) {
  try {
    echo "it() $description</br>";
    $func();
  } catch (Exception $ex) {
    throw new TestException(
      "it() $description failed" . "</br>", 
      $ex->getMessage() . " (file: " . $ex->getFile() . ", line " . $ex->getLine() . ")<br/>");
  }
}

function assertStrict($v1, $v2) {
  if ($v1 === $v2) {
    return true;
  } else {
    throw new Exception("assertStrict() $v1 (" . gettype($v1) . ") !== $v2 (" . gettype($v2) . ")");
  }
}

function assertNotStrict($v1, $v2) {
  if ($v1 == $v2) {
    return true;
  } else {
    throw new Exception("assertNotStrict() $v1 != $v2");
  }
}

function assertObject($obj1, $obj2) {
  if (json_encode($obj1) === json_encode($obj2)) {
    return true;
  } else {
    throw new Exception("assertObject() " . json_encode($obj1) . " !== " . json_encode($obj2));
  }
}

function request($url) {
  $req = curl_init();

  curl_setopt($req, CURLOPT_URL, $url);
  curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);

  $response = curl_exec($req);
  $responseInfo = curl_getinfo($req);

  curl_close($req);

  return ["data" => $response, "info" => $responseInfo];
}

describe("[GET] /api/users", function() {
  it("should get users list", function() {
    global $usersFixtures;

    $response = request("http://localhost/api/users");
    $json = json_decode($response['data']);

    var_dump($json->data->users);
    var_dump($usersFixtures);

    assertStrict($response['info']['http_code'], 200);
    assertObject($json->data->users, $usersFixtures);

    assertStrict(123, 123);
    assertNotStrict("123", 123);
  });
});

?>
</pre>
