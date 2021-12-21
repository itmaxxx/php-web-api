<?php

@include_once "./httpException.php";

class Request {
  private $contentType, $method, $url, $resource;

  public function __construct($req) {
    $this->parseRequest($req);
  }

  public function getRequest() {
    return array(
      "content-type" => $this->contentType,
      "method" => $this->method,
      "url" => $this->url,
      "resource" => $this->resource
    );
  }

  private function parseRequest($req) {
    $this->method = $req['REQUEST_METHOD'];
    # Request url with query params
    $this->url = $req['REQUEST_URI'];
    
    # Request url without query params
    # When we have query params in URL they won't be shown here
    $this->resource = '/';
    if (isset($req['REDIRECT_URL'])) {
      $this->resource = $req['REDIRECT_URL'];
    }

    if (isset($req['CONTENT_TYPE'])) {
      $this->contentType = strtolower(trim($req['CONTENT_TYPE']));
    }
  }

  public function parseBody() {
    # Parsed body
    $body = array();
    # Parsed json from body
    $data = array();

    if ($this->reqContentType == 'application/json') {
      $body = file_get_contents("php://input");
      $data = json_decode($body, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        httpException("Error parsing json", 400)['end']();
      }
    } else if ($reqContentType == 'application/x-www-form-urlencoded') {
      httpException("Form content type not supported yet", 400)['end']();
    } else {
      httpException("Unsupported Content-Type", 400)['end']();
    }

    return array("body" => $body, "data" => $data);
  }
}
