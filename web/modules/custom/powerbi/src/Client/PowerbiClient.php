<?php

namespace Drupal\powerbi\Client;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\powerbi\PowerbiClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Defines PowerbiClient class.
 */
class PowerbiClient implements PowerbiClientInterface {

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client, LoggerChannelFactory $loggerFactory) {
    $this->httpClient = $http_client;
    $this->loggerFactory = $loggerFactory->get('powerbi');
  }

  /**
   * {@inheritdoc}
   */
  public function connect($method, $endpoint, $query, $body) {
    try {
      $response = $this->httpClient->{$method}(
        $endpoint,
        $this->buildOptions($query, $body)
      );
    }
    catch (RequestException $exception) {
      // Log message if there is error.
      $this->loggerFactory->error('Failed to complete task "@error"', [
        '@error' => $exception->getMessage(),
      ]);
      return FALSE;
    }

    $headers = $response->getHeaders();
    $this->throttle($headers);
    return $response->getBody()->getContents();
  }

  /**
   * Build options for the client.
   */
  private function buildOptions($query, $body) {
    $options = [];
    if ($body) {
      $options['form_params'] = $body;
    }
    if ($query) {
      $options['headers'] = [
        'Authorization' => 'Bearer ' . $query,
        'Cache-Control' => 'no-cache',
      ];
    }
    return $options;
  }

  /**
   * Throttle response.
   *
   * 100 per 60s allowed.
   */
  private function throttle($headers) {
    /*If ($headers['API-Request-Rate-Count'][0] > 99) {
    return sleep(60);
    }.*/
    return TRUE;
  }

}
