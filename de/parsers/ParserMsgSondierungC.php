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
use libIwParsers\de\parserResults\DTOParserMsgResultMsgSondierungC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for SubMessages Sondierungen
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_sondierungen
 */
class ParserMsgSondierungC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_msg_sondierungen');
        $this->setCanParseMsg('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserMsgResultMsgSondierungC();
        $retVal =& $parserResult->objResultData;

        $regExpText = $this->getRegularExpressionText();
        $msg = $this->getMsg();

        foreach ($msg as $key => $value) {
            $retVal->$key = $value;
        }

        $aResultText = array();
        $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

        if ($fRetValText !== false && $fRetValText > 0) {

            $retVal->bSuccessfullyParsed = true;

            $aResultTitle = array();
            $fRetValTitle = preg_match($this->getRegularExpressionTitle(), $msg->strMsgTitle, $aResultTitle);
            if ($fRetValTitle !== false && $fRetValTitle > 0) {
                $c = explode(":", $aResultTitle['coords_to']);
                $retVal->strCoordsTo = $aResultTitle['coords_to'];

                $iCoordsGal = PropertyValueC::ensureInteger($c[0]);
                $iCoordsSol = PropertyValueC::ensureInteger($c[1]);
                $iCoordsPla = PropertyValueC::ensureInteger($c[2]);
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsTo = $aCoords;
                if ($aResultTitle['status'] == 'Eigener Planet wurde sondiert') {
                    $retVal->bSuccess = true;
                } else {
                    $retVal->bSuccess = false;
                }
            }

            if (!empty($aResultText['ally1'])) {
                $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally1']);
                $c = explode(":", $aResultText['coords1']);
                $retVal->strCoordsFrom = $aResultText['coords1'];

                $iCoordsGal = PropertyValueC::ensureInteger($c[0]);
                $iCoordsSol = PropertyValueC::ensureInteger($c[1]);
                $iCoordsPla = PropertyValueC::ensureInteger($c[2]);
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsFrom = $aCoords;
            } else if (!empty($aResultText['ally2'])) {
                $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally2']);
                $c = explode(":", $aResultText['coords2']);
                $retVal->strCoordsFrom = $aResultText['coords2'];

                $iCoordsGal = PropertyValueC::ensureInteger($c[0]);
                $iCoordsSol = PropertyValueC::ensureInteger($c[1]);
                $iCoordsPla = PropertyValueC::ensureInteger($c[2]);
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsFrom = $aCoords;
            } else {
                $c = "";
                if (!empty($aResultText['coords1']) && $aResultText['coords1'] != $retVal->strCoordsTo) {
                    $c = $aResultText['coords1'];
                } else if (!empty($aResultText['coords2']) && $aResultText['coords2'] != $retVal->strCoordsTo) {
                    $c = $aResultText['coords2'];
                }

                $retVal->strCoordsFrom = $c;
                $c = explode(":", $c);
                $iCoordsGal = PropertyValueC::ensureInteger($c[0]);
                $iCoordsSol = PropertyValueC::ensureInteger($c[1]);
                $iCoordsPla = PropertyValueC::ensureInteger($c[2]);
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsFrom = $aCoords;
            }
        } else {
            $retVal->bSuccessfullyParsed = false;
            $retVal->aErrors[] = 'Unable to match the pattern (Sondierungen).';
            $retVal->aErrors[] = '...' . $msg->strParserText;
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     */

    private function getRegularExpressionTitle()
    {
        $reCoords = $this->getRegExpKoloCoords();

        $regExp = '/';
        $regExp .= '(?P<status>Sondierung\svereitelt|Eigener\sPlanet\swurde\ssondiert)\s+(?:\((?P<coords_to>' . $reCoords . ')\))';
        $regExp .= '/s';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     */

    private function getRegularExpressionText()
    {

        $reCoords = $this->getRegExpKoloCoords();
        $reAlliance = $this->getRegExpSingleLineText();
//    $reText         = $this->getRegExpSingleLineText3();

        $regExp = '/';
        $regExp .= '(?P<name1>' . $reAlliance . ')';
        $regExp .= '   (?:\s+\[(?P<ally1>' . $reAlliance . ')\])?';
        $regExp .= '   \s+(?:\((?P<coords1>' . $reCoords . ')\))';
        $regExp .= '\s*';
        $regExp .= '(?:[^\[\]]+)?';
        $regExp .= '(?P<name2>' . $reAlliance . ')';
        $regExp .= '   (?:\s*\[(?P<ally2>' . $reAlliance . ')\])?';
        $regExp .= '   \s+(?:\((?P<coords2>' . $reCoords . ')\))';
        $regExp .= '/sxU';

        return $regExp;
    }

}