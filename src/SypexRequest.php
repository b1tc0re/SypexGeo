<?php namespace DeftCMS\Components\b1tc0re\SypexGeo;

use DeftCMS\Components\b1tc0re\Request\RequestClient;
use GuzzleHttp\Psr7\Utils;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * DeftCMS      Get full geo info by remote IP-address
 *
 * @package	    DeftCMS
 * @category	Libraries
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
class SypexRequest extends RequestClient
{
    protected $serviceScheme = self::HTTPS_SCHEME;
    protected $serviceDomain = 'sypexgeo.net';

    /**
     * @param string $url
     * @param string $pathFile
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download($url, $pathFile)
    {
        $resource = fopen($pathFile, 'wb');
        $stream = Utils::streamFor($resource);

        $this->getClient()->request('GET', $url, [ 'save_to' => $stream ]);

    }
}