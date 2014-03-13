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

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for Mainpage
 *
 * This parser is responsible for the Kolonieinfo section on the Mainpage
 *
 * Its identifier: de_index_koloinfos
 */
class ParserIndexKoloInfosC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_index_koloinfos');
        $this->setCanParseMsg('KoloInfos');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserIndexKoloInfosResultC();
        $retVal =& $parserResult->objResultData;

        $parserResult->strIdentifier = 'de_index_koloinfos';

        $msg = $this->getMsg();

        $retObj    = new DTOParserIndexKoloInfosResultKoloInfoC();

        $parserResult->bSuccessfullyParsed = true;

        // ### Part1 : Allgemeine Infos ###

        $regExp = $this->getRegularExpressionKoloinfoPart1();

        $aResult = array();
        $fRetVal = preg_match($regExp, $msg->strParserText, $aResult);
        if ($fRetVal !== false && $fRetVal > 0) {

            if (!empty($aResult['strPlanetName'])) {
                $retObj->strPlanetName = PropertyValueC::ensureString($aResult['strPlanetName']);
                $iCoordsPla            = PropertyValueC::ensureInteger($aResult['iCoordsPla']);
                $iCoordsGal            = PropertyValueC::ensureInteger($aResult['iCoordsGal']);
                $iCoordsSol            = PropertyValueC::ensureInteger($aResult['iCoordsSol']);
                $aCoords               = array(
                    'coords_gal' => $iCoordsGal,
                    'coords_sol' => $iCoordsSol,
                    'coords_pla' => $iCoordsPla
                );
                $strCoords             = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;

                $retObj->aCoords   = $aCoords;
                $retObj->strCoords = $strCoords;

                if (isset($aResult['strKoloTyp'])) {
                    $retObj->strObjectTyp = PropertyValueC::ensureString($aResult['strKoloTyp']);
                }

                $lastScan = array();
                if (isset($aResult['dtLastScan'])) {
                    $lastScan['datetime'] = HelperC::convertDateTimeToTimestamp($aResult['dtLastScan']);
                }

                if (isset($aResult['strScanUsername'])) {
                    $lastScan['username'] = PropertyValueC::ensureString($aResult['strScanUsername']);
                }
                $retObj->aLastScan = $lastScan;

                if (isset($aResult['mtScanRange'])) {
                    $retObj->aScanRange = HelperC::convertMixedDurationToSeconds($aResult['mtScanRange']);
                }

                if (isset($aResult['iLB'])) {
                    $retObj->iLB = PropertyValueC::ensureInteger($aResult['iLB']);
                }

                if (isset($aResult['aktKolo'])) {
                    $obj           = array(
                        'akt' => PropertyValueC::ensureInteger($aResult['aktKolo']),
                        'max' => PropertyValueC::ensureInteger($aResult['maxKolo'])
                    );
                    $retObj->aKolo = $obj;
                }

                if (isset($aResult['aktKB'])) {
                    $obj         = array(
                        'akt' => PropertyValueC::ensureInteger($aResult['aktKB']),
                        'max' => PropertyValueC::ensureInteger($aResult['maxKB'])
                    );
                    $retObj->aKB = $obj;
                }

                if (isset($aResult['aktAB'])) {
                    $obj         = array(
                        'akt' => PropertyValueC::ensureInteger($aResult['aktAB']),
                        'max' => PropertyValueC::ensureInteger($aResult['maxAB'])
                    );
                    $retObj->aAB = $obj;
                }

                if (isset($aResult['aktSB'])) {
                    $obj         = array(
                        'akt' => PropertyValueC::ensureInteger($aResult['aktSB']),
                        'max' => PropertyValueC::ensureInteger($aResult['maxSB'])
                    );
                    $retObj->aSB = $obj;
                }
            }

        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the de_index_koloinfos part1 pattern.';
        }

        // ### Part2 : Kolonieprobleme ###

        $regExp = $this->getRegularExpressionKoloinfoPart2();

        $aResult = array();
        $fRetVal = preg_match($regExp, $msg->strParserText, $aResult);
        if ($fRetVal !== false && $fRetVal > 0) {

            if (!empty($aResult['problems'])) {
                $retObj->strProblems = $aResult['problems'];
            }

        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the de_index_koloinfos part2 pattern.';
        }

        // ### Part3 : Schiffe und Deff ###

        $regExp = $this->getRegularExpressionKoloinfoShips();
        $fRetVal = preg_match($regExp, $msg->strParserText, $aResult);
        if ($fRetVal !== false && $fRetVal > 0) {
            if (!empty($aResult['strShipData'])) {

                $regExp = $this->getRegularExpressionKoloinfoShips2();
                $fRetVal = preg_match_all($regExp, $aResult['strShipData'], $aResult2, PREG_SET_ORDER);
                if ($fRetVal !== false && $fRetVal > 0) {
                    foreach ($aResult2 as $result) {
                        $retObj->aSchiffe[] = array(
                            'object' => PropertyValueC::ensureString(trim($result['strObject'])),
                            'count'  => PropertyValueC::ensureInteger($result['iCount']),
                        );
                    }
                }

            }
        }

        $regExp = $this->getRegularExpressionKoloinfoDefence();
        $fRetVal = preg_match($regExp, $msg->strParserText, $aResult);
        if ($fRetVal !== false && $fRetVal > 0) {
            if (!empty($aResult['strDeffData'])) {

                $foRetVal = preg_match_all($this->getRegularExpressionObject(), $aResult['strDeffData'], $aoResult, PREG_SET_ORDER);
                if ($foRetVal) {
                    foreach ($aoResult as $ores) {
                        $retObj->aPlanDeff[] = array(
                            'object' => PropertyValueC::ensureString(trim($ores['strObject'])),
                            'count'  => PropertyValueC::ensureInteger($ores['iCount'])
                        );
                    }
                }

            }
        }

        $regExp = $this->getRegularExpressionKoloinfoProbeDefence();
        $fRetVal = preg_match($regExp, $msg->strParserText, $aResult);
        if ($fRetVal !== false && $fRetVal > 0) {
            if (!empty($aResult['strProbeDeffData'])) {

                $foRetVal = preg_match_all($this->getRegularExpressionObject(), $aResult['strProbeDeffData'], $aoResult, PREG_SET_ORDER);
                if ($foRetVal) {
                    foreach ($aoResult as $ores) {
                        $retObj->aPlanDeff[] = array(
                            'object' => PropertyValueC::ensureString(trim($ores['strObject'])),
                            'count'  => PropertyValueC::ensureInteger($ores['iCount'])
                        );
                    }
                }

            }
        }

        if ($parserResult->bSuccessfullyParsed) {
            $retVal->aKolos[] = $retObj;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionObject()
    {
        $reObject = $this->getRegExpSingleLineText3();
        $reCount  = $this->getRegExpDecimalNumber();

        $regExp  = '/';
        $regExp .= '(?P<strObject>' . $reObject . ')';
        $regExp .= '\s+?';
        $regExp .= '(?P<iCount>' . $reCount . ')';
        $regExp .= '/mxs';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoPart1()
    {
        $rePlanetName = $this->getRegExpSingleLineText();
        $reDateTime   = $this->getRegExpDateTime();
        $reMixedTime  = $this->getRegExpMixedTime();
        $reCount      = '\d+';
        $reUserName   = $this->getRegExpUserName();
        $reKoloType   = $this->getRegExpKoloTypes();

        $regExp = '/';

        $regExp .= '(?P<strKoloTyp>' . $reKoloType . ')';
        $regExp .= '\s+';
        $regExp .= '(?P<strPlanetName>' . $rePlanetName . ')';
        $regExp .= '\s+';
        $regExp .= '\((?P<iCoordsGal>\d{1,2})\:(?P<iCoordsSol>\d{1,3})\:(?P<iCoordsPla>\d{1,2})\)';
        $regExp .= '(?:';
        $regExp .= ' \n+';
        $regExp .= ' Lebensbedingungen\s+(?P<iLB>' . $reCount . ')\s\%\n';
        $regExp .= ' Flottenscannerreichweite\s+\(normal\)\s+(?P<mtScanRange>' . $reMixedTime . ')\n';
        $regExp .= ' (?:';
        $regExp .= '  Letzter\s+erfolgreicher\s+Feindscan\s+am\s+';
        $regExp .= '  (?P<dtLastScan>' . $reDateTime . ')';
        $regExp .= '  \s+von\s+';
        $regExp .= '  (?P<strScanUsername>' . $reUserName . ')\s*';
        $regExp .= '  |\s*';
        $regExp .= ' )';
        $regExp .= ' \s+Kolonien\s+aktuell\s+\/\s+maximal';
        $regExp .= ' \s';
        $regExp .= ' (?P<aktKolo>' . $reCount . ')';
        $regExp .= ' \s\/\s';
        $regExp .= ' (?P<maxKolo>' . $reCount . ')';
        $regExp .= ' (?:';
        $regExp .= '  \s+aufgebaute\s+Kampfbasen\s+aktuell\s+\/\s+maximal';
        $regExp .= '  \s+';
        $regExp .= '  (?P<aktKB>' . $reCount . ')';
        $regExp .= '  \s+\/\s+';
        $regExp .= '  (?P<maxKB>' . $reCount . ')';
        $regExp .= ' )?';
        $regExp .= ' (?:';
        $regExp .= '  \s+aufgebaute\sRessbasen\saktuell\s\/\smaximal';
        $regExp .= '  \s+';
        $regExp .= '  (?P<aktSB>' . $reCount . ')';
        $regExp .= '  \s+\/\s+';
        $regExp .= '  (?P<maxSB>' . $reCount . ')';
        $regExp .= ' )?';
        $regExp .= ' (?:';
        $regExp .= '  \s+aktuelle\s\/\smaximale\saufgebaute\sArtefaktbasen';
        $regExp .= '  \s+';
        $regExp .= '  (?P<aktAB>' . $reCount . ')';
        $regExp .= '  \s+\/\s+';
        $regExp .= '  (?P<maxAB>' . $reCount . ')';
        $regExp .= ' )?';
        $regExp .= ')?';

        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoPart2()
    {
        $reDateTime   = $this->getRegExpDateTime();
        $reDuration   = $this->getRegExpMixedDuration();
        $reProblem    = $this->getRegExpPlanetaryProblems();

        $regExp = '/';

        $regExp .= '(?:Dauer\sder\sRunde\s+' . $reDuration . '\s+)';
        $regExp .= '(?:Noobstatus\sBis\:\s(?P<noobstatus>(' . $reDateTime . '))\s\(' . $reDuration . '\)\s*)?';
        $regExp .= '(Probleme\s+';
        $regExp .= ' (?P<problems>';
        $regExp .= '  (?:';
        $regExp .= '   ^' . $reProblem . '\s+';
        $regExp .= '  )+';
        $regExp .= ' )';
        $regExp .= ')?';

        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoShips()
    {
        $reObject     = $this->getRegExpSingleLineText3();
        $reCount      = $this->getRegExpDecimalNumber();

        $regExp = '/';

        $regExp .= 'Schiffsübersicht\s*';
        $regExp .= '(?P<strShipData>';
        $regExp .= ' (?:';
        $regExp .= '  (?:'.$reObject.'\s*\n)';
        $regExp .= '  (?:'.$reObject.'\s*'.$reCount.'\n?)+';
        $regExp .= ' )+?';
        $regExp .= ')';
        $regExp .= '\s*(?:Verteidigungsübersicht|Sondenverteidigungsübersicht|Forschungsstatus)\s*';

        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoShips2()
    {
        $reObject     = $this->getRegExpSingleLineText3();
        $reCount      = $this->getRegExpDecimalNumber();

        $regExp = '/';

        $regExp .= '(?:'.$reObject.'\s*\n)?';
        $regExp .= '(?P<strObject>'.$reObject.')\s*(?P<iCount>'.$reCount.')\n?';

        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoDefence()
    {
        $reObject     = $this->getRegExpSingleLineText3();
        $reCount      = $this->getRegExpDecimalNumber();

        $regExp = '/';

        $regExp .= 'Verteidigungsübersicht\s*';
        $regExp .= '(?P<strDeffData>';
        $regExp .= ' (?:'.$reObject.'\s*'.$reCount.'\n?)+';
        $regExp .= ')';

        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKoloinfoProbeDefence()
    {
        $reObject     = $this->getRegExpSingleLineText3();
        $reCount      = $this->getRegExpDecimalNumber();

        $regExp = '/';

        $regExp .= 'Sondenverteidigungsübersicht\s*';
        $regExp .= '(?P<strProbeDeffData>';
        $regExp .= ' (?:'.$reObject.'\s*'.$reCount.'\n?)+';
        $regExp .= ')';

        $regExp .= '/mx';

        return $regExp;
    }

}