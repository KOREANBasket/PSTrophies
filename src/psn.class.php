<?php declare(strict_types=1);

/**
 * PlayStation Network API Wrapper
 *
 * PHP Version 7.2
 *
 * @category  API
 * @package   PSNTrophies
 * @author    Nhu-Hoai Robert VO <nhuhoai.vo@nhuvo.ch>
 * @copyright 2019 Nhu-Hoai Robert VO
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @version   GIT: 0.2.2
 * @link      https://www.nhuvo.ch/
 * @since     0.2.0
 */

namespace NhuVo\PSNTrophies;

/**
 * PlayStation Network API Wrapper
 *
 * PHP Version 7.2
 *
 * @category  API
 * @package   PSNTrophies
 * @author    Nhu-Hoai Robert VO <nhuhoai.vo@nhuvo.ch>
 * @copyright 2019 Nhu-Hoai Robert VO
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @release   GIT: 0.2.2
 * @link      https://www.nhuvo.ch/
 * @since     0.2.0
 */
class PSN
{
  /// Official PSN API SSO url
  protected const URL_SSO = "https://auth.api.sonyentertainmentnetwork.com/2.0/ssocookie";

  /// Official PSN API Code url
  protected const URL_CODE = "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/authorize";

  /// Official PSN API OAuth url
  protected const URL_OAUTH = "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token";

  /// Client ID from Tustin API
  protected const CLIENT_ID = "b7cbf451-6bb6-4a5a-8913-71e61f462787";

  /// Client secret from Tustin API
  protected const CLIENT_SECRET = "zsISsjmCx85zgCJg";

  /// State??? from Tustin API
  protected const STATE = "06d7AuZpOmJAwYYOWmVU63OMY";

  /// DUID (device uid) from Tustin API
  protected const DUID = "0000000d000400808F4B3AA3301B4945B2E3636E38C0DDFC";

  /// App context
  protected const APP_CONTEXT = "inapp_ios";

  /// Scope from Tustin API
  protected const SCOPE = "capone:report_submission,psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes";

  /// Proxy server
  protected static $proxy = "";

  /// Proxy server port
  protected static $proxyPort = 0;

  /**
   * Send a GET request with curl
   *
   * @param string $url     Endpoint
   * @param array  $params  Params
   * @param array  $opts    Customize your curl options
   * @param array  $headers Customize your HTTP header
   *
   * @return string Return result from curl
   */
  protected static function get(string $url, array $params = [], array $opts = [], array $headers = []) : string
  {
    $data = http_build_query($params);
    $url .= "?{$data}";

    $opts[CURLOPT_URL] = $url;
    $opts[CURLOPT_CONNECTTIMEOUT] = 5;
    $opts[CURLOPT_TIMEOUT] =5;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_SSL_VERIFYHOST] = false;
    $opts[CURLOPT_SSL_VERIFYPEER] = false;
    $opts[CURLOPT_ENCODING] = "";
    $opts[CURLOPT_HTTPHEADER] = $headers;

    if (PSN::$proxy != "" && PSN::$proxyProt > 0) {
      $opts[CURLOPT_PROXY] = PSN::$proxy;
      $opts[CURLOPT_PROXYPORT] = PSN::$proxyPort;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }

  /**
   * Send a POST request with curl
   *
   * @param string $url     Endpoint
   * @param array  $params  Params
   * @param array  $opts    Customize your curl options
   * @param array  $headers Customize your HTTP header
   *
   * @return string Return result from curl
   */
  protected static function post(string $url, array $params = [], array $opts = [], array $headers = []) : string
  {
    $data = http_build_query($params);

    $opts[CURLOPT_URL] = $url;
    $opts[CURLOPT_CONNECTTIMEOUT] = 5;
    $opts[CURLOPT_TIMEOUT] =5;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_POST] = true;
    $opts[CURLOPT_POSTFIELDS] = $data;
    $opts[CURLOPT_SSL_VERIFYHOST] = false;
    $opts[CURLOPT_SSL_VERIFYPEER] = false;
    $opts[CURLOPT_ENCODING] = "";
    $opts[CURLOPT_HTTPHEADER] = $headers;

    if (static::$proxy != "" && static::$proxyPort > 0) {
      $opts[CURLOPT_PROXY] = static::$proxy;
      $opts[CURLOPT_PROXYPORT] = static::$proxyPort;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }

  /**
   * Remove proxy config
   *
   * @return void
   */
  public static function removeProxy() : void
  {
    static::$proxy = "";
    static::$proxyPort = 0;
  }

  /**
   * Set a proxy server config
   *
   * @param string $server Proxy server address
   * @param int    $port   Proxy port server
   *
   * @return void
   */
  public static function setProxy(string $server, int $port) : void
  {
    static::$proxy = $server;
    static::$proxyPort = $port;
  }

