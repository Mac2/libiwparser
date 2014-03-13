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
 * @author     masel <masel789@gmail.com>
 * @package    libIwParsers
 * @subpackage helpers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class HelperC
{

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given duration into seconds.
     *
     * @param string $value durationstring
     *
     * @return int|bool integer if conversion was successfull,
     *                  boolean false if the provided parameter couldn't be recognized as a duration
     */
    static public function convertMixedDurationToSeconds($value)
    {
        $value = trim($value);

        $mktime = array(
            'days'    => 0,
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        );

        if (preg_match('/((?P<days>\d+)\s(Tag|Tage|day|days)\s+)?(?P<hours>2[0-4]|[01]\d|\d)\:(?P<minutes>[0-6]?\d)(\:(?P<seconds>[0-6]?\d))?/', $value, $aResult)) {
            if (!empty($aResult['days'])) {
                $mktime['days'] = (int)$aResult['days'];
            }
            $mktime['hours']   = (int)$aResult['hours'];
            $mktime['minutes'] = (int)$aResult['minutes'];
            if (!empty($aResult['seconds'])) {
                $mktime['seconds'] = (int)$aResult['seconds'];
            }

            return $mktime['days'] * 24 * 60 * 60 + $mktime['hours'] * 60 * 60 + $mktime['minutes'] * 60 + $mktime['seconds'];
        } else {
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given date into a unix timestamp.
     *
     * @param $value string timestring
     *
     * @return int|bool - integer if conversion was successfull
     *                    boolean false if the provided parameter couldn't be recognized as a date
     */
    static public function convertDateToTimestamp($value)
    {
        $value = trim($value);

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

        $mktime = array(
            'day'   => 0,
            'month' => 0,
            'year'  => 0,
        );

        if (preg_match('/(?P<day>[0-3]?\d)\D{0,2}?[\s\.](?:(?P<month_num>[0-1]?\d)|(?P<month_str>\D+))[\s\.](?P<year>20\d\d)/', $value, $aResult) OR
            preg_match('/(?P<year>20\d\d)[\-\.](?P<month_num>[0-1]?\d)[\-|\.](?P<day>[0-3]?\d)/', $value, $aResult) OR
            preg_match('/(?P<month_str>\D+)\s(?P<day>[0-3]?\d)\D{0,2}\,?\s(?P<year>20\d\d)/', $value, $aResult)
        ) {

            $mktime['year'] = (int)$aResult['year'];

            if (!empty($aResult['month_num'])) { //a month number
                $mktime['month'] = (int)$aResult['month_num'];
            } elseif (!empty($aResult['month_str']) AND isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateTimeToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }

            $mktime['day'] = (int)$aResult['day'];

            return mktime(0, 0, 0, $mktime['month'], $mktime['day'], $mktime['year']);

        } else {
            return false;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given duration into a unix timestamp.
     *
     * @param $value String timestring
     *
     * @return int|bool - integer if conversion was successfull
     *                 boolean false if the provided parameter couldn't be
     *                 recognized as a date
     */
    static public function convertMixedDurationToTimestamp($value)
    {
        $value = trim($value);

        $mktime = array(
            'days'    => 0,
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        );

        if (preg_match('/((?P<days>\d+)\s(Tag|Tage|day|days)\s+)?(?P<hours>2[0-4]|[01]\d|\d)\:(?P<minutes>[0-6]?\d)(\:(?P<seconds>[0-6]?\d))?/', $value, $aResult) != false) {

            $mktime['hours']   = (int)$aResult['hours'];
            $mktime['minutes'] = (int)$aResult['minutes'];
            if (!empty($aResult['seconds'])) {
                $mktime['seconds'] = (int)$aResult['seconds'];
            }

            if (!empty($aResult['days'])) {
                $mktime['hours'] = ((int)$aResult['days']) * 24;
            }

            return mktime($mktime['hours'], $mktime['minutes'], $mktime['seconds']);

        } else {
            return false;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Tries to convert the given time into a unix timestamp.
     *
     * @param $value string timestring
     *
     * @return Int|bool integer if conversion was successfull
     *                 boolean false if the provided parameter couldn't be
     *                 recognized as a date
     */
    static public function convertTimeToTimestamp($value)
    {
        $value = trim($value);

        $mktime = array(
            'hour'   => 0,
            'minute' => 0,
            'second' => 0,
        );

        if (preg_match('/(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-5]?\d)(\:(?P<second>[0-5]?\d))?(\s(?P<pm>pm)|am)?/i', $value, $aResult) != false) {

            $mktime['hour']   = (int)$aResult['hour'];
            $mktime['minute'] = (int)$aResult['minute'];
            if (!empty($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }
            if (isset($aResult['pm']) AND $aResult['pm'] == 'pm') {
                $mktime['hour'] += 12;
            }

            return mktime($mktime['hour'], $mktime['minute'], $mktime['second']);

        } else {
            return false;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    static public function convertBracketStringToArray($string)
    {
        $return  = array();
        $treffer = array();
        if (preg_match_all('%(?:\(((?:[^\n\(\)]+)(?:\((?:[^\n\(\)]*)\)(?:[^\n\(\)]*))*)\))%', $string, $treffer)) {
            $return = $treffer[1];
        }

        return $return;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * Converts the given string into an coordinates result DTO.
     *
     * @param $strCoordinates string a string of the format gal:sys:pla
     *
     * @return DTOCoordinatesC
     */
    static public function convertCoordinates($strCoordinates)
    {
        $retVal  = new DTOCoordinatesC();
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
     * Tries to convert the given datetime into a unix timestamp.
     *
     * @param $value string timestring
     *
     * @return int|bool - integer if conversion was successfull
     *                 boolean false if the provided parameter couldn't be
     *                 recognized as a date
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

        $mktime = array(
            'unix'   => 0,
            'hour'   => 0,
            'minute' => 0,
            'second' => 0,
            'month'  => 0,
            'day'    => 0,
            'year'   => 0,
        );

        /*
         * match date.
         * See IW Account => Settings => Administration => Time formats
         *
         * TT   :=    0 -   39 => [0-3]?\d
         * MM   :=    0 -   19 => [0-1]?\d
         * JJJJ := 2000 - 2099 => 20\d\d
         * HH   :=    0 -   24 => 2[0-4]|[01]\d|\d
         * MM   :=    0 -   69 => [0-6]?\d
         * SS   :=    0 -   69 => [0-6]?\d
         * optional am or pm
         */
        if (preg_match('/^(?P<day>[0-3]?\d)\.(?P<month_num>[0-1]?\d)\.(?P<year>20\d\d)\s(?P<hour>2[0-4]|[01]\d|\d):(?P<minute>[0-6]?\d):(?P<second>[0-6]?\d)$/', $value, $aResult) OR
            preg_match('/^(?P<day>[0-3]?\d)\D{0,2}?[\s\.](?:(?P<month_num>[0-1]?\d)|(?P<month_str>\D+))[\s\.](?P<year>20\d\d)\s(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-6]?\d)(?:\:(?P<second>[0-6]?\d))?$/', $value, $aResult) OR
            preg_match('/(?P<year>20\d\d)[\-\.](?P<month_num>[0-1]?\d)[\-\.](?P<day>[0-3]?\d)\s(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-6]?\d)(?:\:(?P<second>[0-6]?\d))?/', $value, $aResult) OR
            preg_match('/(?P<month_str>\D+)\s(?P<day>[0-3]?\d)\D{0,2}?\,?\s(?P<year>20\d\d)\,?\s(?P<hour>2[0-4]|[01]\d|\d)\:(?P<minute>[0-6]?\d)(?:\:(?P<second>[0-6]?\d))?(\s(?P<pm>pm)|am)?/i', $value, $aResult) OR
            preg_match('/(?P<day>[0-3]?\d)\D{0,2}?[\s\.](?:(?P<month_num>[0-1]?\d)|(?P<month_str>\D+))[\s\.](?P<year>20\d\d)/', $value, $aResult) OR
            preg_match('/(?P<year>20\d\d)[\-\.](?P<month_num>[0-1]?\d)[\-\.](?P<day>[0-3]?\d)/', $value, $aResult) OR
            preg_match('/(?P<month_str>\D+)\s(?P<day>[0-3]?\d)\D{0,2}?\,?\s(?P<year>20\d\d)/', $value, $aResult)
        ) {

            $mktime['year'] = (int)$aResult['year'];

            if (!empty($aResult['month_num'])) { //a month number
                $mktime['month'] = (int)$aResult['month_num'];
            } elseif (!empty($aResult['month_str']) AND isset($month2int[$aResult['month_str']])) { //a valid month string
                $mktime['month'] = (int)$month2int[$aResult['month_str']];
            } else {
                trigger_error('Invalid month conversation in HelperC::convertDateTimeToTimestamp Value:' . $aResult['month_str'], E_USER_NOTICE);

                return false;
            }

            $mktime['day'] = (int)$aResult['day'];

            if (isset($aResult['hour']) AND !empty($aResult['hour'])) {
                $mktime['hour'] = (int)$aResult['hour'];
            }

            if (!empty($aResult['minute'])) {
                $mktime['minute'] = (int)$aResult['minute'];
            }

            if (!empty($aResult['second'])) {
                $mktime['second'] = (int)$aResult['second'];
            }

            if (isset($aResult['pm']) AND $aResult['pm'] == 'pm') {
                $mktime['hour'] += 12;
            }

            return mktime($mktime['hour'], $mktime['minute'], $mktime['second'], $mktime['month'], $mktime['day'], $mktime['year']);

        } else {
            return false;
        }
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
        $retVal               = null;
        $xsltProcessor        = new XSLTProcessor();
        $docInjectionXsl      = new DOMDocument();
        $docSourceXml         = null;
        $filenameResources    = "http://www.icewars.de/portal/xml/$language/ressourcen.xml";
        $filenameInjectionXsl = ConfigC::get('path.xslt') . DIRECTORY_SEPARATOR . 'injectResourceNames.xsl';

        $docInjectionXsl->load($filenameInjectionXsl);

        //set the name of the resource.xml that shall be used.
        $xpath       = new DOMXPath($docInjectionXsl);
        $xpathResult = $xpath->query('//xsl:variable[@name="filenameResources"]');

        if ($xpathResult instanceof DOMNodeList && $xpathResult->length === 1) {
            $domVariable            = $xpathResult->item(0);
            $domVariable->nodeValue = $filenameResources;
        }

        $xsltProcessor->importStyleSheet($docInjectionXsl);

        if ($xml instanceof DOMDocument) {
            $docSourceXml =& $xml;
        } elseif ($xml instanceof SimpleXMLElement) {
            $docSourceXml = dom_import_simplexml($xml);
        } elseif (is_string($xml)) {
            $docSourceXml = new DOMDocument();

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
        if ($docSourceXml instanceof DOMDocument) {
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

}