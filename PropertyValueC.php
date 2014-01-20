<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <benjamin.woester@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Benjamin Wöster
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Benjamin Wöster <benjamin.woester@googlemail.com>
 * @package    libIwParsers
 * @subpackage helpers
 */

namespace libIwParsers;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class PropertyValueC
{
    /////////////////////////////////////////////////////////////////////////////

    public static function ensureBoolean($value)
    {
        return (boolean)$value;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * function ensureInteger
     *
     * filtert beliebige Zahlen mit Tausendertrennzeichen und maximal 2 Nachkommastellen
     *
     * @param string|int|float   $value  Zahl zum Filtern
     * @uses   PropertyValueC::ensureFloat
     * @return int gefilterte Zahl
     *
     * @author masel <masel789@googlemail.com>
     */
    public static function ensureInteger($value)
    {
        return (integer)round(PropertyValueC::ensureFloat($value));
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * function ensureFloat
     *
     * filtert beliebige Zahlen mit Tausendertrennzeichen und maximal 2 Nachkommastellen
     *
     * @param string|int|float   $value  Zahl zum Filtern
     *
     * @return float gefilterte Zahl
     *
     * @author masel <masel789@googlemail.com>
     */
    public static function ensureFloat($value)
    {

        $filtered_number = 0;
        if (preg_match('~^\s*(?P<sign>-|\+|)(?P<digit>\d{1,3}(?:(\D?)\d{3})?(?:\3\d{3})*)(?:\D(?P<part>\d{1,2}))?\s*$~', $value, $numberpart)) {
            $filtered_number = preg_replace('~\D~', '', $numberpart['digit']);

            if (isset($numberpart['part'])) { //Nachkommastellen vorhanden?
                if (strlen($numberpart['part']) === 2) { //zwei Nachkommastellen
                    $filtered_number += $numberpart['part'] / 100;
                } else { //eine Nachkommastelle
                    $filtered_number += $numberpart['part'] / 10;
                }
            }

            if ($numberpart['sign'] === '-') { //evl. negatives Vorzeichen wieder dazu
                $filtered_number = -$filtered_number;
            }

        }

        return (float)$filtered_number;

    }

    /////////////////////////////////////////////////////////////////////////////

    public static function ensureString($value)
    {
        return (string)$value;
    }

    /////////////////////////////////////////////////////////////////////////////

    public static function ensureArray($value)
    {
        return (array)$value;
    }

    /////////////////////////////////////////////////////////////////////////////

    public static function ensureObject($value)
    {
        return (object)$value;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Checks if the given value is a value of the enum $enumName
     *
     * e.g.
     * If you try to ensure resource names, you'd call it like this:
     *
     * $result = PropertyValueC::ensureEnum( 'Eisen', 'eResources' );
     *
     * @author masel <masel789@googlemail.com>
     */
    public static function ensureEnum($value, $enumName)
    {
        $enumClass = "\\libIwParsers\\enums\\".$enumName;

        if (isset($enumClass::$enum[$value])) {
            return ($enumClass::$enum[$value]);
        } else {
            throw new \Exception("'$value' is not a valid enumerable value for enum '$enumName'.");
        }
    }

    /////////////////////////////////////////////////////////////////////////////

}