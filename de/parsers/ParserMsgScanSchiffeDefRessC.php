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

namespace libIwParsers\de\parsers;
use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserMsgBaseC;
use libIwParsers\ParserMsgI;
use libIwParsers\HelperC;
use libIwParsers\de\parserResults\DTOParserMsgResultMsgScanSchiffeDefRessC;

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
        $msg = $this->getMsg();

        foreach ($msg as $key => $value) {
            $retVal->$key = $value;
        }

        $aResultText = array();
        $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

        //@todo!
//   $parserResult->bSuccessfullyParsed = true;
//   $parserResult->aErrors[] = 'Parser not yet implemented.';    
//   return;

// 	return;
//     if (strpos($parserResult->objResultData->strMsgTitle,"Eigener Planet wurde sondiert") !== false)
// 	return;
        if ($fRetValText !== false && $fRetValText > 0) {
// 	$parserResult->bSuccessfullyParsed = true;
            $retVal->bSuccessfullyParsed = true;
            $strCoords = "";
// 	$strPlanetName = "";
            $strOwner = "";
            $strOwnerAlly = "";
            $strPlanetTyp = "";
            $strObjektTyp = "";
            $aCoords = array();
            $iCoordsGal = -1;
            $iCoordsSol = -1;
            $iCoordsPla = -1;
            $aSchiffe = array();
            $astatSchiffe = array();
            $aResources = array();
            $aDefence = array();

            $strCoords = $aResultText['coords'];
            $strOwner = $aResultText['owner'];
            $strOwnerAlly = $aResultText['alliance'];
            $strPlanetTyp = $aResultText['planetname'];
            $strObjektTyp = $aResultText['objektname'];
// 	$time = ;
// print_pre($time);
            $iCoordsGal = PropertyValueC::ensureInteger($aResultText['coords_gal']);
            $iCoordsSol = PropertyValueC::ensureInteger($aResultText['coords_sol']);
            $iCoordsPla = PropertyValueC::ensureInteger($aResultText['coords_pla']);
            $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);

            if (isset($aResultText['schiffe'])) {
                $aResultSchiffe = array();
                $regExpSchiffe = $this->getRegularExpressionSchiffe();
                $fRetValSchiffe = preg_match_all($regExpSchiffe, $aResultText['schiffe'], $aResultSchiffe, PREG_SET_ORDER);

                if ($fRetValSchiffe !== false && $fRetValSchiffe > 0) {
                    foreach ($aResultSchiffe as $result) {
                        $strSchiffName = $result['schiff_name'];
                        $iSchiffCount = $result['schiffe_count'];
                        $strSchiffName = PropertyValueC::ensureString($strSchiffName);
                        $iSchiffCount = PropertyValueC::ensureInteger($iSchiffCount);
                        if ($strSchiffName == "-???-") {
                            continue;
                        }
// 			  $aSchiffe[md5($strSchiffName)] = array('schiffe_name' => $strSchiffName,'schiffe_count' => $iSchiffCount);
                        $aSchiffe[$strSchiffName] = $iSchiffCount;
                    }
                }
            }
            if (isset($aResultText['stat_fleet'])) {
                $aResultStatSchiffe = array();
                $regExpStatSchiffe = $this->getRegularExpressionStatSchiffe();
                $fRetValStatSchiffe = preg_match_all($regExpStatSchiffe, $aResultText['stat_fleet'], $aResultStatSchiffe, PREG_SET_ORDER);

                if ($fRetValStatSchiffe !== false && $fRetValStatSchiffe > 0) {
                    foreach ($aResultStatSchiffe as $resultStat) {
                        $aResultSchiffe = array();
                        $regExpSchiffe = $this->getRegularExpressionSchiffe();
                        $fRetValSchiffe = preg_match_all($regExpSchiffe, $resultStat['stat_fleet'], $aResultSchiffe, PREG_SET_ORDER);
                        $statOwner = $resultStat['owner_stat'];

                        if ($fRetValSchiffe !== false && $fRetValSchiffe > 0) {
                            foreach ($aResultSchiffe as $result) {
                                $strSchiffName = $result['schiff_name'];
                                $iSchiffCount = $result['schiffe_count'];
                                $strSchiffName = PropertyValueC::ensureString($strSchiffName);
                                $iSchiffCount = PropertyValueC::ensureInteger($iSchiffCount);
                                if ($strSchiffName == "-???-") {
                                    continue;
                                }
                                // 			  $astatSchiffe[md5($strSchiffName)] = array('schiffe_name' => $strSchiffName,'schiffe_count' => $iSchiffCount);
                                $astatSchiffe[$statOwner][$strSchiffName] = $iSchiffCount;
                            }
                        }
                    }
                }
            }
            if (isset($aResultText['defence'])) {
                $aResultDefence = array();
                $regExpDefence = $this->getRegularExpressionDefence();
                $fRetValDefence = preg_match_all($regExpDefence, $aResultText['defence'], $aResultDefence, PREG_SET_ORDER);

                if ($fRetValDefence !== false && $fRetValDefence > 0) {
                    foreach ($aResultDefence as $result) {
                        $strDefenceName = $result['defence_name'];
                        $iDefenceCount = $result['defence_count'];
                        $strDefenceName = PropertyValueC::ensureString($strDefenceName);
                        $iDefenceCount = PropertyValueC::ensureInteger($iDefenceCount);
                        if ($strDefenceName == "-???-") {
                            continue;
                        }
// 			  $aDefence[md5($strDefenceName)] = array('defence_name' => $strDefenceName,'defence_count' => $iDefenceCount);
                        $aDefence[$strDefenceName] = $iDefenceCount;
                    }
                }
            }
            if (isset($aResultText['resources'])) {
                $aResultResources = array();
                $regExpResources = $this->getRegularExpressionResources();
                $fRetValResources = preg_match_all($regExpResources, $aResultText['resources'], $aResultResources, PREG_SET_ORDER);

                if ($fRetValResources !== false && $fRetValResources > 0) {
                    foreach ($aResultResources as $result) {
                        $strResourceName = $result['resource_name'];
                        $iResourceCount = $result['resource_count'];
                        $strResourceName = PropertyValueC::ensureEnum($strResourceName, 'eResources' );
                        $iResourceCount = PropertyValueC::ensureInteger($iResourceCount);
                        if ($strResourceName == "-???-") {
                            continue;
                        }
// 			  $aResources[md5($strResourceName)] = array('resource_name' => $strResourceName,'resource_count' => $iResourceCount);
                        $aResources[$strResourceName] = $iResourceCount;
                    }
                }
            }

            $retVal->strOwnerName = PropertyValueC::ensureString($strOwner);
            $retVal->strOwnerAllianceTag = PropertyValueC::ensureString($strOwnerAlly);
            $retVal->strCoords = PropertyValueC::ensureString($strCoords);
            $retVal->iTimestamp = HelperC::convertDateTimeToTimestamp($aResultText['datetime']);

            $retVal->ePlanetType = PropertyValueC::ensureString($strPlanetTyp);
            $retVal->eObjectType = PropertyValueC::ensureString($strObjektTyp);

            $retVal->aCoords = $aCoords;
            $retVal->aSchiffe = $aSchiffe;
            $retVal->astatSchiffe = $astatSchiffe;
            $retVal->aResources = $aResources;
            $retVal->aDefences = $aDefence;
        } else {
            $retVal->bSuccessfullyParsed = false;
            $retVal->aErrors[] = 'Unable to match the pattern (Schiffe/Deff/Ress).';
            $retVal->aErrors[] = '...' . $msg->strParserText;
        }
