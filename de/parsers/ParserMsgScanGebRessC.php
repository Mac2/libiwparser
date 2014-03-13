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
 * Parser for SubMessages Sondierung (Gebäude/Ress)
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_scan_gebress
 */
class ParserMsgScanGebRessC extends ParserMsgBaseC implements ParserMsgI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_msg_scan_gebress');
        $this->setCanParseMsg('Sondierung (Gebäude/Ress)');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserMsgI::parseMsg()
     */
    public function parseMsg(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserMsgResultMsgScanGebRessC();
        $retVal =& $parserResult->objResultData;

        $regExpText = $this->getRegularExpressionText();
        $msg        = $this->getMsg();

        foreach ($msg as $key => $value) {
            $retVal->$key = $value;
        }

        $aResultText = array();
        $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

        if ($fRetValText !== false && $fRetValText > 0) {
            $parserResult->bSuccessfullyParsed = true;

            $aBuildings   = array();
            $aResources   = array();

            $strCoords    = $aResultText['coords'];
            $strOwner     = $aResultText['owner'];
            $strOwnerAlly = $aResultText['alliance'];
            $strPlanetTyp = $aResultText['planetname'];
            $strObjektTyp = $aResultText['objektname'];
            $iCoordsGal   = PropertyValueC::ensureInteger($aResultText['coords_gal']);
            $iCoordsSol   = PropertyValueC::ensureInteger($aResultText['coords_sol']);
            $iCoordsPla   = PropertyValueC::ensureInteger($aResultText['coords_pla']);
            $aCoords      = array(
                'coords_gal' => $iCoordsGal,
                'coords_sol' => $iCoordsSol,
                'coords_pla' => $iCoordsPla
            );

            if (!empty($aResultText['buildings'])) {
                $aResultBuildings = array();
                $regExpBuildings  = $this->getRegularExpressionBuildings();
                $fRetValBuildings = preg_match_all($regExpBuildings, $aResultText['buildings'], $aResultBuildings, PREG_SET_ORDER);

                if ($fRetValBuildings !== false && $fRetValBuildings > 0) {
                    foreach ($aResultBuildings as $result) {
                        $strBuildingName = $result['building_name'];
                        $iBuildingCount  = $result['building_count'];
                        $strBuildingName = PropertyValueC::ensureString($strBuildingName);
                        $iBuildingCount  = PropertyValueC::ensureInteger($iBuildingCount);
                        if ($strBuildingName == "-???-") {
                            continue;
                        }
                        if (!$iBuildingCount) {
                            continue;
                        }

                        $aBuildings[$strBuildingName] = $iBuildingCount;
                    }
                } else {
                    $retVal->bSuccessfullyParsed = false;
                    $retVal->aErrors[]           = 'Unable to match the gebpattern. (Geb/Ress)';
                    $retVal->aErrors[]           = $aResultText['buildings'];
                }
            }

            if (!empty($aResultText['resources'])) {
                $aResultResources = array();
                $regExpResources  = $this->getRegularExpressionResources();
                $fRetValResources = preg_match_all($regExpResources, $aResultText['resources'], $aResultResources, PREG_SET_ORDER);

                if ($fRetValResources !== false && $fRetValResources > 0) {
                    foreach ($aResultResources as $result) {
                        $strResourceName = $result['resource_name'];
                        $iResourceCount  = $result['resource_count'];
                        $strResourceName = PropertyValueC::ensureEnum($strResourceName, 'eResources');
                        $iResourceCount  = PropertyValueC::ensureInteger($iResourceCount);
                        if (!$strResourceName || $strResourceName == "-???-") {
                            continue;
                        }
                        if ($iResourceCount == "-???-") {
                            continue;
                        }
                        $aResources[$strResourceName] = $iResourceCount;
                    }
                } else {
                    $retVal->bSuccessfullyParsed = false;
                    $retVal->aErrors[]           = 'Unable to match the resspattern. (Geb/Ress)';
                    $retVal->aErrors[]           = $aResultText['resources'];
                }
            }

            if ($parserResult->bSuccessfullyParsed === true) {
                $retVal->strOwnerName        = PropertyValueC::ensureString($strOwner);
                $retVal->strOwnerAllianceTag = PropertyValueC::ensureString($strOwnerAlly);
                $retVal->strCoords           = PropertyValueC::ensureString($strCoords);
                $retVal->iTimestamp          = HelperC::convertDateTimeToTimestamp($aResultText['datetime']);
                $retVal->ePlanetType         = PropertyValueC::ensureString($strPlanetTyp);
                $retVal->eObjectType         = PropertyValueC::ensureString($strObjektTyp);

                $retVal->aCoords    = $aCoords;
                $retVal->aBuildings = $aBuildings;
                $retVal->aResources = $aResources;
            }

        } else {
            $retVal->bSuccessfullyParsed = false;
            $retVal->aErrors[]           = 'Unable to match the de_msg_scan_gebress pattern.';
            $retVal->aErrors[]           = $msg->strParserText;
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRegularExpressionResources()
    {
        $reResource      = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp = '/';
        $regExp .= '(?P<resource_name>(' . $reResource . '|-\?\?\?-))
			\s+
			(?P<resource_count>(' . $reDecimalNumber . '|-\?\?\?-))
			[\s\n]*
		';
        $regExp .= '/mx';

        return $regExp;
    }

    protected function getRegularExpressionBuildings()
    {
        $reBuilding      = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp = '/';
        $regExp .= '(?P<building_name>(' . $reBuilding . '|-\?\?\?-))
			\s+
			(?P<building_count>(' . $reDecimalNumber . '|-\?\?\?-))
			[\s\n]*
		';
        $regExp .= '/mx';

        return $regExp;
    }

    private function getRegularExpressionText()
    {
        $reUserName       = $this->getRegExpUserName();
        $reMixedTime      = $this->getRegExpDateTime();
        $reBasisTyp       = $this->getRegExpSingleLineText3();
        $rePlanetTyp      = $this->getRegExpPlanetTypes();
        $reObjektTyp      = $this->getRegExpObjectTypes();
        $reSingleLineText = $this->getRegExpSingleLineText();

        $regExp = '/Sondierungsbericht\s\(Geb.{1,4}ude\)\svon\s(?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))';
        $regExp .= '\sam\s(?P<datetime>' . $reMixedTime . ')\.';
        $regExp .= '\sBesitzer\sist\s(?:(?P<owner>' . $reUserName . ')\s(?:\[(?P<alliance>' . $reBasisTyp . ')\])?)?\.';
        $regExp .= '\s*Planetentyp\s+(?P<planetname>(?:' . $rePlanetTyp . '|-\?\?\?-))\s*';
		$regExp .= '\s*Objekttyp\s+(?P<objektname>(?:' . $reObjektTyp . '|-\?\?\?-))\s*';
        $regExp .= '(\s*Basistyp\s' . $reBasisTyp . '\s*)?';
        $regExp .= '(?:Geb.{1,4}ude[\s\n\t]+';
		$regExp .= '    (?P<buildings>';
        $regExp .= '        (?:' . $reSingleLineText . '\n)+';
		$regExp .= '    )?';
		$regExp .= ')';
        $regExp .= '(?:Ressourcen[\s\n]+';
		$regExp .= '    (?P<resources>';
		$regExp .= '        (?:' . $reSingleLineText . '\n)+';
		$regExp .= '    )?';
		$regExp .= ')';
        $regExp .= '^Hinweise\s';
        $regExp .= '(.*\n){1,5}';
        $regExp .= '(?:^(?P<link>http:\/\/www\.icewars\.de\/portal\/kb\/de\/sb\.php\?id=(\d+)\&md_hash=([\w\d]+)))?';
        $regExp .= '/mx';

        return $regExp;
    }

}