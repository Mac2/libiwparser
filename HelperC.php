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
 * @author     Martin Martimeo <martin@martimeo.de>
 * @package    libIwParsers
 * @subpackage helpers
 */

namespace libIwParsers;

use libIwParsers\de\parserResults\DTOCoordinatesC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class HelperC
{

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given duration into seconds.
     *
     *
     * @param string $value durationstring
     *
     * @return int|bool integer if conversion was successfull,
     *                  boolean false if the provided parameter couldn't be recognized as a duration
     */
    static public function convertMixedDurationToSeconds($value)
    {
        $aResult = array();
        if (preg_match('/^(?:(?P<days>\d+)\s(?:Tag|Tage|day|days)\s+|)(?P<hours>\d{1,2})\:(?P<minutes>[0-5]\d|\d)(?:\:(?P<seconds>[0-5]\d|\d))?$/', $value, $aResult) != false) {

            if (!isset($aResult['seconds'])) {
                $aResult['seconds'] = 0;
            }

            return ((int)$aResult['days'] * 24 * 60 * 60 + (int)$aResult['hours'] * 60 * 60 + (int)$aResult['minutes'] * 60 + (int)$aResult['seconds']);

        } else {
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given date into a unix timestamp.
     *
     * @param string $value date string
     *
     * @return int|bool integer (unix timestamp) if conversion was successfull,
     *                  boolean false if the provided parameter couldn't be recognized as a date
     */
    static public function convertDateToTimestamp($value)
    {
        static $month2int = array(
            'January'   => 1,
            'Jan'       => 1,
            'Januar'    => 1,
            'February'  => 2,
            'Feb'       => 2,
            'Februar'   => 2,
            'March'     => 3,
            'Mar'       => 3,
            'März'      => 3,
            'April'     => 4,
            'Apr'       => 4,
            'May'       => 5,
            'Mai'       => 5,
            'June'      => 6,
            'Juni'      => 6,
            'July'      => 7,
            'Juli'      => 7,
            'August'    => 8,
            'Aug'       => 8,
            'September' => 9,
            'Sept'      => 9,
            'October'   => 10,
            'Oct'       => 10,
            'Oktober'   => 10,
            'November'  => 11,
            'Nov'       => 11,
            'December'  => 12,
            'Dez'       => 12,
            'Dec'       => 12,
            'Dezember'  => 12
        );

        $aResult = array();
        $mktime = array();

        /*
         * match date
         *
         * simple regex patterns for day, month and year used and an additional checkdate call on the end
         */
        if (preg_match('/^(?P<day>\d{1,2})\w{0,2}(?:\s|\.)(?:(?P<month_num>\d{1,2})|(?P<month_str>\w))(?:\s|\.)(?P<year>\d{4})$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            if (!empty($aResult['month_num'])) { //a month number
                $mktime['month'] = (int)$aResult['month_num'];
            } elseif (isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }
            $mktime['year'] = (int)$aResult['year'];
        } elseif (preg_match('/^(?P<year>\d{4})(?:\-|\.)(?P<month>\d{1,2})(?:\-|\.)(?P<day>\d{1,2})$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            $mktime['month'] = (int)$aResult['month'];
            $mktime['year'] = (int)$aResult['year'];
        } elseif (preg_match('/^(?P<month_str>\w)\s(?P<day>\d{1,2})\w{0,2}(?:\s|\,\s)(?P<year>\d{4})$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            if (isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }
            $mktime['year'] = (int)$aResult['year'];
        } else {
            return false;
        }

        if (checkdate($mktime['month'], $mktime['day'], $mktime['year'])) {
            return mktime(0, 0, 0, $mktime['month'], $mktime['day'], $mktime['year']);
        } else {
            return false;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given time into a unix timestamp.
     *
     *
     * @param string $value time string
     *
     * @return Int|bool integer (a unix timestamp) if conversion was successfull,
     *                  boolean false if the provided parameter couldn't be recognized as a date
     */
    static public function convertTimeToTimestamp($value)
    {
        $aResult = array();

        /*
         * match time patterns
         *
         * valid ranges and regex patterns for hour, minute, second:
         *
         * HH   :=   0 - 00 - 24    2[0-4]|[01]\d|\d
         * MM   :=   0 - 00 - 59      [0-5]?\d
         * SS   :=   0 - 00 - 59      [0-5]?\d
         */
        if (preg_match('/^(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-5]\d)(?:\:(?P<second>[0-5]?\d))?\s?(?:(?P<pm>pm)|am|)$/i', $value, $aResult) != false) {

            if (!isset($aResult['second'])) {
                $aResult['second'] = 0;
            }
            if (isset($aResult['pm'])) {
                $aResult['hour'] += 12;
            }

            return mktime((int)$aResult['hour'], (int)$aResult['minute'], (int)$aResult['second']);

        } else {
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given datetime into a unix timestamp.
     *
     * @uses HelperC::convertDateToTimestamp to test for date patterns
     *
     * @param string $value datetime string
     *
     * @return int|bool integer if conversion was successfull,
     *                  boolean false if the provided parameter couldn't be recognized as a date
     */
    static public function convertDateTimeToTimestamp($value)
    {
        static $month2int = array(
            'January'   => 1,
            'Jan'       => 1,
            'Januar'    => 1,
            'February'  => 2,
            'Feb'       => 2,
            'Februar'   => 2,
            'March'     => 3,
            'Mar'       => 3,
            'März'      => 3,
            'April'     => 4,
            'Apr'       => 4,
            'May'       => 5,
            'Mai'       => 5,
            'June'      => 6,
            'Juni'      => 6,
            'July'      => 7,
            'Juli'      => 7,
            'August'    => 8,
            'Aug'       => 8,
            'September' => 9,
            'Sept'      => 9,
            'October'   => 10,
            'Oct'       => 10,
            'Oktober'   => 10,
            'November'  => 11,
            'Nov'       => 11,
            'December'  => 12,
            'Dez'       => 12,
            'Dec'       => 12,
            'Dezember'  => 12
        );

        $aResult = array();
        $mktime = array();
        $mktime['second'] = 0;

        /*
         * match datetime patterns
         *
         * simple regex patterns for day, month and year used and an additional checkdate call on the end
         * valid ranges and regex patterns for hour, minute, second:
         *
         * HH   :=   0 - 00 - 24    2[0-4]|[01]\d|\d
         * MM   :=   0 - 00 - 59      [0-5]?\d
         * SS   :=   0 - 00 - 59      [0-5]?\d
         */
        if (preg_match('/^(?P<day>\d{1,2})\.(?P<month>\d{1,2})\.(?P<year>\d{4})\s(?P<hour>2[0-4]|[01]\d|\d):(?P<minute>[0-5]?\d)(?:\:(?P<second>[0-5]?\d))?$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            $mktime['month'] = (int)$aResult['month'];
            $mktime['year'] = (int)$aResult['year'];
            $mktime['hour'] = (int)$aResult['hour'];
            $mktime['minute'] = (int)$aResult['minute'];
            if (isset($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }
        } else if (preg_match('/^(?P<day>\d{1,2})\w{0,2}(?:\s|\.)(?:(?P<month_num>\d{1,2})|(?P<month_str>\w))(?P<year>\d{4})\s(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-5]\d)(?:\:(?P<second>[0-5]\d))?$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            if (!empty($aResult['month_num'])) { //a month number
                $mktime['month'] = (int)$aResult['month_num'];
            } elseif (isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateTimeToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }
            $mktime['year'] = (int)$aResult['year'];
            $mktime['hour'] = (int)$aResult['hour'];
            $mktime['minute'] = (int)$aResult['minute'];
            if (isset($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }
        } elseif (preg_match('/^(?P<year>\d{4})(?:\-|\.)(?P<month>\d{1,2})(?:\-|\.)(?P<day>\d{1,2})\s(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-5]\d)(?:\:(?P<second>[0-5]\d))?$/', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            $mktime['month'] = (int)$aResult['month'];
            $mktime['year'] = (int)$aResult['year'];
            $mktime['hour'] = (int)$aResult['hour'];
            $mktime['minutes'] = (int)$aResult['minute'];
            if (isset($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }
        } elseif (preg_match('/^(?P<month_str>\w)\s(?P<day>\d{1,2})\w{0,2}(?:\s|\,\s)(?P<year>\d{4})(?:\s|\,\s)(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-6]\d)(?:\:(?P<second>[0-6]\d))?\s?(?:(?P<pm>pm)|am|)$/i', $value, $aResult) != false) {
            $mktime['day'] = (int)$aResult['day'];
            if (isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateTimeToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }
            $mktime['year'] = (int)$aResult['year'];
            $mktime['hour'] = (int)$aResult['hour'];
            $mktime['minute'] = (int)$aResult['minute'];
            if (isset($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }
            if (!empty($aResult['pm'])) {
                $mktime['hour'] += 12;
            }
        } else {
            //all datetime patterns unsuccessfully checked, try date patterns...
            $result = HelperC::convertDateToTimestamp($value);
            if ($result !== false ) {
                return $result;
            } else {
                //all date patterns also unsuccessfully checked, last try time patterns...
                return HelperC::convertTimeToTimestamp($value);
            }
        }

        if (checkdate($mktime['month'], $mktime['day'], $mktime['year'])) {
            return mktime($mktime['hour'], $mktime['minute'], $mktime['second'], $mktime['month'], $mktime['day'], $mktime['year']);
        } else {
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    static public function convertBracketStringToArray($string)
    {
        $return = array();
        $treffer = array();
        if (preg_match_all('/(?:\(((?:[^\n\(\)]+)(?:\((?:[^\n\(\)]*)\)(?:[^\n\(\)]*))*)\))/', $string, $treffer)) {
            $return = $treffer[1];
        }

        return $return;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Converts the given string into an coordinates result DTO.
     *
     * @param string $strCoordinates a string of the format gal:sys:pla
     *
     * @uses DTOCoordinatesC
     *
     * @return DTOCoordinatesC
     */
    static public function convertCoordinates($strCoordinates)
    {
        $retVal = new DTOCoordinatesC();
        $aPieces = explode(':', $strCoordinates);

        if (count($aPieces) === 3) {
            $retVal->iGalaxy = PropertyValueC::ensureInteger($aPieces[0]);
            $retVal->iSystem = PropertyValueC::ensureInteger($aPieces[1]);
            $retVal->iPlanet = PropertyValueC::ensureInteger($aPieces[2]);
        }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Includes resource names into a xml document
     *
     * The method takes a source xml, which is processed and searched
     * for occurences of resource definitions. For every definition that
     * is found, the corresponding resource name retrieved from
     * http://www.icewars.de/portal/xml/de/ressourcen.xml will be injected.
     *
     * @param mixed  $xml      the method tries to figure out what you provided.
     *                         It supports xml-strings, xml files, DOMDocuments and
     *                         SimpleXMLElements.
     * @param string $language optional. Providing this parameter, you can
     *                         define if you want the german or the englisch resource names
     *                         being injected. Valid inputs are 'de' and 'en'. The default
     *                         id 'de'.
     *
     * @return mixed. Reference to the DOMDocument representing the new XML
     *        document or NULL if an error occured.
     */
    static public function &xmlInjectResourceNames($xml, $language = 'de')
    {
        $retVal = null;
        $xsltProcessor = new \XSLTProcessor();
        $docInjectionXsl = new \DOMDocument();
        $docSourceXml = null;
        $filenameResources = "http://www.icewars.de/portal/xml/$language/ressourcen.xml";
        $filenameInjectionXsl = ConfigC::get('path.xslt') . DIRECTORY_SEPARATOR . 'injectResourceNames.xsl';

        $docInjectionXsl->load($filenameInjectionXsl);


        //set the name of the resource.xml that shall be used.
        $xpath = new \DOMXPath($docInjectionXsl);
        $xpathResult = $xpath->query('//xsl:variable[@name="filenameResources"]');

        if ($xpathResult instanceof \DOMNodeList && $xpathResult->length === 1) {
            $domVariable = $xpathResult->item(0);
            $domVariable->nodeValue = $filenameResources;
        }


        $xsltProcessor->importStyleSheet($docInjectionXsl);

        if ($xml instanceof \DOMDocument) {
            $docSourceXml =& $xml;
        } elseif ($xml instanceof \SimpleXMLElement) {
            $docSourceXml = dom_import_simplexml($xml);
        } elseif (is_string($xml)) {
            $docSourceXml = new \DOMDocument();

            //treat the param as filename...
            $fRetVal = $docSourceXml->load($xml);

            //okay, it was no file... Treat it as xml-string
            if ($fRetVal === false) {
                $fRetVal = $docSourceXml->loadXML($xml);

                //hm, it was neigther a xml-string?
                if ($fRetVal === false) {
                    //I give up.
                    $docSourceXml = null;
                }
            }
        }

        //If we found a way to open the source xml, do the real work
        if ($docSourceXml instanceof \DOMDocument) {
            $retVal = $xsltProcessor->transformToDoc($docSourceXml);

            //TODO: error processing
            if ($retVal === false) {
                $retVal = null;
            }
        }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * For debugging with "The Regex Coach" which doesn't support named groups
     */
    static public function removeNamedGroups($regularExpression)
    {
        $retVal = preg_replace('/\?P<\w+>/', '', $regularExpression);

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

}