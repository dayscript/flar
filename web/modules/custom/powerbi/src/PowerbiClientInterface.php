<?php

namespace Drupal\powerbi;

/**
 * Defines PowerbiClientInterface interface.
 */
interface PowerbiClientInterface {

  /**
   * Utilizes Drupal's httpClient to connect to powerbi.
   *
   * @param string $method
   *   get, post, patch, delete, etc. See Guzzle documentation.
   * @param string $endpoint
   *   The Powerbi authentication API endpoint.
   * @param array $query
   *   Query string parameters the endpoint allows (ex. ['api-version' => 'x'].
   * @param array $body
   *   (converted to JSON)
   *   Utilized for some endpoints.
   *
   * @return object
   *   \GuzzleHttp\Psr7\Response body
   */
  public function connect($method, $endpoint, array $query, array $body);

}
