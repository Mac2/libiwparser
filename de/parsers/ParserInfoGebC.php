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
 * Parses a Building Information
 *
 * This parser is responsible for parsing the information of a building
 *
 * Its identifier: de_info_geb
 */
class ParserInfoGebC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_info_geb');
    $this->setName("Geb&auml;udeinformationen");
    $this->setRegExpCanParseText('/Geb.{1,3}udeinfo\s+Geb.{1,3}udeinfo|Geb.{1,3}udeinfo.+Farbenlegende/s');
    $this->setRegExpBeginData( '' );
    $this->setRegExpEndData( '' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserInfoGebResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();
    $regExpRess = $this->getRegularExpressionRess();
    $regExpMain = $this->getRegularExpressionMaintenance();
    $regExpEffect = $this->getRegularExpressionEffect();
    $regExpTime = $this->getRegularExpressionTime();

    $aResult = array();
    $fRetVal = preg_match( $regExp, $this->getText(), $aResult );
// print_die($aResult);
    if( $fRetVal !== false && $fRetVal > 0 )
    {
        $parserResult->bSuccessfullyParsed = true;
        
        $retVal->aResearchsNeeded = HelperC::convertBracketStringToArray($aResult['strResearchsNeeded']);
        $retVal->aBuildingsNeeded = HelperC::convertBracketStringToArray($aResult['strBuildingsNeeded']);
        if (in_array("Gruppe",$retVal->aBuildingsNeeded))
		unset($retVal->aBuildingsNeeded[array_search("Gruppe",$retVal->aBuildingsNeeded)]);

        if (isset($aResult['strResearchsDevelop']))
            $retVal->aResearchsDevelop = HelperC::convertBracketStringToArray($aResult['strResearchsDevelop']);
        if (isset($aResult['strBuildingsDevelop'])) {
            $retVal->aBuildingsDevelop = HelperC::convertBracketStringToArray($aResult['strBuildingsDevelop']);
	    if (in_array("Gruppe",$retVal->aBuildingsDevelop))
		unset($retVal->aBuildingsDevelop[array_search("Gruppe",$retVal->aBuildingsDevelop)]);
	}
 	if (isset($aResult['strPlanetNeeded']))
            $retVal->strPlanetNeeded = PropertyValueC::ensureString($aResult['strPlanetNeeded']);

	if (isset($aResult['strObjectsNeeded']))
            $retVal->strObjectTypesNeeded = PropertyValueC::ensureString($aResult['strObjectsNeeded']);

 	if (isset($aResult['strPlanetPropertiesNeeded']))
            $retVal->aPlanetPropertiesNeeded = explode(" ", PropertyValueC::ensureString($aResult['strPlanetPropertiesNeeded']));

        $retVal->strGebName = trim(PropertyValueC::ensureString( $aResult['strBuildingName'] ));

	if (isset($aResult['comment']))
        	$retVal->strGebComment = trim(PropertyValueC::ensureString( $aResult['comment'] ));
	else if (isset($aResult['commentS']))
        	$retVal->strGebComment = trim(PropertyValueC::ensureString( $aResult['commentS'] ));

        $retVal->iHS = PropertyValueC::ensureInteger( $aResult['iHS'] );
 	$retVal->imaxAnz = PropertyValueC::ensureInteger( $aResult['imaxAnz'] );

        if (isset($aResult['stufe']) && !empty($aResult['stufe']))
        {
            $retVal->bIsStufenGeb = true;
            $retVal->iStufe = PropertyValueC::ensureInteger( $aResult['stufe'] );
        }
        
	//! Kosten
        $treffer = array();
        preg_match_all ($regExpRess, $aResult['kosten'], $treffer, PREG_SET_ORDER );
	$stufe=0;
        foreach ($treffer as $teff)
        {
	    if (!empty($teff['stufe'])) {
		$stufe = PropertyValueC::ensureInteger($teff['stufe']);
		if (isset ($teff["rise_type"]) && strpos($teff["rise_type"],"global") !== FALSE)
			$retVal->strRiseType = "global";
		else if (isset ($teff["rise_type"]) && strpos($teff["rise_type"],"global") === FALSE)
			$retVal->strRiseType = "local";
		continue;
	    }

	    if (!empty($stufe) && !empty($teff['resource_name']) ) {
		if (count($retVal->aCosts) == 0) {
            		$retVal->aCosts[] = array(
						'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
						'iResourceCount' => PropertyValueC::ensureInteger($teff['resource_count']), 
						'stufe' => $stufe
					    );
		}
	    	else {
			$exist=false;
			foreach ($retVal->aCosts as $key => $dat) {

				if ( $dat['iResourceCount'] == PropertyValueC::ensureInteger($teff['resource_count']) && $dat["strResourceName"] == PropertyValueC::ensureResource($teff['resource_name']) && $stufe > $dat["stufe"]) {
					$exist=true;
					break;
				}
			}
			if (!$exist) {
	    			$retVal->aCosts[] = array(
							'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
							'iResourceCount' => PropertyValueC::ensureInteger($teff['resource_count']),
							'stufe' => $stufe
						     );
			}
		}
	    }
	    else {
			$retVal->aCosts[] = array(
						'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
						'iResourceCount' => PropertyValueC::ensureInteger($teff['resource_count']), 
						'stufe' => 0
					    );
  	    }
        }

	//! unterhalt
	$treffer = array();
        preg_match_all ($regExpMain, $aResult['unterhalt'], $treffer, PREG_SET_ORDER );
        foreach ($treffer as $teff)
        {
            $retVal->aMaintenance[] = array('strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 'iResourceCount' => PropertyValueC::ensureInteger($teff['resource_count']));
        }

	//! Bauzeit
	$treffer = array();
        preg_match_all ($regExpTime, $aResult['dauer'], $treffer, PREG_SET_ORDER );
	$stufe=0;
        foreach ($treffer as $teff)
        {
	    if (!empty($teff['stufe'])) {
		$stufe = PropertyValueC::ensureInteger($teff['stufe']);
	    }

	    if (!empty($stufe) && !empty($teff['build_time']) ) {
		if (count($retVal->aBuildTime) == 0) {
            		$retVal->aBuildTime[] = array(
						'iBuildTime' => HelperC::convertMixedTimeToTimestamp( $teff["build_time"] ), 
						'stufe' => $stufe
					    );
		}
	    	else {
			$exist=false;
			foreach ($retVal->aBuildTime as $key => $dat) {

				if ( $dat['iBuildTime'] == HelperC::convertMixedTimeToTimestamp( $teff["build_time"] ) && $stufe > $dat["stufe"]) {
					$exist=true;
					break;
				}
			}
			if (!$exist) {
	    			$retVal->aBuildTime[] = array(
							'iBuildTime' => HelperC::convertMixedTimeToTimestamp( $teff["build_time"] ), 
							'stufe' => $stufe
						     );
			}
		}
	    }
	    else {	//! keine Verteuerung
			$retVal->aBuildTime[] = array(
						'iBuildTime' => HelperC::convertMixedTimeToTimestamp( $teff["build_time"] ), 
						'stufe' => 0
					    );
	    }
        }

	//! Nutzen
	$treffer = array();
        preg_match_all ($regExpEffect, $aResult['bringt'], $treffer, PREG_SET_ORDER );
	$stufe=0;
        foreach ($treffer as $teff)
        {
	    if (!empty($teff['stufe'])) {
		$stufe = PropertyValueC::ensureInteger($teff['stufe']);
		continue;
	    }
	    if (!empty($stufe) && !empty($teff['resource_name']) ) {
		if (count($retVal->aEffect) == 0) {
			$retVal->aEffect[] = array(
						'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
						'iResourceCount' => PropertyValueC::ensureFloat($teff['resource_count']),
						'stufe' => $stufe
					     );
		}
		else {
			$last = $retVal->aEffect[count($retVal->aEffect)-1];
			if (!empty($last) && $last['iResourceCount'] != PropertyValueC::ensureFloat( $teff['resource_count'] )) {
				$retVal->aEffect[] = array(
							'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
							'iResourceCount' => PropertyValueC::ensureFloat($teff['resource_count']),
							'stufe' => $stufe
						     );
			}
		}
	    }
	    else {
			$retVal->aEffect[] = array(
						'strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 
						'iResourceCount' => PropertyValueC::ensureFloat($teff['resource_count']),
						'stufe' => 0
					     );
	    }
        }

	//! @todo zerstoerbar durch ...

    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }

  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpressionRess()
  {
    /**
    */

    $reResource                = $this->getRegExpResource();

    $regExpRess  = '/';
    $regExpRess  .= '(?:(?P<rise_type>globale\sAnzahl|Stufe)\s(?P<stufe>\d+)\:\s)|(?P<resource_name>'.$reResource.')\:\s(?P<resource_count>'.$this->getRegExpDecimalNumber().')';
    $regExpRess  .= '/mx';
    
    return $regExpRess;
  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpressionMaintenance()
  {
    /**
    */

    $reResource                = $this->getRegExpResource();

    $regExpRess  = '/';
    $regExpRess  .= '(?P<resource_count>'.$this->getRegExpDecimalNumber().')\s(?P<resource_name>'.$reResource.')';
    $regExpRess  .= '/mx';
    
    return $regExpRess;
  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpressionTime()
  {
    /**
    */

    $reMixedTime     = $this->getRegExpMixedTime();

    $regExpRess  = '/';
    $regExpRess  .= '(?:globale\sAnzahl\s(?P<stufe>\d+)\:\s)?(?P<build_time>'.$reMixedTime.')';
    $regExpRess  .= '/mx';

    return $regExpRess;
  }


 /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpressionEffect()
  {
    /**
    */

    $reResource                = $this->getRegExpResource();
    $reResourceCount           = $this->getRegExpFloatingDouble();

    $regExpRess  = '/';
    $regExpRess  .= '(?:(?:globale\sAnzahl|Stufe)\s(?P<stufe>\d+)\:\s)|(?P<resource_count>'.$reResourceCount.')\s(?P<resource_name>'.$reResource.')';
    $regExpRess  .= '/mx';
    
    return $regExpRess;
  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    /**
    */

    $reResearch         = $this->getRegExpSingleLineText3();        //! accepted also numbers in ResearchName
    $reBracketString    = $this->getRegExpBracketString();
    $reDecNumber        = $this->getRegExpDecimalNumber();
    $reResource         = $this->getRegExpResource();
    $reMixedTime        = $this->getRegExpMixedTime();
    $reObjects          = $this->getRegExpObjectTypes();
    $rePlanets          = $this->getRegExpPlanetTypes();

    $regExp  = '/';
    $regExp  .= 'Geb.{1,3}udeinfo\:\s';
    $regExp  .= '(?P<strBuildingName>'.$reResearch.')\s*?';
    $regExp  .= '[\n\r]+';

    //! da kommentar zu unspezifisch ist, bei Stufengebaeuden explizit kommentar und Stufeninfos verknuepfen...
    $regExp  .= '(?:';
    //! Stufengebaeude
    $regExp  .= '(?P<commentS>(?:^[\s\S]*[\n\r])?)';
    $regExp  .= '(?:^Stufengeb.{1,3}ude';
    $regExp  .= '(?:\s*'.$reResearch.'[\n\t])*';
    $regExp  .= '^Stufe\s(?P<stufe>\d+)[\n\t]';
    $regExp  .= '\s*)';
    //! nur Kommentar
    $regExp  .= '|'; 
    $regExp  .= '(?P<comment>(?:^[\s\S]*[\n\r])?)';
    $regExp  .= ')';

    $regExp  .= 'Kosten\s+?(?P<kosten>(?:(?:(?:globale\sAnzahl|Stufe)\s(?:\d)+\:\s)?(?:'.$reResource.'\:\s'.$reDecNumber.'\s)*[\n\t]*)+)';
    $regExp  .= 'Dauer\s+?(?P<dauer>(?:(?:globale\sAnzahl\s(?:\d)+\:\s)?'.$reMixedTime.'[\n\t]*)+)\s*';

    //! ausnahme fuer Forschungsgebaeude da sonst zu fuzzy
    $regExp  .= '(?:';
    //! mehrzeilig -> Flabs
    $regExp  .=   'bringt\s+?(?P<bringt>(?:';
    $regExp  .=     '(?:(?:(?:globale\sAnzahl|Stufe)\s(?:\d)+\:\s)'.$reResearch.'[\n\t])+';
    $regExp  .=     '|';
    //! nur eine zeile -> rest 
    $regExp  .=     '(?:'.$reResearch.'[\n\t]){1,2}';
    $regExp  .=   '))';
    $regExp  .= ')';

    $regExp  .= '(?:^Kosten\s+(?P<unterhalt>(?:'.$reDecNumber.'\s'.$reResource.',?\s*)+))?';

    $regExp  .= '(?:Maximale\sAnzahl\s+';
    $regExp  .= '(?P<imaxAnz>'.'\d+'.'(?:\s*\(global\))?)\s*?';
    $regExp  .= '[\n\r]*)?';

    $regExp  .= '(?:Entscheidungsgeb\sDieses\sGeb.{1,3}ude\skann\snicht\smehr\sgebaut\swerden,\swenn\seines\sder\sfolgenden\sGeb.{1,3}ude\sgebaut\swurde\:[\n\t]';
    $regExp  .= '(?:'.$reResearch.'[\n\t])+';
    $regExp  .= ')?';

    $regExp  .= '(?:Punkte\s+?';
    $regExp  .= '(?P<iHS>'.'\d+'.')\s*?';
    $regExp  .= '[\n\r]+)?';

    $regExp  .= '\s*Voraussetzungen\sForschungen\s+?';
    $regExp  .= '(?P<strResearchsNeeded>'.$reBracketString.')?';
    $regExp  .= '\s+?';
    $regExp  .= 'Voraussetzungen\sGeb.{1,3}ude\s+?';
    $regExp  .= '(?P<strBuildingsNeeded>'.$reBracketString.'(?:\(Gruppe\))?)?';
    $regExp  .= '\s+?';
    $regExp  .= '(?:Voraussetzungen\sPlaneteneigenschaften\s+?';
    $regExp  .= '(?P<strPlanetPropertiesNeeded>'.$reResearch.')?';
    $regExp  .= '\s+?)?';
 
    $regExp  .= '(?:Voraussetzungen\sPlanetentyp\s+?';
    $regExp  .= '(?P<strPlanetNeeded>'.$rePlanets.')?';
    $regExp  .= '\s+?)?';

    $regExp  .= '(?:Voraussetzungen\sKolonietyp\s+?';
    $regExp  .= '(?P<strObjectsNeeded>'.$reObjects.')?';
    $regExp  .= '\s+?)?';

    $regExp .= '(?:\s*Dieses\sGeb.{1,3}ude\sben.{1,3}tigt\sweitere\skomplexe\sVoraussetzungen\s)?';

    $regExp  .= '(?:';
    $regExp  .= 'Kann\szerst.{1,3}rt\swerden\sdurch\s+?';
    $regExp  .= '(?P<strDestroyable>(?:.*\s'.$reBracketString.'\s*)*)?';
    $regExp  .= '\s+?)?';

    $regExp  .= 'Erm.{1,3}glicht\sForschungen\s+?';
    $regExp  .= '(?P<strResearchsDevelop>'.$reBracketString.')?';
    $regExp  .= '\s+?';

    $regExp  .= 'Erm.{1,3}glicht\sGeb.{1,3}ude\s+?';
    $regExp  .= '(?P<strBuildingsDevelop>'.$reBracketString.')?';
    $regExp  .= '\s+?';

    $regExp  .= '/mx';
    
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

