<?php

namespace Drupal\powerbi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\powerbi\Client\PowerbiClient;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PowerbiController.
 *
 * @package Drupal\powerbi\Controller
 */
class PowerbiController extends ControllerBase {

  /**
   * The PowerbiClient API.
   *
   * @var PowerbiClient
   */
  protected $powerbiApiClient;

  /**
   * The config factory object.
   *
   * @var ConfigFactory
   */
  protected $config;

  /**
   * Powerbi username.
   *
   * @var string
   */
  protected $username;

  /**
   * The powerbi password.
   *
   * @var string
   */
  protected $password;

  /**
   * The powerbi $clientId.
   *
   * @var string
   */
  protected $clientId;

  /**
   * The powerbi $resource.
   *
   * @var string
   */
  protected $resource;

  /**
   * The powerbi $groupId.
   *
   * @var string
   */
  protected $groupId;

  /**
   * The powerbi $datasetId.
   *
   * @var string
   */
  protected $datasetId;

  /**
   * The powerbi $reportId.
   *
   * @var string
   */
  protected $reportId;

  /**
   * PowerbiController constructor.
   *
   * @param PowerbiClient $powerbi_api_client
   *   The powerbi api client.
   * @param ConfigFactory $config_factory
   *   The config factory object.
   */
  public function __construct(PowerbiClient $powerbi_api_client, ConfigFactory $config_factory) {
    $this->powerbiApiClient = $powerbi_api_client;
    $config = $config_factory->get('powerbi.settings');

    $this->username = $config->get('powerbi_username');
    $this->password = $config->get('powerbi_password');
    $this->clientId = $config->get('powerbi_client_id');
    $this->groupId = $config->get('powerbi_group_id');
    $this->datasetId = $config->get('powerbi_dataset_id');
    $this->reportId = $config->get('powerbi_report_id');
    $this->resource = 'https://analysis.windows.net/powerbi/api';
  }

  /**
   * Service container to create service instance.
   *
   * @param ContainerInterface $container
   *   The service container.
   *
   * @return ControllerBase|static
   *   Services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('powerbi.client'),
      $container->get('config.factory')
    );
  }

  /**
   * Display the powerbi dashboard.
   *
   * @param mixed $component
   *   Component to embed.
   *
   * @return array|mixed
   *   The data to be embedded on the page.
   */
  public function embedComponent($component) {

    $token = $this->getAuthToken();//isset($_COOKIE["access_token"]) ? $_COOKIE["access_token"] 
    $endpoint = 'https://api.powerbi.com/v1.0/myorg/groups/' . $this->groupId . '/reports/' . $this->reportId;

    $request = $this->powerbiApiClient->connect('get', $endpoint, $token, []);
    $results = json_decode($request, TRUE);
    $embedUrl = $results['embedUrl'];
    $embedToken = $this->getEmbedToken($endpoint, $token);

    $data = [
      'powerBI' => TRUE,
      'reportId' => $this->reportId,
      'groupId' => $this->groupId,
      //'accessToken' => $token,
      'embedToken' => $embedToken,
      'embedUrl' => $embedUrl
    ];
    return [
      '#theme' => 'get-pbi-data',
      '#data' => $data,
      // Attached library.
      '#attached' => [
        'library' => [
          'powerbi/powerbi',
          'powerbi/powerbi-client',
        ],
        'drupalSettings' => $data,
      ],
    ];
  }

  /**
   * Get authorization token.
   *
   * @param mixed $component
   *   Component like page, report etc.
   *
   * @return mixed
   *   Component.
   */
  public function getTitle($component) {
    return $component;
  }

  /**
   * Get authorization token.
   *
   * @return mixed|null
   *   Authorization token.
   */
  public function getAuthToken() {

    /*if (isset($_COOKIE["access_token"])) {
      return $_COOKIE["access_token"];
    }*/

    $body = [
      'grant_type' => 'password',
      'scope' => 'openid',
      'resource' => $this->resource,
      'client_id' => $this->clientId,
      'username' => $this->username,
      'password' => $this->password,
    ];
    $endpoint = 'https://login.windows.net/common/oauth2/token';
    $request = $this->powerbiApiClient->connect('post', $endpoint, NULL, $body);
    $results = json_decode($request, TRUE);
    if (!empty($results)) {
      //setcookie("access_token", $results['access_token'], $results['expires_on']);
      return $results['access_token'];
    }
    return NULL;
  }

  /**
   * Get embed token.
   *
   * @return mixed|null
   *   Embed token.
   */
  public function getEmbedToken($endpoint, $token) {
    $endpoint = $endpoint . "/GenerateToken";
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS =>"{'accessLevel': 'View','datasetId':'".$this->datasetId."'}",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $token",
        "Content-Type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $results = json_decode($response, TRUE);
    $embedToken = $results['token'];
    
    curl_close($curl);
    return $embedToken;
  }
}