  /**
   * Get NPSSO from two factor authentication
   * Be sure you have enabled the two factor authentication in your PSN account.
   * Go to https://www.bungie.net/en/User/SignIn/Psnid?code=000000 and sign in.
   * DO NOT validate the two factor authentication step! Get the ticket_uuid in the URL
   * and the mobile message code
   *
   * @param string $ticket_uuid You can found it in the URL after sign into the following URL
   *                            https://www.bungie.net/en/User/SignIn/Psnid?code=000000
   * @param string $code        This is the mobile message code
   *
   * @return string
   */
  public static function getNPSSO(string $ticket_uuid, string $code) : string
  {
    $res = "";

    $params = [
      "authentication_type" => "two_step",
      "ticket_uuid" => $ticket_uuid,
      "code" => $code,
      "client_id" => static::CLIENT_ID
    ];

    $opts = [];

    $headers = [
      "Content-Type: application/x-www-form-urlencoded"
    ];

    $result = static::post(static::URL_SSO, $params, $opts, $headers);

    $json = json_decode($result, true);
    if (!is_null($json) && is_array($json) && array_key_exists("npsso", $json)) {
      $res = $json["npsso"];
    }

    return $res;
  }

  /**
   * Get grant code from NPSSO
   *
   * @param string $npsso NPSSO token
   *
   * @return string
   */
  public static function getGrantCode(string $npsso) : string
  {
    $res = "";

    $params = [
      "state" => static::STATE,
      "duid" => static::DUID,
      "app_context" => static::APP_CONTEXT,
      "client_id" => static::CLIENT_ID,
      "scope" => static::SCOPE,
      "response_type" => "code"
    ];

    $opts = [
      CURLOPT_HEADER => true
    ];

    $headers = [
      "Cookie: npsso={$npsso}"
    ];

    $result = static::get(static::URL_CODE, $params, $opts, $headers);

    $result = explode("\n", $result);
    foreach ($result as $row) {
      $row = explode(":", $row);
      if (count($row) == 2) {
        $field = trim($row[0]);
        $value = trim($row[1]);
        if ($field == "X-NP-GRANT-CODE") {
          $res = $value;
          break;
        }
      }
    }

    return $res;
  }

  /**
   * Get full set tokens
   *
   * @param string $grantCode Get grant code from getGrantCode method
   *
   * @return string
   */
  public static function getOAuth(string $grantCode) : string
  {
    $res = [];

    $params = [
      "app_context" => static::APP_CONTEXT,
      "client_id" => static::CLIENT_ID,
      "client_secret" => static::CLIENT_SECRET,
      "code" => $grantCode,
      "duid" => static::DUID,
      "grant_type" => "authorization_code",
      "scope" => static::SCOPE
    ];

    $opts = [];

    $headers = [
      "Content-Type: application/x-www-form-urlencoded"
    ];

    $result = static::post(static::URL_OAUTH, $params, $opts, $headers);

    $json = json_decode($result, true);
    if (!is_null($json) && is_array($json) && array_key_exists("refresh_token", $json)) {
      $res = $json["refresh_token"];
    }

    return $res;
  }

  /**
   * Renew access token
   *
   * @param string $refreshToken Get it from getOAuth method, please store renew token somewhere
   *
   * @return array Return acces token, refresh token and exiration duration
   */
  public static function renewAccessToken(string $refreshToken) : array
  {
    $res = [];

    $params = [
      "app_context" => static::APP_CONTEXT,
      "client_id" => static::CLIENT_ID,
      "client_secret" => static::CLIENT_SECRET,
      "refresh_token" => $refreshToken,
      "duid" => static::DUID,
      "grant_type" => "refresh_token",
      "scope" => static::SCOPE
    ];

    $opts = [];

    $headers = [
      "Content-Type: application/x-www-form-urlencoded"
    ];

    $result = static::post(static::URL_OAUTH, $params, $opts, $headers);

    $json = json_decode($result, true);
    if (!is_null($json) && is_array($json) && array_key_exists("refresh_token", $json) && array_key_exists("access_token", $json) && array_key_exists("token_type", $json) && $json["token_type"] == "bearer") {
      $res = $json;
    }

    return $res;
  }
  /**
   * All-in-one method from npsso request to oauth
   *
   * @param string $ticket_uuid You can found it in the URL after sign into the following URL
   *                            https://www.bungie.net/en/User/SignIn/Psnid?code=000000
   * @param string $code        This is the mobile message code
   *
   * @return string
   */
  public static function getRefreshToken(string $ticket_uuid, string $code) : string
  {
    $res = "";

    if (($data = static::getNPSSO($ticket_uuid, $code)) != "") {
      if (($data = static::getGrantCode($data)) != "") {
        $res = static::getOAuth($data);
      }
    }

    return $res;
  }

  /**
   * No constructor, no singleton
   */
  protected function __construct()
  {
  }
}
