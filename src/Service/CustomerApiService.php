<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Class ServerService
 *
 * @category Api
 *
 * @package App\Service
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class CustomerApiService
{
    const ACTION_TYPE_GET_BALANCE = 'getBalance';
    const ACTION_TYPE_ADD_BALANCE = 'addBalance';
    const ERROR_CODE_TIMEOUT = 2.1;
    const ERROR_CODE_404 = 2.2;
    const ERROR_CODE_500 = 2.3;
    const ERROR_CODE_GENERAL = 2.4;
    const ERROR_CODE_CARD_NOT_FOUND = 2.5;
    const ERROR_CODE_SYNTAX_ERROR = 2.6;


    /**
     * CustomerApiService constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param array $apiInfo
     * @param $apiData
     * @return array
     */
    public function processCustomerRequest($apiInfo, $apiData)
    {
        // Check number parameter exists
        if (!isset($apiData['number'])) {
            return array(
                'type' => 'ERROR',
                'text' => 'Number should be inserted.',
                'error_code' => self::ERROR_CODE_SYNTAX_ERROR,
                'headerStatus' => Response::HTTP_BAD_REQUEST,
            );
        }

        if (!isset($apiInfo['action_type']) || !isset($apiInfo['base_url']) || !isset($apiInfo['username']) || !isset($apiInfo['password'])) {
            return array(
                'type' => 'ERROR',
                'text' => 'Syntax error',
                'error_code' => self::ERROR_CODE_SYNTAX_ERROR,
                'headerStatus' => Response::HTTP_BAD_REQUEST,
            );
        }

        switch ($apiInfo['action_type'])
        {
            case self::ACTION_TYPE_GET_BALANCE:
                $requestUrl = $apiInfo['base_url'] . '?action=getBalance&number' . $apiData['number'];
                return $this->fetch($requestUrl, $apiInfo, $apiData['number']);
            case self::ACTION_TYPE_ADD_BALANCE:
                if (!isset($apiData['currency']) || !isset($apiData['amount'])) {
                    return array(
                        'type' => 'ERROR',
                        'text' => 'Syntax error',
                        'error_code' => self::ERROR_CODE_SYNTAX_ERROR,
                        'headerStatus' => Response::HTTP_BAD_REQUEST,
                    );
                }

                $requestUrl = $apiInfo['base_url'] . '?action=addBalance&number' . $apiData['number'] . '&currency='. $apiData['currency'] . '&amount=' . $apiData['amount'];
                return $this->fetch($requestUrl, $apiInfo, $apiData['number']);
            default:
                return array(
                    'type' => 'ERROR',
                    'text' => 'Syntax error',
                    'error_code' => self::ERROR_CODE_404,
                    'headerStatus' => Response::HTTP_BAD_REQUEST,
                );
        }
    }

    public function fetch($requestUrl, $apiInfo, $number, $method = 'GET') {
        try {
            $client = HttpClient::create(['headers' => [
                'Authorization' => 'username=' . $apiInfo['username'] . '&password=' . $apiInfo['password'],
                'Content-Type' => 'text/plain',
            ]]);

            // After testing, you should comment bellow line code
            $client = $this->getMockClientByNumber($number, $apiInfo);

            $response = $client->request($method, $requestUrl);

            if (isset($response) )
            {
                $statusCode = $response->getStatusCode();
                switch ($statusCode) {
                    case Response::HTTP_NOT_FOUND:
                        return array(
                            'type' => 'ERROR',
                            'text' => 'Http Not found',
                            'error_code' => self::ERROR_CODE_404,
                            'headerStatus' => Response::HTTP_NOT_FOUND
                        );
                    case Response::HTTP_INTERNAL_SERVER_ERROR:
                        return array(
                            'type' => 'ERROR',
                            'text' => 'Http internal server error',
                            'error_code' => self::ERROR_CODE_500,
                            'headerStatus' => Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    case Response::HTTP_OK || Response::HTTP_ACCEPTED:
                        $content = $response->getContent();
                        if (!empty($content)) {
                            $result = self::xmlToArray($content);
                            if (isset($result['results'])) {
                                $result = $result['results'];
                            } else if (isset($result['addBalance'])) {
                                $result = $result['addBalance'];
                            }
                            if (!isset($result['type'])) {
                                $result['type'] = 'SUCCESS';
                            }
                            if (!isset($result['text'])) {
                                $result['text'] = 'Top-up done successfully!';
                            }
                            $result['error_code'] = '';
                            $result['headerStatus'] = $statusCode;
                            if ($result['type'] === 'ERROR' && !in_array($result['text'], array('Syntax error', 'Card not found'))) {
                                return array(
                                    'type' => 'ERROR',
                                    'text' => 'General error',
                                    'error_code' => self::ERROR_CODE_GENERAL,
                                    'headerStatus' => Response::HTTP_NO_CONTENT
                                );
                            }
                            return $result;
                        }
                        return array(
                            'type' => 'ERROR',
                            'text' => 'Http Not found',
                            'error_code' => self::ERROR_CODE_404,
                            'headerStatus' => Response::HTTP_NOT_FOUND
                        );
                    default:
                        return array(
                            'type' => 'ERROR',
                            'text' => 'Timeout',
                            'error_code' => self::ERROR_CODE_TIMEOUT,
                            'headerStatus' => Response::HTTP_REQUEST_TIMEOUT
                        );
                }
            }else {
                return array(
                    'type' => 'ERROR',
                    'text' => 'Timeout',
                    'error_code' => self::ERROR_CODE_TIMEOUT,
                    'headerStatus' => Response::HTTP_REQUEST_TIMEOUT
                );
            }
        } catch (TransportExceptionInterface $e) {
            return array(
                'type' => 'ERROR',
                'text' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'headerStatus' => Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param $xmlString
     * @return array
     */
    public static function xmlToArray( $xmlString )
    {
        $xml = simplexml_load_string($xmlString);

        if ( !$xml || $xml === '' || $xml === null )
        {
            return array();
        }

        return self::processXmlObject($xml);
    }

    private static function processXmlObject( \SimpleXMLElement $el )
    {
        $result = (array) $el;

        foreach ( $result as $key => $val )
        {
            if ( is_object($val) && $val instanceof \SimpleXMLElement )
            {
                $result[$key] = self::processXmlObject($val);
            }
        }

        return $result;
    }

    public function getMockClientByNumber($number, $apiInfo) {
        switch ($number) {
            case 1:
                $body = '<?xml version="1.0" encoding="utf-8"?><results><type>ERROR</type><text>Card not found</text></results>';
                $info = array('response_headers' =>array(
                    'type' => 'ERROR',
                    'Content-type' => 'text/xml',
                    'charset'=>'utf-8'
                ),
                    'http_code' => Response::HTTP_NOT_FOUND
                );
                break;
            case 2:
                $body = '<?xml version="1.0" encoding="utf-8"?><results><type>ERROR</type><text>Syntax error</text></results>';
                $info = array('response_headers' =>array(
                    'type' => 'ERROR',
                    'Content-type' => 'text/xml',
                    'charset'=>'utf-8',
                ),
                    'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                );
                break;
            case 3:
                if ($apiInfo['action_type'] === 'getBalance') {
                    $body = '<?xml version="1.0" encoding="utf-8"?><results><card><number>3</number><blocked>false</blocked><balance>72.00</balance><curr>USD</curr></card></results>';
                    $info = array('response_headers' =>array(
                        'type' => 'SUCCESS',
                        'Content-type' => 'text/xml',
                        'charset'=>'utf-8'
                    ),
                        'http_code' => Response::HTTP_ACCEPTED
                    );
                } else {
                    $body = '<?xml version="1.0" encoding="utf-8"?><result>Ok</result>';
                    $info = array('response_headers' =>array(
                        'type' => 'SUCCESS',
                        'Content-type' => 'text/xml',
                        'charset'=>'utf-8'
                    ),
                        'http_code' => Response::HTTP_ACCEPTED
                    );
                }
                break;
            default:
                $body= '<?xml version="1.0" encoding="utf-8"?><results><type>ERROR</type><text>Не атрымалася ініцыялізаваць пар</text></results>';
                $info = array('response_headers' =>array(
                    'type' => 'ERROR',
                    'Content-type' => 'text/xml'
                ),
                    'http_code' => Response::HTTP_REQUEST_TIMEOUT
                );
        }
        return new MockHttpClient(new MockResponse($body, $info));
    }
}
