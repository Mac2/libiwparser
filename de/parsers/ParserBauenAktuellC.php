<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <martin@martimeo.de> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Martin Martimeo
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Martin Martimeo <martin@martimeo.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the aktueller Bau
 *
 * This parser is responsible for parsing the building site
 *
 * Its identifier: de_bauen_aktuell
 */
class ParserBauenAktuellC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_bauen_aktuell');
        $this->setName('aktueller Geb&auml;udebau');
        $this->setRegExpCanParseText('/aktuell\sim\sBau\sauf\sdiesem\sPlaneten/sm');
        $this->setRegExpBeginData($this->getRegExpCanParseText());
        $this->setRegExpEndData('/Ausbau/sm');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserBauenAktuellResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                $iDateToExpire = HelperC::convertMixedDurationToSeconds($result['dateToExpire']);
                $iDateOfFinish = HelperC::convertDateTimeToTimestamp($result['dateOfFinish']);
                $strBuilding   = $result['building'];

                $entry                = new DTOParserBauenAktuellResultBuildingC();
                $entry->iDateToExpire = PropertyValueC::ensureInteger($iDateToExpire);
                $entry->iDateOfFinish = PropertyValueC::ensureInteger($iDateOfFinish);
                $entry->strBuilding   = PropertyValueC::ensureString(trim($strBuilding));

                $retVal->aBuildings[] = $entry;
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         * die Daten sind Zeilen, von denen jede folgendermaßen aussieht:
         * Datum | Vergangene Zeit | Forschung
         */

        $reDateOfFinish = $this->getRegExpDateTime();
        $reDateToExpire = $this->getRegExpMixedTime();

        $regExp = '/';
        $regExp .= '(?P<building>' . '[^\n\t]+' . ')\s+?';
        $regExp .= 'bis\s(?P<dateOfFinish>' . $reDateOfFinish . ')[\n\s]+?';
        $regExp .= '(?P<dateToExpire>' . $reDateToExpire . '|abgeschlossen|)';
        $regExp .= '/m';

        return $regExp;
    }

}