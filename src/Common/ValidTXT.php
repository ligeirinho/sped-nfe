<?php

namespace NFePHP\NFe\Common;

/**
 * Validation for TXT representation of NFe
 *
 * @category  NFePHP
 * @package   NFePHP\NFe\Common\ValidTXT
 * @copyright NFePHP Copyright (c) 2008 - 2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfe for the canonical source repository
 */

class ValidTXT
{
    public static $errors = [];
    public static $entities = [];
    
    /**
     * Loads structure of txt from json file in storage folder
     * @param float $version
     */
    public static function loadStructure($version = 3.10)
    {
        $path = realpath(__DIR__ . "/../../storage");
        $json = file_get_contents(
            $path . '/txtstructure' . ($version*100) . '.json'
        );
        self::$entities = json_decode($json, true);
    }
    
    /**
     * Verifies the validity of txt according to the rules of the code
     * Important: The structures are in the storage folder and must be
     * obtained through reverse engineering with the free sender
     * @param string $txt
     * @return array
     */
    public static function isValid($txt)
    {
        self::loadStructure();
        $rows = explode("\n", $txt);
        foreach ($rows as $row) {
            $fields = explode('|', $row);
            if (empty($fields)) {
                continue;
            }
            $count = count($fields);
            $ref = strtoupper($fields[0]);
            if ($ref == "A") {
                self::loadStructure($fields[1]);
            }
            if (empty($ref)) {
                continue;
            }
            if (substr($row, -1) != '|') {
                self::$errors[] = "ERRO: Todas as linhas devem terminar com 'pipe'. [$row]";
                continue;
            }
            if (!array_key_exists($ref, self::$entities)) {
                self::$errors[] = "ERRO: Essa referencia não está definida. [$row]";
                continue;
            }
            $default = count(explode('|', self::$entities[$ref]));
            if ($default != $count) {
                self::$errors[] = "ERRO: O numero de parametros na linha "
                    . "está errado. [ $row ] Esperado [ "
                    . self::$entities[$ref]." ]";
            }
        }
        return self::$errors;
    }
}