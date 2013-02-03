<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <masel789@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * masel
 * ----------------------------------------------------------------------------
 */
/**
 * @author     masel <masel789@googlemail.com>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the visibility overview
 *
 * This parser is responsible for parsing the universe visibility and universe xml scan info
 *
 * Its identifier: de_wirtschaft_universe
 */
class ParserWirtschaftUniverseC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_wirtschaft_universe');
        $this->setName('Wirtschaft Universum - Sichtweite');
        $this->setRegExpCanParseText('/UniversumScan/sm');
        $this->setRegExpBeginData('/Letztes Scanergebnis/sm');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserUniversumSichtweiteResultC();
        $retVal                      =& $parserResult->objResultData;
        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match($regExp, $this->getText(), $aResult);

        //ToDo evl: Die Sichtweite ist mit der Unikontrolleinrichtung auch parsebar durch die Anzeige der Unixml Scanbereiche

        if ($fRetVal !== false && $fRetVal > 0) {
            if (!empty($aResult['NewUniXmlTime'])) {
                $retVal->iNewUniXmlTime = HelperC::convertDateTimeToTimestamp($aResult['NewUniXmlTime']);
            }
            $parserResult->bSuccessfullyParsed = true;
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = "unable to match Pattern";
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         */

        $reDateTime = $this->getRegExpDateTime();

        $regExp = '~';
        $regExp .= '(?:Kein\sErgebnis)|(?:Dateiname:)'; //xml-Link is parsed by ParserXmlC
        $regExp .= '.*?Scannen.*?';
        $regExp .= '(?:Der\snächste\sScan\sist\serst\sab\s(?P<NewUniXmlTime>' . $reDateTime . ')\smöglich|Kosten)';
        $regExp .= '~sx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * For debugging with "The Regex Coach" which doesn't support named groups
     */
    private function getRegularExpressionWithoutNamedGroups()
    {
        $retVal = $this->getRegularExpression();

        $retVal = preg_replace('/\?P<\w+>/', '', $retVal);

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////