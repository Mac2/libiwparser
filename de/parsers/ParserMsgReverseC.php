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
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for SubMessages Rückkehr
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_reverse
 */
class ParserMsgReverseC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_msg_reverse');
        $this->setCanParseMsg('Rückkehr');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserMsgResultMsgReverseC();
        $retVal =& $parserResult->objResultData;

        $regExpText         = $this->getRegularExpressionText();
        $msg                = $this->getMsg();
        $msg->strParserText = trim($msg->strParserText);

        foreach ($msg as $key => $value) {
            $retVal->$key = $value;
        }

        if (empty($msg->strParserText)) { //! Mac: leerer Input, evtl. nicht ausgeklappt ?
            $retVal->bSuccessfullyParsed = false;
            $retVal->aErrors[]           = 'Leere Nachricht, nicht ausgeklappt?';

            return;
        }

        $aResultText = array();
        $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

        if ($fRetValText !== false && $fRetValText > 0) {
            $parserResult->bSuccessfullyParsed = true;

            $aSchiffe      = array();
            $aResources    = array();

            $strPlanetName = $aResultText['planet_name'];
            $strCoords     = $aResultText['coords'];
            $iCoordsGal    = PropertyValueC::ensureInteger($aResultText['coords_gal']);
            $iCoordsSol    = PropertyValueC::ensureInteger($aResultText['coords_sol']);
            $iCoordsPla    = PropertyValueC::ensureInteger($aResultText['coords_pla']);
            $aCoords       = array(
                'coords_gal' => $iCoordsGal,
                'coords_sol' => $iCoordsSol,
                'coords_pla' => $iCoordsPla
            );

            if (isset($aResultText['schiffe'])) {
                $aResultSchiffe = array();
                $regExpSchiffe  = $this->getRegularExpressionSchiffe();
                $fRetValSchiffe = preg_match_all($regExpSchiffe, $aResultText['schiffe'], $aResultSchiffe, PREG_SET_ORDER);

                if ($fRetValSchiffe !== false && $fRetValSchiffe > 0) {
                    foreach ($aResultSchiffe as $result) {
                        $strSchiffName                 = $result['schiff_name'];
                        $iSchiffCount                  = $result['schiffe_count'];
                        $strSchiffName                 = PropertyValueC::ensureString($strSchiffName);
                        $iSchiffCount                  = PropertyValueC::ensureInteger($iSchiffCount);
                        $aSchiffe[md5($strSchiffName)] = array(
                            'schiffe_name'  => $strSchiffName,
                            'schiffe_count' => $iSchiffCount
                        );
                    }
                }
            }
            if (isset($aResultText['resources'])) {
                $aResultResources = array();
                $regExpResources  = $this->getRegularExpressionResources();
                $fRetValResources = preg_match_all($regExpResources, $aResultText['resources'], $aResultResources, PREG_SET_ORDER);

                if ($fRetValResources !== false && $fRetValResources > 0) {
                    foreach ($aResultResources as $result) {
                        $strResourceName                   = $result['resource_name'];
                        $iResourceCount                    = $result['resource_count'];
                        $strResourceName                   = PropertyValueC::ensureEnum($strResourceName, 'eResources');
                        $iResourceCount                    = PropertyValueC::ensureInteger($iResourceCount);
                        $aResources[md5($strResourceName)] = array(
                            'resource_name'  => $strResourceName,
                            'resource_count' => $iResourceCount
                        );
                    }
                }
            }

            $retVal->strPlanetName = PropertyValueC::ensureString($strPlanetName);
            $retVal->strCoords     = PropertyValueC::ensureString($strCoords);
            $retVal->aCoords       = $aCoords;
            $retVal->aSchiffe      = $aSchiffe;
            $retVal->aResources    = $aResources;
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the ReverseMsg pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionText()
    {

        $reSchiffe  = $this->getRegExpSchiffe();

        #Just even don't think to ask anything about this regexp, fu!
        $regExp = '/
        Eine\sFlotte\s(ist\szu|wurde\sauf)\sdem\sPlaneten
        (?:\s(?P<planet_name>.*)\s|\s)
        (?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))
        \s(zur.{1,3}ckgekehrt|stationiert)\.
        [\s\n]+
        (Es\skamen\sfolgende\sSachen\szur.{1,3}ck|Es\swurden\sfolgende\sSachen\sstationiert)
        [\s\n]+
        (?:
        Schiffe
        [\s\n]+
        (?P<schiffe>
        (' . $reSchiffe . '\s+\d+[\s\n]*)+
        )
        |)
        (?:
        Ressourcen
        [\s\n]+
        (?P<resources>
        ([\w\süöä]+\s+\d+[\s\n]*)+
        )
        |)

        /mx';

        return $regExp;
    }

}