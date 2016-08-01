<?php
namespace strong2much\vk;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\HttpException;

/**
 * This is basic class for vkontakte api.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class Api extends Component
{
    const API_BASE_URL = 'https://api.vk.com/method/';

    /**
     * @var string API version
     */
    public $version = '5.5';

    /**
     * @var integer Default value for count limit
     */
    public $defaultCount = 100;

    /**
     * Return array of countries in json format.
     * For version 5.5, json format is {count, items=>[id,title]}.
     * See documentation (@link http://vk.com/dev/database.getCountries)
     * @param array $params additional params: code, offset, count,
     * @return array response
     */
    public function getCountries($params=[])
    {
        $params['need_all'] = 1;
        if(!isset($params['count'])) {
            $params['count'] = $this->defaultCount;
        }

        $result = $this->call('database.getCountries', $params);
        return $result;
    }

    /**
     * Return array of cities in json format.
     * For version 5.5, json format is {count, items=>[id,title,(important,region,area)]}.
     * See documentation (@link http://vk.com/dev.php?method=database.getCities)
     * @param integer $countryId id of the country in vk db. For example, 1 - Russia
     * @param string $q query string
     * @param array $params additional params: region_id, offset, count
     * @return array response
     */
    public function getCities($countryId, $q='', $params=[])
    {
        $params['need_all'] = 1;
        $params['country_id'] = $countryId;
        if(!isset($params['count'])) {
            $params['count'] = $this->defaultCount;
        }
        if(!empty($q)) {
            $params['q'] = $q;
        }

        $result = $this->call('database.getCities', $params);
        return $result;
    }

    /**
     * Return array of cities names in json format.
     * For version 5.5, json format is [{id,title}].
     * See documentation (@link https://vk.com/dev/database.getCitiesById)
     * @param string $ids list of ids separated by commas
     * @return array response
     */
    public function getCitiesById($ids)
    {
        $params['city_ids'] = $ids;

        $result = $this->call('database.getCitiesById', $params);
        return $result;
    }

    /**
     * Return array of universities in json format.
     * For version 5.5, json format is {count, items=>[id,title]}.
     * See documentation (@link http://vk.com/dev.php?method=database.getUniversities)
     * @param integer $countryId id of the country in vk db. For example, 1 - Russia
     * @param integer $cityId id of the city in vk db. For example, 144 - Tomsk
     * @param string $q query string
     * @param array $params additional params: offset, count
     * @return array response
     */
    public function getUniversities($countryId, $cityId, $q='', $params=[])
    {
        $params['need_all'] = 1;
        $params['country_id'] = $countryId;
        $params['city_id'] = $cityId;
        if(!isset($params['count'])) {
            $params['count'] = $this->defaultCount;
        }
        if(!empty($q)) {
            $params['q'] = $q;
        }

        $result = $this->call('database.getUniversities', $params);
        return $result;
    }

    /**
     * Return array of schools in json format.
     * For version 5.5, json format is {count, items=>[id,title]}.
     * See documentation (@link http://vk.com/dev.php?method=database.getSchools)
     * @param integer $countryId id of the country in vk db. For example, 1 - Russia
     * @param integer $cityId id of the city in vk db. For example, 144 - Tomsk
     * @param string $q query string
     * @param array $params additional params: offset, count
     * @return array response
     */
    public function getSchools($countryId, $cityId, $q='', $params=[])
    {
        $params['need_all'] = 1;
        $params['country_id'] = $countryId;
        $params['city_id'] = $cityId;
        if(!isset($params['count'])) {
            $params['count'] = $this->defaultCount;
        }
        if(!empty($q)) {
            $params['q'] = $q;
        }

        $result = $this->call('database.getSchools', $params);
        return $result;
    }

    /**
     * @param string $method method name
     * @param array $params params for method
     * @return array the response
     */
    public function call($method, $params=[])
    {
        $params['v'] = $this->version;
        if(!isset($params['lang'])) {
            $params['lang'] = Yii::$app->language;
        }

        $result = $this->makeRequest($method, ['query' => $params]);
        return $result['response'];
    }

    /**
     * Makes the curl request to the url.
     * @param string $url relative url to request.
     * @param array $options HTTP request options. Keys: query, data, options, headers.
     * @return array|mixed the response.
     * @throws Exception
     */
    protected function makeRequest($url, $options = [])
    {
        $request = $this->initRequest($url, $options);
        $response = $request->send();

        $data = $response->getData();
        $error = $this->fetchJsonError($data);
        if($error != null) {
            throw new Exception($error['message'], $error['code']);
        }

        if(!$response->isOk) {
            Yii::error(
                'Invalid response http code: ' . $response->getStatusCode() . '.' . PHP_EOL .
                'Headers: ' . Json::encode($response->getHeaders()->toArray()) . '.' . PHP_EOL .
                'URL: ' . $url . PHP_EOL .
                'Options: ' . Json::encode($options) . PHP_EOL .
                'Result: ' . (is_array($data) ? Json::encode($data) : var_export($data, true)),
                __METHOD__
            );
            throw new HttpException($response->getStatusCode());
        }

        return $data;
    }

    /**
     * Initializes a new request.
     * @param string $url relative url to request.
     * @param array $options HTTP request options. Keys: query, data, headers, options
     * @return \yii\httpclient\Request
     */
    protected function initRequest($url, $options = [])
    {
        $client = new Client([
            'baseUrl' => self::API_BASE_URL,
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest();
        if (isset($options['data'])) {
            $request->setMethod('post');
            $request->setData($options['data']);
        }
        if (!empty($options['headers'])) {
            $request->setHeaders($options['headers']);
        }
        if (!empty($options['options'])) {
            $request->setOptions($options['options']);
        }
        if (isset($options['query'])) {
            $url_parts = parse_url($url);
            if (isset($url_parts['query'])) {
                $query = $url_parts['query'];
                if (strlen($query) > 0) {
                    $query .= '&';
                }
                $query .= http_build_query($options['query']);
                $url = str_replace($url_parts['query'], $query, $url);
            }
            else {
                $url_parts['query'] = $options['query'];
                $new_query = http_build_query($url_parts['query']);
                $url .= '?' . $new_query;
            }
        }
        $request->setUrl($url);

        return $request;
    }

    /**
     * Returns the error info from json.
     * @param array $json the json response.
     * @return array the error array with 2 keys: code and message. Should be null if no errors.
     */
    protected function fetchJsonError($json)
    {
        if (isset($json['error'])) {
            return [
                'code' => is_string($json['error']) ? 0 : $json['error']['error_code'],
                'message' => is_string($json['error']) ? $json['error'] : $json['error']['error_msg'],
            ];
        }
        else {
            return null;
        }
    }
}