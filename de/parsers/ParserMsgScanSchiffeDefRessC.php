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
 * Parser for SubMessages Sondierung (Schiffe/Def/Ress)
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_ScanSchiffeDefRess
 */
class ParserMsgScanSchiffeDefRessC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_msg_scan_schiffedefress');
        $this->setCanParseMsg('Sondierung (Schiffe/Def/Ress)');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserMsgResultMsgScanSchiffeDefRessC();
        $retVal =& $parserResult->objResultData;

        $regExpText = $this->getRegularExpressionText();
        $msg        = $this->getMsg();

        foreach ($msg as $key => $value) {
            $retVal->$key = $value;
        }

        $aResultText = array();
        $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

        if ($fRetValText !== false && $fRetValText > 0) {

            $retVal->bSuccessfullyParsed = true;
            $aSchiffe     = array();
            $astatSchiffe = array();
            $aResources   = array();
            $aDefence     = array();

            $strCoords    = $aResultText['coords'];
            $strOwner     = $aResultText['owner'];
            $strOwnerAlly = $aResultText['alliance'];
            $strPlanetTyp = $aResultText['planettyp'];
            $strObjektTyp = $aResultText['objekttyp'];

            $iCoordsGal = PropertyValueC::ensureInteger($aResultText['coords_gal']);
            $iCoordsSol = PropertyValueC::ensureInteger($aResultText['coords_sol']);
            $iCoordsPla = PropertyValueC::ensureInteger($aResultText['coords_pla']);
            $aCoords    = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);

            if (isset($aResultText['schiffe'])) {
                $aResultSchiffe = array();
                $regExpSchiffe  = $this->getRegularExpressionSchiffe();
                $fRetValSchiffe = preg_match_all($regExpSchiffe, $aResultText['schiffe'], $aResultSchiffe, PREG_SET_ORDER);

                if ($fRetValSchiffe !== false && $fRetValSchiffe > 0) {
                    foreach ($aResultSchiffe as $result) {
                        $strSchiffName = $result['schiff_name'];
                        $iSchiffCount  = $result['schiffe_count'];
                        $strSchiffName = PropertyValueC::ensureString($strSchiffName);
                        $iSchiffCount  = PropertyValueC::ensureInteger($iSchiffCount);
                        if ($strSchiffName == "-???-") {
                            continue;
                        }
                        $aSchiffe[$strSchiffName] = $iSchiffCount;
                    }
                }
            }
            if (isset($aResultText['stat_fleet'])) {
                $aResultStatSchiffe = array();
                $regExpStatSchiffe  = $this->getRegularExpressionStatSchiffe();
                $fRetValStatSchiffe = preg_match_all($regExpStatSchiffe, $aResultText['stat_fleet'], $aResultStatSchiffe, PREG_SET_ORDER);

                if ($fRetValStatSchiffe !== false && $fRetValStatSchiffe > 0) {
                    foreach ($aResultStatSchiffe as $resultStat) {
                        $aResultSchiffe = array();
                        $regExpSchiffe  = $this->getRegularExpressionSchiffe();
                        $fRetValSchiffe = preg_match_all($regExpSchiffe, $resultStat['stat_fleet'], $aResultSchiffe, PREG_SET_ORDER);
                        $statOwner      = $resultStat['owner_stat'];

                        if ($fRetValSchiffe !== false && $fRetValSchiffe > 0) {
                            foreach ($aResultSchiffe as $result) {
                                $strSchiffName = $result['schiff_name'];
                                $iSchiffCount  = $result['schiffe_count'];
                                $strSchiffName = PropertyValueC::ensureString($strSchiffName);
                                $iSchiffCount  = PropertyValueC::ensureInteger($iSchiffCount);
                                if ($strSchiffName == "-???-") {
                                    continue;
                                }
                                $astatSchiffe[$statOwner][$strSchiffName] = $iSchiffCount;
                            }
                        }
                    }
                }
            }
            if (isset($aResultText['defence'])) {
                $aResultDefence = array();
                $regExpDefence  = $this->getRegularExpressionDefence();
                $fRetValDefence = preg_match_all($regExpDefence, $aResultText['defence'], $aResultDefence, PREG_SET_ORDER);

                if ($fRetValDefence !== false && $fRetValDefence > 0) {
                    foreach ($aResultDefence as $result) {
                        $strDefenceName = $result['defence_name'];
                        $iDefenceCount  = $result['defence_count'];
                        $strDefenceName = PropertyValueC::ensureString($strDefenceName);
                        $iDefenceCount  = PropertyValueC::ensureInteger($iDefenceCount);
                        if ($strDefenceName == "-???-") {
                            continue;
                        }
                        $aDefence[$strDefenceName] = $iDefenceCount;
                    }
                }
            }
            if (isset($aResultText['resources'])) {
                $aResultResources = array();
                $regExpResources  = $this->getRegularExpressionResources();
                $fRetValResources = preg_match_all($regExpResources, $aResultText['resources'], $aResultResources, PREG_SET_ORDER);

                if ($fRetValResources !== false && $fRetValResources > 0) {
                    foreach ($aResultResources as $result) {
                        $strResourceName = $result['resource_name'];
                        $iResourceCount  = $result['resource_count'];
                        $strResourceName = PropertyValueC::ensureEnum($strResourceName, 'eResources');
                        $iResourceCount  = PropertyValueC::ensureInteger($iResourceCount);
                        if ($strResourceName == "-???-") {
                            continue;
                        }
                        $aResources[$strResourceName] = $iResourceCount;
                    }
                }
            }

            $retVal->strOwnerName        = PropertyValueC::ensureString($strOwner);
            $retVal->strOwnerAllianceTag = PropertyValueC::ensureString($strOwnerAlly);
            $retVal->strCoords           = PropertyValueC::ensureString($strCoords);
            $retVal->iTimestamp          = HelperC::convertDateTimeToTimestamp($aResultText['datetime']);

            $retVal->ePlanetType = PropertyValueC::ensureString($strPlanetTyp);
            $retVal->eObjectType = PropertyValueC::ensureString($strObjektTyp);

            $retVal->aCoords      = $aCoords;
            $retVal->aSchiffe     = $aSchiffe;
            $retVal->astatSchiffe = $astatSchiffe;
            $retVal->aResources   = $aResources;
            $retVal->aDefences    = $aDefence;
        } else {
            $retVal->bSuccessfullyParsed = false;
            $retVal->aErrors[]           = 'Unable to match the de_msg_scan_schiffedefress pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegularExpressionResources()
    {
        $reResource      = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp  = '/';
        $regExp .= '(?P<resource_name>(' . $reResource . '|-\?\?\?-))';
		$regExp .= '\s+';
        $regExp .= '(?P<resource_count>(' . $reDecimalNumber . '|-\?\?\?-))';
		$regExp .= '[\s\n]*';
        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegularExpressionSchiffe()
    {
        $reSchiffe       = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp  = '/';
        $regExp .= '(?P<schiff_name>' . $reSchiffe . ')';
		$regExp .= '\s+';
        $regExp .= '(?P<schiffe_count>(' . $reDecimalNumber . '|-\?\?\?-))';
		$regExp .= '[\s\n]*';
        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegularExpressionDefence()
    {
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reDefence       = $this->getRegExpSingleLineText3();

        $regExp = '/';
        $regExp .= '(?P<defence_name>(' . $reDefence . '|-\?\?\?-))';
		$regExp .= '\s+';
        $regExp .= '(?P<defence_count>(' . $reDecimalNumber . '|-\?\?\?-))';
		$regExp .= '[\s\n]*';
        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegularExpressionStatSchiffe()
    {
        $reSchiffe       = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reUserName      = $this->getRegExpUserName();

        $regExp = '/';
        $regExp .= '	Stationierte\sFlotte\svon\s(?P<owner_stat>(' . $reUserName . '|-\?\?\?-))\s\((' . $reDecimalNumber . '|-\?\?\?-)\sSchiffe\)';
		$regExp .= '	[\s\n]+';
        $regExp .= '	(?P<stat_fleet>(';
		$regExp .= '	((' . $reSchiffe . '|-\?\?\?-)\s+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n]*)+';
        $regExp .= '	))';
        $regExp .= '/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionText()
    {
        $reUserName      = $this->getRegExpUserName();
        $reMixedTime     = $this->getRegExpDateTime();
        $reBasisTyp      = $this->getRegExpSingleLineText();
        $rePlanetTyp     = $this->getRegExpPlanetTypes();
        $reObjektTyp     = $this->getRegExpObjectTypes();
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reResource      = $this->getRegExpResource();

        $regExp  = '/';
		$regExp .= 'Sondierungsbericht\s\(Schiffe\)\svon\s';
		$regExp .= '(?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))';
        $regExp .= '\sam\s(?P<datetime>' . $reMixedTime . ')\.';
        $regExp .= '\sBesitzer\sist\s((?P<owner>' . $reUserName . ')\s(\[(?P<alliance>' . $reBasisTyp . ')\])?)?\.';
        $regExp .= '\s*Planetentyp\s+(?P<planettyp>(' . $rePlanetTyp . '|-\?\?\?-))\s*';
		$regExp .= '\s*Objekttyp\s+(?P<objekttyp>(' . $reObjektTyp . '|-\?\?\?-))\s*';
        $regExp .= '(?:(\s*Basistyp\s+.*\s*)|)';
        $regExp .= '(?:';
        $regExp .= '	Schiffe[\s\n]*Planetare\sFlotte';
		$regExp .= '	[\s\n]+';
        $regExp .= '	(?P<schiffe>';
		$regExp .= '	    ((' . $reBasisTyp . '|-\?\?\?-)\s+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n]*)+';
        $regExp .= '	)?';
		$regExp .= '|)';
        $regExp .= '(?:';
        $regExp .= '    (?P<stat_fleet>';
        $regExp .= '        (';
		$regExp .= '            Stationierte\sFlotte\svon\s(' . $reUserName . '|-\?\?\?-)\s\((' . $reDecimalNumber . '|-\?\?\?-)\sSchiffe\)';
		$regExp .= '            [\s\n]+';
		$regExp .= '            ((' . $reBasisTyp . '|-\?\?\?-)\s+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n]*)+';
        $regExp .= '        )+';
        $regExp .= '	)';
		$regExp .= '|)';
        $regExp .= '(?:';
		$regExp .= '    Defence';
		$regExp .= '    [\s\n]+';
		$regExp .= '    (?P<defence>';
		$regExp .= '        ((' . $reBasisTyp . '|-\?\?\?-)\s+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n]*)+';
        $regExp .= '    )?';
		$regExp .= '|)';
        $regExp .= '(?:';
		$regExp .= '    Ressourcen';
		$regExp .= '    [\s\n]+';
		$regExp .= '    (?P<resources>';
		$regExp .= '        ((' . $reResource . '|-\?\?\?-)\s+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n]*)+';
		$regExp .= '    )';
		$regExp .= '|)';
        $regExp .= '^Hinweise\s';
        $regExp .= '(.*\n){1,5}';
        $regExp .= '(^(?P<link>http:\/\/www\.icewars\.de\/portal\/kb\/de\/sb\.php\?id=(\d+)\&md_hash=([\w\d]+)))?';
        $regExp .= '/mx';

        return $regExp;
    }

}