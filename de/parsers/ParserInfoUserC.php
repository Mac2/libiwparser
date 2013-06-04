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
    $this->setRegExpBeginData( '/Spielerinfo\s+Spielerinfo/' );
    $this->setRegExpEndData( '/Schreibe\sNachricht/' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserInfoUserResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match( $regExp, $this->getText(), $aResult );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
        $parserResult->bSuccessfullyParsed = true;

        $retVal->strUserName = PropertyValueC::ensureString( $aResult['strUserName'] );
        $retVal->strUserAlliance = PropertyValueC::ensureString( $aResult['strUserAlliance'] );
        $retVal->strUserAllianceTag = PropertyValueC::ensureString( $aResult['strUserAllianceTag'] );
        $retVal->strUserAllianceJob = PropertyValueC::ensureString( $aResult['strUserAllianceJob'] );

        if (!empty($aResult['strPlanetName'])) {          //Informationen über den Hauptplanet vorhanden

            $iCoordsGal = PropertyValueC::ensureInteger($aResult['iCoordsGal']);
            $iCoordsSol = PropertyValueC::ensureInteger($aResult['iCoordsSol']);
            $iCoordsPla = PropertyValueC::ensureInteger($aResult['iCoordsPla']);
            $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
            $retVal->aCoords = $aCoords;
            $retVal->strCoords = $iCoordsGal.':'.$iCoordsSol.':'.$iCoordsPla;

            $planetname = HelperC::convertBracketStringToArray($aResult['strPlanetName']);
            $retVal->strPlanetName = PropertyValueC::ensureString( $planetname[0] );

        }

        if (!empty($aResult['AdminAcc'])) {
            $retVal->strAccType = 'Admin';
        } elseif (!empty($aResult['IWBPAcc'])) {
            $retVal->strAccType = 'IWBP';
        } else {
            $retVal->strAccType = 'Spieler';
        }

        $retVal->iEntryDate = HelperC::convertDateTimeToTimestamp($aResult['iEntryDate']);

        $retVal->iGebPkt = PropertyValueC::ensureInteger($aResult['iGebPkt']);
        $retVal->iFP = PropertyValueC::ensureInteger($aResult['iFP']);
        $retVal->iHSPos = PropertyValueC::ensureInteger($aResult['iHSPos']);
        $retVal->iHSChange = PropertyValueC::ensureInteger($aResult['iHSChange']);
        $retVal->iEvo = PropertyValueC::ensureInteger($aResult['iEvo']);

        $retVal->strStaatsform = PropertyValueC::ensureString( $aResult['strStaatsform'] );
        if (isset($aResult['strTitel']))
            $retVal->strTitel = PropertyValueC::ensureString( $aResult['strTitel'] );
        if (isset($aResult['strDescr']))
            $retVal->strDescr = PropertyValueC::ensureString($aResult['strDescr']);
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
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

  $regExp  = '/';
  $regExp  .= 'Name\s+?';
  $regExp  .= '(?P<strUserName>'.$reName.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= '  (?:Allianz\s+?';
  $regExp  .= '  (?P<strUserAlliance>'.$reAlliance.')\s*';
  $regExp  .= '  \[(?P<strUserAllianceTag>'.$reAlliance.')\]\s*';
  $regExp  .= '  (?:\((?P<strUserAllianceJob>'.$reAlliance.')\)\s*)?';
  $regExp  .= '  [\n\r]+';
  $regExp  .= ')?';
  $regExp  .= '(?:';        //! Mac: seit Runde 11 nur optional, bzw. ab 2 Planeten ?
  $regExp  .= '  Hauptplanet\s+(?P<iCoordsGal>'.$reCoordsGal.')';
  $regExp  .= '  \s:\s';
  $regExp  .= '  (?P<iCoordsSol>'.$reCoordsSol.')';
  $regExp  .= '  \s:\s';
  $regExp  .= '  (?P<iCoordsPla>'.$reCoordsPla.')';
  $regExp  .= '  \s';
  $regExp  .= '  (?P<strPlanetName>'.$rePlanetName.')';
  $regExp  .= '  [\n\s]+?';
  $regExp  .= ')?';
  $regExp  .= '(?P<AdminAcc>Admin\sAccount[\n\s]+)?';
  $regExp  .= 'dabei\sseit\s+?';
  $regExp  .= '(?P<iEntryDate>'.$reEntryDate.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Geb.{1,3}udepunkte\s+';
  $regExp  .= '(?P<iGebPkt>'.$rePoints.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Forschungspunkte\s+';
  $regExp  .= '(?P<iFP>'.$rePoints.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Position\sin\sder\sHighscore\s+';
  $regExp  .= '(?P<iHSPos>'.$rePoints.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Ver.{1,3}nderung\sin\sder\sHighscore\s+';
  $regExp  .= '(?P<iHSChange>'.$reNumber.')\sPl.{1,3}tze\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Evolutionsstufe\s+';
  $regExp  .= '(?P<iEvo>'.$rePoints.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Titel\s+';
  $regExp  .= '(?P<strStaatsform>'.$reText.')\s*?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'eigener\sTitel\s+';
  $regExp  .= '(?:(?P<strTitel>'.$reText.')\s*)?';
  $regExp  .= '[\n\r]+';
  $regExp  .= 'Beschreibung\s+';
  $regExp  .= '(?:(?P<strDescr>(?:'.$reDescr.'[\n\r]+)+))?';
  $regExp  .= '[\n\r]?';
  $regExp  .= 'Diverses\s+';
  $regExp  .= '(?:(?P<IWBPAcc>besoffener\sPinguin\sAccount\sBesitzer)|(?P<strMisc>'.$reText.')\s*)?';
  $regExp  .= '[\n\r]+';
  $regExp  .= '/mx';

    return $regExp;
  }  

  /////////////////////////////////////////////////////////////////////////////  

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

?>
