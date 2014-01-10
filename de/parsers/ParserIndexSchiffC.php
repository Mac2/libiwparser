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
 * @author Mac <MacXY@herr-der-mails.de>
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'ParserBaseC.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'ParserI.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'HelperC.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'parserResults'   . DIRECTORY_SEPARATOR .
              'DTOParserMsgResultC.php' );



/**
 * Parser for Mainpage
 *
 * This parser is responsible for the Werft section on the Mainpage
 *
 * Its identifier: de_index_schiff
 */
class ParserIndexSchiffC extends ParserMsgBaseC implements ParserMsgI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_index_schiff');
    $this->setCanParseMsg('Schiff');
  }

 /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserMsgI::parseMsg()
   */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserIndexSchiffResultC();
        $retVal                      =& $parserResult->objResultData;

        $regExp = $this->getRegularExpression();
        $msg    = $this->getMsg();

        $parserResult->strIdentifier = 'de_index_schiff';

        $aResult = array();

        $fRetVal = preg_match_all($regExp, $msg->strParserText, $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                if (!empty($result['iCoordsPla']) && !empty($result['iCoordsGal']) && !empty($result['iCoordsSol'])) {
                    $iCoordsPla    = PropertyValueC::ensureInteger($result['iCoordsPla']);
                    $iCoordsGal    = PropertyValueC::ensureInteger($result['iCoordsGal']);
                    $iCoordsSol    = PropertyValueC::ensureInteger($result['iCoordsSol']);
                    $strCoords     = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;
                    $strPlanetName = PropertyValueC::ensureString($result['strPlanetName']);
                }

                $retObj1 = new DTOParserIndexSchiffResultSchiffC();
                $retObj2 = new DTOParserIndexSchiffResultWerftBelegtC();

                if (!empty($strPlanetName)) {
                    $retObj1->strPlanetName = $strPlanetName;
                }
                $retObj1->strSchiffName = PropertyValueC::ensureString($result['strSchiffName']);
                $retObj1->iSchiffEnd    = HelperC::convertDateTimeToTimestamp($result['dtDateTime']);
                if (!empty($result['mtMixedTime'])) {
                    $retObj1->iSchiffEndIn = HelperC::convertMixedTimeToTimestamp($result['mtMixedTime']);
                }
                $retObj1->iAnzSchiff  = PropertyValueC::ensureInteger($result['iAnzahlSchiff']);
                $retObj1->iAnzWerften = PropertyValueC::ensureInteger($result['iAnzahlWerft']);
                $retObj1->strWerftTyp = PropertyValueC::ensureString($result['strWerftName']);

                if (!empty($strCoords)) {
                    if (!empty($retObj1->strSchiffName)) {
                        $retObj1->strCoords = $strCoords;
                        if (isset($retVal->aSchiff[$strCoords][$retObj1->strSchiffName])) {
                            $exist = false;
                            foreach ($retVal->aSchiff[$strCoords][$retObj1->strSchiffName] as $ship) {
                                if ($ship->iSchiffEnd == $retObj1->iSchiffEnd) {
                                    $exist = true;
                                    $ship->iAnzSchiff += $retObj1->iAnzSchiff;
                                    $ship->iAnzWerften += $retObj1->iAnzWerften;
                                }
                            }
                            if (!$exist) {
                                $retVal->aSchiff[$strCoords][$retObj1->strSchiffName][] = $retObj1;
                            }
                        } else {
                            $retVal->aSchiff[$strCoords][$retObj1->strSchiffName][] = $retObj1;
                        }
                    }

                    if (!empty($retObj1->strWerftTyp)) {
                        $retObj2->strWerftTyp = $retObj1->strWerftTyp;
                        $retObj2->iAnzWerften = $retObj1->iAnzWerften;

                        $retObj2->strSchiffName = $retObj1->strSchiffName;
                        $retObj2->iAnzSchiff    = $retObj1->iAnzSchiff;

                        $retObj2->iSchiffEnd   = $retObj1->iSchiffEnd;
                        $retObj2->iSchiffEndIn = $retObj1->iSchiffEndIn;

                        $retVal->aWerftBelegt[$strCoords][$retObj2->strWerftTyp][] = $retObj2;
                    }

                }

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
  private function getRegularExpression()
  {
    $rePlanetName       = $this->getRegExpSingleLineText();
    $reDateTime         = $this->getRegExpDateTime();
    $reMixedTime         = $this->getRegExpMixedTime();

    $regExp = '/';
    $regExp .= '(\[(?P<iCoordsGal>\d+)\:(?P<iCoordsSol>\d+)\:(?P<iCoordsPla>\d+)\]';
    $regExp .= '\s)?';
    $regExp .= '(?P<strPlanetName>'.$rePlanetName.')';
    $regExp .= '\s+';
    $regExp  .= '^(?P<iAnzahlSchiff>\d+)';
    $regExp .= '\s+';
    $regExp .= '(?P<strSchiffName>'.$rePlanetName.')';
    $regExp .= '\s+';
    $regExp .= '(?P<iAnzahlWerft>\d+)';
    $regExp .= '\s+';
    $regExp .= '(?P<strWerftName>'.$rePlanetName.')';
    $regExp .= '\s+bis\s';
    $regExp .= '(?P<dtDateTime>'.$reDateTime.')';
    $regExp .= '(\s(-\s)?';
    $regExp .= '(?P<mtMixedTime>'.$reMixedTime.'))?';
    $regExp .= '/mxs';

    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * For debugging with "The Regex Coach" which doesn't support named groups
   */
  private function getRegularExpressionWithoutNamedGroups()
  {
    $retVal = $this->getRegularExpression();

    $retVal = preg_replace( '/\?P<\w+>/', '', $retVal );

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
