<?php namespace DeftCMS\Components\b1tc0re\SypexGeo;

use GuzzleHttp\Exception\GuzzleException;
use PhpZip\Exception\ZipException;
use RuntimeException;

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
class SypexGeoTool
{
    /**
     * Путь к файлам загрузкии
     */
    const DOWNLOAD_URL = 'files/SxGeoCity_utf8.zip';

    /**
     * API
     * @var \SxGeo
     */
    private $_SxGeo;

    /**
     * SypexGeoTool constructor.
     */
    public function __construct()
    {
        $databasePath = __DIR__ . '/files/SxGeoCity.dat';

        if( class_exists('DeftCMS\Engine', false) )
        {
            $databasePath = Engine::$DT->config->item('sx.database_path');
        }

        $this->_SxGeo = new \SxGeo($this->getDataBasePath($databasePath));
    }

    /**
     * @param string|array $ipaddress
     * @return array
     */
    public function get($ipaddress)
    {
        if( !is_array($ipaddress) ) {
            $ipaddress = [ $ipaddress ];
        }

        $result = [];

        foreach ($ipaddress as $_) {

            if( $data = $this->_SxGeo->getCityFull($_) ) {
                $result[] = $data;
            }

        }

        return $result;
    }

    /**
     * Получить путь к файлам базы данных
     * @param string $databasePath - Путь к базе данных
     * @return string
     *
     * @throws GuzzleException
     * @throws ZipException
     */
    private function getDataBasePath($databasePath)
    {
        if( $databasePath === null || !file_exists($databasePath) )
        {
            $databasePath = $this->download($databasePath);
        }

        return $databasePath;
    }

    /**
     * Загрузить базы данных для работы с SxGeo
     *
     * @param string $databasePath
     *
     * @return string
     * @throws GuzzleException
     * @throws ZipException
     * @throws RuntimeException
     */
    private function download($databasePath)
    {
        $client = new SypexRequest();
        $tmpSxZip = tempnam(sys_get_temp_dir(), "sx_");

        $client->download(self::DOWNLOAD_URL, $tmpSxZip);

        $zipFile = new \PhpZip\ZipFile();
        $zipFile->openFile($tmpSxZip);

        $path = pathinfo($databasePath, PATHINFO_DIRNAME);

        if( !is_dir($path) && !mkdir($path, 0777) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        $zipFile->extractTo($path, [ 'SxGeoCity.dat' ]);

        return $databasePath;
    }
}