<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <MacXY@herr-der-mails.de> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Mac
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

namespace libIwParsers\de\parsers;

use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserMsgBaseC;
use libIwParsers\ParserMsgI;
use libIwParsers\HelperC;
use libIwParsers\de\parserResults\DTOParserIndexFleetResultC;
use libIwParsers\de\parserResults\DTOParserIndexFleetResultFleetC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for Mainpage
 *
 * This parser is responsible for the Flotten/Transporte section on the Mainpage
 *
 * Its identifier: de_index_fleet
 */
class ParserIndexFleetC extends ParserMsgBaseC implements ParserMsgI
{

    private $type = '';

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_index_fleet');
        $this->setCanParseMsg('Fleet');
    }

    /////////////////////////////////////////////////////////////////////////////

    public function setType($type)
    {
        $this->type = $type;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserIndexFleetResultC();
        $retVal =& $parserResult->objResultData;
        $retVal->strType = $this->type;

        $regExp = $this->getRegularExpression();
        $msg    = $this->getMsg();

        $parserResult->strIdentifier = 'de_index_fleet';
        $aResult = array();

        $fRetVal = preg_match_all($regExp, $msg->strParserText, $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            if ($this->getObjectsVisible()) {
                $retVal->bObjectsVisible = true;
            }

            foreach ($aResult as $result) {

                $retObj = new DTOParserIndexFleetResultFleetC();

                $planetName = PropertyValueC::ensureString($result['strPlanetNameTo']);
                if (strpos($planetName, '* +') === false) {
                    $retObj->strPlanetNameTo = $planetName;
                }
                $retObj->strPlanetNameFrom = PropertyValueC::ensureString($result['strPlanetNameFrom']);
                $retObj->strUserNameFrom   = PropertyValueC::ensureString($result['strUserNameFrom']);

                $iCoordsPla = PropertyValueC::ensureInteger($result['iCoordsPlaFrom']);
                $iCoordsGal = PropertyValueC::ensureInteger($result['iCoordsGalFrom']);
                $iCoordsSol = PropertyValueC::ensureInteger($result['iCoordsSolFrom']);
                $aCoords    = array(
                    'coords_gal' => $iCoordsGal,
                    'coords_sol' => $iCoordsSol,
                    'coords_pla' => $iCoordsPla
                );
                $strCoords = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;

                $retObj->aCoordsFrom    = $aCoords;
                $retObj->strCoordsFrom  = $strCoords;
                $retObj->eTransfairType = PropertyValueC::ensureString($result['eTransfairType']);

                $iCoordsPla = PropertyValueC::ensureInteger($result['iCoordsPlaTo']);
                $iCoordsGal = PropertyValueC::ensureInteger($result['iCoordsGalTo']);
                $iCoordsSol = PropertyValueC::ensureInteger($result['iCoordsSolTo']);
                $aCoords    = array(
                    'coords_gal' => $iCoordsGal,
                    'coords_sol' => $iCoordsSol,
                    'coords_pla' => $iCoordsPla
                );
                $strCoords  = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;

                $retObj->aCoordsTo   = $aCoords;
                $retObj->strCoordsTo = $strCoords;

                if (!empty($result['dtDateTime'])) {
                    $retObj->iAnkunft = HelperC::convertDateTimeToTimestamp($result['dtDateTime']);
                }

                if (!empty($result['mtMixedTime'])) {
                    $retObj->iAnkunftIn = HelperC::convertMixedDurationToSeconds($result['mtMixedTime']);
                }

                if ($retVal->bObjectsVisible) {
                    if (isset($result['strObjecte'])) {
                        $aoResult = array();
                        $foRetVal = preg_match_all($this->getRegularExpressionObject(), $result['strObjecte'], $aoResult, PREG_SET_ORDER);
                        if ($foRetVal) {
                            foreach ($aoResult as $ores) {
                                $ores['iCount']     = PropertyValueC::ensureInteger($ores['iCount']);
                                $ores['strObject']  = PropertyValueC::ensureString($ores['strObject']);
                                $retObj->aObjects[] = array('count' => $ores['iCount'], 'object' => $ores['strObject']);
                            }
                        }
                    }
                }
                $retVal->aFleets[] = $retObj;

            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
            $parserResult->aErrors[]           = $msg->strParserText;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     */
    private function getRegularExpressionObject()
    {
        $reObject = $this->getRegExpSingleLineText3();
        $reCount  = $this->getRegExpDecimalNumber();

        $regExp = '/
        (?P<iCount>' . $reCount . ')
        \s+?
        (?P<strObject>' . $reObject . ')
        ';
        $regExp .= '/mxs';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getObjectsVisible()
    {
        $retVal = false;
        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getMsg()->strParserText, $aResult, PREG_SET_ORDER);

        $onlyReturn = true;
        if ($fRetVal !== false && $fRetVal > 0) {
            foreach ($aResult as $result) {
                if (!empty($result['strObjecte'])) {
                    $retVal = true;
                    break;
                } else if ($onlyReturn && $result["eTransfairType"] != "Rückkehr") { //! Mac: evtl noch andere Schiffaktionen ohne Infos ?
                    $onlyReturn = false;
                }
            }
        }

        if ($onlyReturn) {
            return true;
        }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     */
    private function getRegularExpression()
    {
        $rePlanetName    = $this->getRegExpSingleLineText();
        $reUserName      = $this->getRegExpUserName();
        $reDateTime      = $this->getRegExpDateTime();
        $reCoordsUnnamed = $this->getRegExpKoloCoords();
        $reMixedTime     = $this->getRegExpMixedTime();
        $reShipActions   = $this->getRegExpShipActions();
        $reShipTexts     = $this->getRegExpShipTexts(); //! Mac: zufaellige Texte nach Ankunft
        $reShipNames     = $this->getRegExpSingleLineText3();
        $reObject        = '(?:' . $this->getRegExpResource() . '|' . $reShipNames . '|' . $reShipTexts . ')';
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExpOpera = '(?:\s+(?:(?:' . $reDecimalNumber . '\s+?' . $reObject . '\s*)+)\s*)?'; //! Opera kopiert die Objects nochmal ... warum auch immer oO

        $regExp = '/';
        $regExp .= '(?P<strPlanetNameTo>'.$rePlanetName.'\s|)';
        $regExp .= '[\s\n]+';
        $regExp .= '\((?P<iCoordsGalTo>\d{1,2})\:(?P<iCoordsSolTo>\d{1,3})\:(?P<iCoordsPlaTo>\d{1,2})\)';
        $regExp .= '[\s\n]+';
        $regExp .= '(?:\(\s*via\s+'.$reCoordsUnnamed.'\s+via\s+'.$reCoordsUnnamed.'\s\)|)';
        $regExp .= '\s*';
        $regExp .= '(?:(?P<strPlanetNameFrom>' . $rePlanetName . ')\s|)';
        $regExp .= '\((?P<iCoordsGalFrom>\d+)\:(?P<iCoordsSolFrom>\d+)\:(?P<iCoordsPlaFrom>\d+)\)';
        $regExp .= '[\s\n]+';

        $regExp .= '(?:(?P<strUserNameFrom>^' . $reUserName . ')|)';

        $regExp .= '\s*';
        $regExp .= '(?:';
        $regExp .= '    (?P<dtDateTime>' . $reDateTime . ')\s*(?: - \s*(?P<mtMixedTime>' . $reMixedTime . ')?)?';
        $regExp .= '    (?:\s*(?:\(?angekommen\)?|' . $reMixedTime . ')\s*)?' . $regExpOpera . '(?=[\s\n]+' . $reShipActions . ')'
            . '   |' . $reObject . '\s*-?\s\(?angekommen\)?' . $regExpOpera . '(?=[\s\n]+' . $reShipActions . ')' //! bei Angriff: beliebiger Text + angekommen
            . '   |' . $reObject . '\s*' . $regExpOpera . '(?=[\s\n]+' . $reShipActions . ')' //! nach Ankunft: beliebiger Text
            . ')';
        $regExp .= '[\s\n]+';
        $regExp .= '(?P<eTransfairType>' . $reShipActions . ')';
        $regExp .= '([\s\n]+';
        $regExp .= 'Es\sgibt\s\d+\sweitere\sFlotten\smit\sder\sselben\sAktion,\sselben\sZiel\sund\sselben\sAuftrag\.';
        $regExp .= '[\s\n]+)?';
        $regExp .= '(\s+(?<!Rückkehr\s)';
        $regExp .= '    (?P<strObjecte>(?:' . $reDecimalNumber . '\s+?' . $reObject . '\s*?)+)\s*(?:\*\s\+|\+)';
        $regExp .= '    |(?:\*\s\+|\+)';
        $regExp .= ')?';
        $regExp .= '/mxs';

        return $regExp;
    }

}