// print_die($parserResult);
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     */

    protected function getRegularExpressionResources()
    {
        $reResource = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp = '/';
        $regExp .= '(?P<resource_name>(' . $reResource . '|-\?\?\?-))
			[\s\t]+
			(?P<resource_count>(' . $reDecimalNumber . '|-\?\?\?-))
			[\s\n\r\t]*
		';
        $regExp .= '/mx';

        return $regExp;
    }

    protected function getRegularExpressionSchiffe()
    {
        $reSchiffe = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();

        $regExp = '/';
        $regExp .= '(?P<schiff_name>' . $reSchiffe . ')
			[\s\t]+
			(?P<schiffe_count>(' . $reDecimalNumber . '|-\?\?\?-))
			[\s\n\r\t]*
		';
        $regExp .= '/mx';

        return $regExp;
    }

    protected function getRegularExpressionDefence()
    {
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reDefence = $this->getRegExpSingleLineText3();

        $regExp = '/';
        $regExp .= '(?P<defence_name>(' . $reDefence . '|-\?\?\?-))
			[\s\t]+
			(?P<defence_count>(' . $reDecimalNumber . '|-\?\?\?-))
			[\s\n\r\t]*
		';
        $regExp .= '/mx';

        return $regExp;
    }

    protected function getRegularExpressionStatSchiffe()
    {
        $reSchiffe = $this->getRegExpSingleLineText3();
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reUserName = $this->getRegExpUserName();

        $regExp = '/';
        $regExp .= '	Stationierte\sFlotte\svon\s(?P<owner_stat>(' . $reUserName . '|-\?\?\?-))\s\((' . $reDecimalNumber . '|-\?\?\?-)\sSchiffe\)
			[\s\n\r\t]+
			(?P<stat_fleet>(
			((' . $reSchiffe . '|-\?\?\?-)[\s\t]+(' . $reDecimalNumber . '|-\?\?\?-)[\s\n\r\t]*)+
			))';
        $regExp .= '/mx';

        return $regExp;
    }

    private function getRegularExpressionText()
    {

        $reUserName = $this->getRegExpUserName();
        $reMixedTime = $this->getRegExpDateTime();
        $reBasisTyp = $this->getRegExpSingleLineText();
        $rePlanetTyp = $this->getRegExpPlanetTypes();
        $reObjektTyp = $this->getRegExpObjectTypes();
        $reDecimalNumber = $this->getRegExpDecimalNumber();
        $reResource = $this->getRegExpResource();

        $regExp  = '/
			Sondierungsbericht\s\(Schiffe\)\svon\s
			(?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))';
        $regExp .= 	'\sam\s(?P<datetime>'.$reMixedTime.')\.';
        $regExp .=	'\sBesitzer\sist\s((?P<owner>'.$reUserName.')\s(\[(?P<alliance>'.$reBasisTyp.')\])?)?\.';
        $regExp  .= '	\s*Planetentyp\s+(?P<planetname>('.$rePlanetTyp.'|-\?\?\?-))\s*
			\s*Objekttyp\s+(?P<objektname>('.$reObjektTyp.'|-\?\?\?-))\s*
			(?:(\s*Basistyp\s+.*\s*)|)';
        $regExp  .= '	(?:
			Schiffe[\s\n]*Planetare\sFlotte
			[\s\n\r\t]+
			(?P<schiffe>
			(('.$reBasisTyp.'|-\?\?\?-)[\s\t]+('.$reDecimalNumber.'|-\?\?\?-)[\s\n\r\t]*)+
			)?
			|)';
        $regExp  .= '	(?:(?P<stat_fleet>(
			Stationierte\sFlotte\svon\s('.$reUserName.'|-\?\?\?-)\s\(('.$reDecimalNumber.'|-\?\?\?-)\sSchiffe\)
			[\s\n\r\t]+
			(('.$reBasisTyp.'|-\?\?\?-)[\s\t]+('.$reDecimalNumber.'|-\?\?\?-)[\s\n\r\t]*)+
			)+)
			|)';
        $regExp  .= '	(?:
			Defence
			[\s\n\r\t]+
			(?P<defence>
			(('.$reBasisTyp.'|-\?\?\?-)[\s\t]+('.$reDecimalNumber.'|-\?\?\?-)[\s\n\r\t]*)+
			)?
			|)';
        $regExp  .= '	(?:
			Ressourcen
			[\s\n\r\t]+
			(?P<resources>
			(('.$reResource.'|-\?\?\?-)[\s\t]+('.$reDecimalNumber.'|-\?\?\?-)[\s\n\r\t]*)+
			)
			|)';
        $regExp .=	'^Hinweise\s';
        $regExp .=	'(.*[\n]){1,5}';
        $regExp .= 	'(^(?P<link>http:\/\/www\.icewars\.de\/portal\/kb\/de\/sb\.php\?id=(\d+)\&md_hash=([\w\d]+)))?';
        $regExp .= '/mx';

        return $regExp;
    }

}