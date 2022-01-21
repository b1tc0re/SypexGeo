<?php namespace DeftCMS\Components\b1tc0re\SypexGeo;

use SxGeo;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * DeftCMS     Extend SxGeo
 * Array and string offset access using curly braces in php >=7.4
 *
 * @package	    DeftCMS
 * @category	Libraries
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
class SxGeoEx extends SxGeo
{

    /**
     * @param        $pack
     * @param string $item
     * @see https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.array-string-access-curly-brace
     *
     * @return array
     */
    protected function unpack($pack, $item = ''){
        $unpacked = array();
        $empty = empty($item);
        $pack = explode('/', $pack);
        $pos = 0;
        foreach($pack AS $p){
            list($type, $name) = explode(':', $p);
            $type0 = $type[0];
            if($empty) {
                $unpacked[$name] = $type0 === 'b' || $type0 === 'c' ? '' : 0;
                continue;
            }
            switch($type0){
                case 't':
                case 'T': $l = 1; break;
                case 's':
                case 'n':
                case 'S': $l = 2; break;
                case 'm':
                case 'M': $l = 3; break;
                case 'd': $l = 8; break;
                case 'c': $l = (int)substr($type, 1); break;
                case 'b': $l = strpos($item, "\0", $pos)-$pos; break;
                default: $l = 4;
            }
            $val = substr($item, $pos, $l);
            switch($type0){
                case 't': $v = unpack('c', $val); break;
                case 'T': $v = unpack('C', $val); break;
                case 's': $v = unpack('s', $val); break;
                case 'S': $v = unpack('S', $val); break;
                case 'm': $v = unpack('l', $val . (ord($val[2]) >> 7 ? "\xff" : "\0")); break;
                case 'M': $v = unpack('L', $val . "\0"); break;
                case 'i': $v = unpack('l', $val); break;
                case 'I': $v = unpack('L', $val); break;
                case 'f': $v = unpack('f', $val); break;
                case 'd': $v = unpack('d', $val); break;

                case 'n': $v = current(unpack('s', $val)) / pow(10, $type[1]); break;
                case 'N': $v = current(unpack('l', $val)) / pow(10, $type[1]); break;

                case 'c': $v = rtrim($val, ' '); break;
                case 'b': $v = $val; $l++; break;
            }
            $pos += $l;
            $unpacked[$name] = is_array($v) ? current($v) : $v;
        }
        return $unpacked;
    }
}