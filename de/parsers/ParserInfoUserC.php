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
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\HelperC;
use libIwParsers\de\parserResults\DTOParserInfoUserResultC;
use libIwParsers\de\parserResults\DTOParserInfoUserResultUserC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a User Information
 *
 * This parser is responsible for parsing the information of a User
 *
 * Its identifier: de_info_user
 */
class ParserInfoUserC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_info_user');
        $this->setName("Spielerinformation");
        $this->setRegExpCanParseText('/Spielerinfo.+Schreibe\sNachricht/s');
        $this->setRegExpBeginData('/Spielerinfo\s+Spielerinfo/');
        $this->setRegExpEndData('/Schreibe\sNachricht/');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserInfoUserResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {        
                $retObj = new DTOParserInfoUserResultUserC();

                $retObj->strUserName        = PropertyValueC::ensureString($result['strUserName']);
                $retObj->strUserAlliance    = PropertyValueC::ensureString($result['strUserAlliance']);
                $retObj->strUserAllianceTag = PropertyValueC::ensureString($result['strUserAllianceTag']);
                $retObj->strUserAllianceJob = PropertyValueC::ensureString($result['strUserAllianceJob']);

                if (!empty($result['strPlanetName'])) { //Informationen über den Hauptplanet vorhanden

                    $iCoordsGal        = PropertyValueC::ensureInteger($result['iCoordsGal']);
                    $iCoordsSol        = PropertyValueC::ensureInteger($result['iCoordsSol']);
                    $iCoordsPla        = PropertyValueC::ensureInteger($result['iCoordsPla']);
                    $aCoords           = array(
                        'coords_gal' => $iCoordsGal,
                        'coords_sol' => $iCoordsSol,
                        'coords_pla' => $iCoordsPla
                    );
                    $retObj->aCoords   = $aCoords;
                    $retObj->strCoords = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;

                    $planetname            = HelperC::convertBracketStringToArray($result['strPlanetName']);
                    $retObj->strPlanetName = PropertyValueC::ensureString($planetname[0]);

                }

                if (!empty($result['AdminAcc'])) {
                    $retObj->strAccType = 'Admin';
                } elseif (!empty($result['IWBPAcc'])) {
                    $retObj->strAccType = 'IWBP';
                } else {
                    $retObj->strAccType = 'Spieler';
                }

                $retObj->iEntryDate = HelperC::convertDateTimeToTimestamp($result['iEntryDate']);

                $retObj->iGebPkt   = PropertyValueC::ensureInteger($result['iGebPkt']);
                $retObj->iFP       = PropertyValueC::ensureInteger($result['iFP']);
                $retObj->iHSPos    = PropertyValueC::ensureInteger($result['iHSPos']);
                $retObj->iHSChange = PropertyValueC::ensureInteger($result['iHSChange']);
                $retObj->iEvo      = PropertyValueC::ensureInteger($result['iEvo']);

                $retObj->strStaatsform = PropertyValueC::ensureString($result['strStaatsform']);
                if (isset($result['strTitel'])) {
                    $retObj->strTitel = PropertyValueC::ensureString($result['strTitel']);
                }
                if (isset($result['strDescr'])) {
                    $retObj->strDescr = PropertyValueC::ensureString($result['strDescr']);
                }
                $retVal->aUser[$retObj->strUserName] = $retObj;
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
         */

        $reText       = $this->getRegExpSingleLineText3();
        $reName       = $this->getRegExpUserName();
        $reAlliance   = $this->getRegExpSingleLineText();
        $rePlanetName = $this->getRegExpBracketString();
        $reCoordsGal  = '\d+';
        $reCoordsSol  = '\d+';
        $reCoordsPla  = '\d+';
        $reEntryDate  = $this->getRegExpDate();
        $rePoints     = $this->getRegExpResearchPoints();
        $reNumber     = $this->getRegExpFloatingDouble();
        $reDescr      = $this->getRegExpSingleLineText3();

        $regExp = '/';
        $regExp .= 'Name\s+?';
        $regExp .= '(?P<strUserName>' . $reName . ')\s*?';
        $regExp .= '\n+';
        $regExp .= '  (?:Allianz\s+?';
        $regExp .= '  (?P<strUserAlliance>' . $reAlliance . ')\s*';
        $regExp .= '  \[(?P<strUserAllianceTag>' . $reAlliance . ')\]\s*';
        $regExp .= '  (?:\((?P<strUserAllianceJob>' . $reAlliance . ')\)\s*)?';
        $regExp .= '  \n+';
        $regExp .= ')?';
        $regExp .= '(?:'; //! Mac: seit Runde 11 nur optional, bzw. ab 2 Planeten ?
        $regExp .= '  Hauptplanet\s+(?P<iCoordsGal>' . $reCoordsGal . ')';
        $regExp .= '  \s:\s';
        $regExp .= '  (?P<iCoordsSol>' . $reCoordsSol . ')';
        $regExp .= '  \s:\s';
        $regExp .= '  (?P<iCoordsPla>' . $reCoordsPla . ')';
        $regExp .= '  \s';
        $regExp .= '  (?P<strPlanetName>' . $rePlanetName . ')';
        $regExp .= '  [\n\s]+?';
        $regExp .= ')?';
        $regExp .= '(?P<AdminAcc>Admin\sAccount[\n\s]+)?';
        $regExp .= 'dabei\sseit\s+?';
        $regExp .= '(?P<iEntryDate>' . $reEntryDate . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Geb.{1,4}udepunkte\s+';
        $regExp .= '(?P<iGebPkt>' . $rePoints . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Forschungspunkte\s+';
        $regExp .= '(?P<iFP>' . $rePoints . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Position\sin\sder\sHighscore\s+';
        $regExp .= '(?P<iHSPos>' . $rePoints . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Ver.{1,4}nderung\sin\sder\sHighscore\s+';
        $regExp .= '(?P<iHSChange>' . $reNumber . ')\sPl.{1,4}tze\s*?';
        $regExp .= '\n+';
        $regExp .= 'Evolutionsstufe\s+';
        $regExp .= '(?P<iEvo>' . $rePoints . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'Titel\s+';
        $regExp .= '(?P<strStaatsform>' . $reText . ')\s*?';
        $regExp .= '\n+';
        $regExp .= 'eigener\sTitel\s+';
        $regExp .= '(?:(?P<strTitel>' . $reText . ')\s*)?';
        $regExp .= '\n+';
        $regExp .= 'Beschreibung\s+';
        $regExp .= '(?:(?P<strDescr>(?:' . $reDescr . '\n+)+))?';
        $regExp .= '\n*';
        $regExp .= 'Diverses\s+';
        $regExp .= '(?:(?P<IWBPAcc>besoffener\sPinguin\sAccount\sBesitzer)|(?P<strMisc>' . $reText . ')\s*)?';
        $regExp .= '\n+';
        $regExp .= '/mx';

        return $regExp;
    }

}