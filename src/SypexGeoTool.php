<?php namespace DeftCMS\Components\SypexGeo;

use DeftCMS\Components\GeoTools\SxGeoApi;
use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DeftCMS      Get full geo info by remote IP-address
 *
 * @package	    DeftCMS
 * @category	Libraries
 * @author	    b1tc0re
 * @copyright   (c) 2018, DeftCMS (http://deftcms.org)
 * @since	    Version 0.0.1
 */
class SypexGeoTool
{

    /**
     * API
     * @var SxGeoApi
     */
    private $_SxGeo;

    /**
     * SypexGeoTool constructor.
     */
    public function __construct()
    {
        Engine::$DT->load->config('sypex.geo');
        Engine::$DT->load->library('user_agent');
        $this->_SxGeo = new SxGeoApi(Engine::$DT->config->item('sx.database_path'));
    }

    /**
     * Get location information by client ip address
     * @return array
     */
    public function getByClientIpAddress()
    {
        if (Engine::$DT->agent->is_robot())
        {
            return Engine::$DT->config->get('sx.default_location', array());
        }

        return $this->getGeoData(Engine::$DT->input->ip_address());
    }

    /**
     * Locate by ip address
     * @param null|string $ip IP address for locating
     * @return array
     */
    private function getGeoData($ip = null)
    {
        $ip !== null or $ip = Engine::$DT->input->ip_address();
        $data = $this->_SxGeo->getCityFull($ip);
        return empty($data) ? Engine::$DT->config->get('sx.default_location', array()) : $data;
    }
}