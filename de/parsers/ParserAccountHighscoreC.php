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
 * Parser for the ingame account Highscores
 *
 * Its identifier: de_account_highscore
 */
class ParserAccountHighscoreC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();
 
    $this->setIdentifier('de_account_highscore');
    $this->setName('Account Highscore');
    $this->setRegExpCanParseText('/(?:Highscore\s-\sMen.{1,3}\s+)?eigene\sHighscore\s+globale\sHighscores\s+Highscore\sWackelpudding\s+Highscore\s*\n(?:Highscore)?.*Alle\sAnzeigen\sAlle\sAusblenden/sm');
	$this->setRegExpBeginData( '/^Alle\sAnzeigen\sAlle\sAusblenden\n/sm' );
//     $this->setRegExpEndData( '/Statue/sm' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserAccountHighscoreResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();
    
    $regExp = $this->getRegularExpression();
	
    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );
// print_pre($regExp);
// echo $this->getText();
// print_die($aResult);

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      $parserResult->bSuccessfullyParsed = true;

	$strMainCat="";
	$strSubCat="";
	$strContent="";
	$atranslate = array();
	//! MainCat
	$atranslate["von anderen geklaut"] = "stolen";
	$atranslate["Produziert / Gebaut"] = "prod";
	$atranslate["Verbaut / Verbraucht"] = "used";
	$atranslate["Handel"] = "trade";
	$atranslate["Verluste (im eigenen Account)"] = "lost";
	$atranslate["Zerstört (bei anderen Accounts)"] = "destroyed";

	//! SubCat
	$atranslate["Verteidigung"] = "defence";
	$atranslate["Gebäude"] = "gebaeude";
	$atranslate["Werften"] = "gebaeude";
	$atranslate["Schiffe"] = "schiffe";
	$atranslate["Ressourcen"] = "resource";
	$atranslate["gesamte Ressourcenproduktion"] = "resource";
	$atranslate["Ressourcenproduktion (pro Stunde)"] = "resource";
	$atranslate["Umwandlung / Laufende Ressourcenkosten"] = "wandlung";
	$atranslate["Verteidigungsbau"] = "defencebau";
	$atranslate["Gebäudebau"] = "gebaeudebau";
	$atranslate["Schiffbau"] = "schiffebau";
	$atranslate["Forschung"] = "research";
	$atranslate["Eingenommen"] = "income";
	$atranslate["Ausgegeben"] = "outcome";
	$atranslate["Gekauft"] = "input";
	$atranslate["Verkauft"] = "output";

      foreach( $aResult as $result )
      {
	$strArea="";	
	if (isset($result['strMainCat']) && !empty($result['strMainCat'])) {
		$strMainCat = PropertyValueC::ensureString($result['strMainCat']);

		//! @todo Aktueller Besitz, Aktuelle maximale Produktion, Verschickt per Schiff, Verschwendet / Abbruch
		if (in_array($strMainCat,array_keys($atranslate)))
			$strMainCat = $atranslate[$strMainCat];
		else {
			$strMainCat="";
		}
		continue;
	}

	if (isset($result['strSubCat']) && !empty($result['strSubCat'])) {
		$strSubCat = PropertyValueC::ensureString($result['strSubCat']);
		if (in_array($strSubCat,array_keys($atranslate)))
			$strSubCat = $atranslate[$strSubCat];
	}

	if (isset($result['strContent']) && !empty($result['strContent'])) {
		$strContent = PropertyValueC::ensureString($result['strContent']);
	}

	if (isset($result['strAreas']) && !empty($result['strAreas'])) {
		$strArea = PropertyValueC::ensureString($result['strAreas']);
		if (in_array($strArea,array_keys($atranslate)))
			$strArea = $atranslate[$strArea];
	}

	if (!empty($strContent)) {
		$fRetVal2 = preg_match_all( $this->splitContentRegularExpression(), $strContent, $aCont, PREG_SET_ORDER );

		foreach( $aCont as $cont )
		{
			$strName = PropertyValueC::ensureString( $cont['strName'] );
			$iAnz = PropertyValueC::ensureInteger( $cont["iAnz"] );
			if (empty($iAnz) || empty($strMainCat) || empty($strSubCat) || empty($strName)) continue;

			if (!empty($strArea) && $strMainCat == "used") 
				if (isset($retVal->aData[$strMainCat][$strSubCat][$strArea][$strName]))
					$retVal->aData[$strMainCat][$strSubCat][$strArea][$strName] += $iAnz;
				else
					$retVal->aData[$strMainCat][$strSubCat][$strArea][$strName] = $iAnz;
			else
				if (isset($retVal->aData[$strMainCat][$strSubCat][$strName]))
					$retVal->aData[$strMainCat][$strSubCat][$strName] += $iAnz;
				else
					$retVal->aData[$strMainCat][$strSubCat][$strName] = $iAnz;
		}
	}
      }
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }

  }

  private function splitContentRegularExpression()
  {
	$reInteger  	   = $this->getRegExpDecimalNumber();
	$reContent  	   = $this->getRegExpSingleLineText();	// (?:[^\n\r]*)
    
	$regExp  = '/(?:';
	$regExp .= '(?:^(?P<strName>'  . $reContent . ')\s+(?P<iAnz>' . $reInteger . ')\s*$)';	
	$regExp .= ')';
    $regExp .= '/m';
	
	return $regExp;
  }
  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    /**
    */

	$reInteger  	   = $this->getRegExpDecimalNumber();
	$reAreas	   = '(?:'.$this->getRegExpAreas().'|(?:Ressourcen))';
// 	$reAreas	   = $this->getRegExpAreas();
	$reMainCat         = "(?:Aktuelle\smaximale\sProduktion|Produziert\s\/\sGebaut|Aktueller\sBesitz|von\sanderen\sgeklaut|Verluste\s\(im\seigenen\sAccount\)|Zerst.{1,3}rt\s\(bei\sanderen\sAccounts\)|Verschickt\sper\sSchiff|(?<=^)Handel(?=\s*Gekauft)|Verbaut\s\/\sVerbraucht|Verschwendet\s\/\sAbbruch)";

	//! direkt die daten abgreifen
//	$reSubCat = "(?:Ressourcenproduktion\s\(pro\sStunde\)|gesamte\sRessourcenproduktion|Werften|(?<=^)Verteidigung|Geb.{1,3}ude|Ressourcen|nach\sFlottentyp)";

	//! zus. sortierung nach Areas
//	$reSubCat_ext = "(?:Schiffe|Verteidigung|Geb.{1,3}ude|Gekauft|Verkauft|Eingenommen|Ausgegeben|Credits\sEmpfangen|Credits\sVersendet|Schiffbau)";
//	$reSubCatUsed = "(?:Verteidigungsbau|Geb.{1,3}udebau|<?=^)Forschung|Umwandlung\s\/\sLaufende\sRessourcenkosten)";

//	$reContent 	= '(?!'.$reSubCat.'|'.$reSubCat_ext.'|'.$reAreas.')(?:[^\n\r]*)';		//! alles außer Subkategorien

//! funzt, aber besser wenn nur ein Subcat und ein Content ...
    $regExp  = '/^(?:';
// 	$regExp .= '(?P<strMainCat>'     . $reMainCat     . ')\s[\n\t|\n\r\t]|';				//! einzelne MainKategorie
// 	$regExp .= '(?:';										//! Subkategorie, mit Untergruppierung (Areas)
// 	$regExp .= '(?:(?P<strSubCatExt>'. $reSubCat_ext  . ')\s(?:' . $reInteger . ')?\s[\n\t|\n\r\t])?';
// 	$regExp .= '(?P<strAreas>'        . $reAreas      . ')\s' . $reInteger . '\s*[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strContentExt>(?:'  . $reContent . '\s' . $reInteger . '\s[\n\t|\n\r\t])+)';	
// 	$regExp .= ')';
// 	$regExp .= '|(?:';										//! einfache Subkategorie mit Daten
// 	$regExp .= '(?P<strSubCat>'      . $reSubCat      . ')\s' . $reInteger . '\s[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strContent>(?:' . $reContent     . '\s' . $reInteger       . '\s[\n\t|\n\r\t])+)';	
// 	$regExp .= ')';
// 	$regExp .= '|(?:';										//! einfache Subkategorie mit Daten
// 	$regExp .= '(?P<strSubCatUsed>'      . $reSubCatUsed      . ')\s(?:' . $reInteger . ')?\s[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strAreasUsed>'        . 'Ressourcen'      . ')\s' . $reInteger . '\s*[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strContentUsed>(?:' . $reContent     . '\s' . $reInteger       . '\s[\n\t|\n\r\t])+)';	
// 	$regExp .= ')';
// 	$regExp .= ')';
	
	$reSubCat = "(?:Ressourcenproduktion\s\(pro\sStunde\)|gesamte\sRessourcenproduktion|Werften|Verteidigungsbau|(?<=^)Verteidigung|Geb.{1,3}ude|Ressourcen|nach\sFlottentyp|Schiffe|Geb.{1,3}ude|Gekauft|Verkauft|Eingenommen|Ausgegeben|Credits\sEmpfangen|Credits\sVersendet|Schiffbau|Geb.{1,3}udebau|(?<=^)Forschung(?=\s*Ressourcen)|Umwandlung\s\/\sLaufende\sRessourcenkosten)";
	
	$reContent 	= '(?!'.$reSubCat.'|'.$reAreas.')(?:[^\n\r]*)';		//! alles außer Subkategorien

	$regExp .= '(?P<strMainCat>'     . $reMainCat     . ')\s*[\n\t|\n\r\t]|';				//! einzelne MainKategorie
	$regExp .= '(?:';										//! Subkategorie, mit Untergruppierung (Areas)
	$regExp .= '(?:(?P<strSubCat>'. $reSubCat  . ')\s+(?:' . $reInteger . ')?\s*[\n\t|\n\r\t])?';
	$regExp .= '(?:(?P<strAreas>'        . $reAreas      . ')\s+' . $reInteger . '\s*[\n\t|\n\r\t])?';
	$regExp .= '(?P<strContent>(?:'  . $reContent . '\s+' . $reInteger . '\s*[\n\t|\n\r\t])+)';	
	$regExp .= ')';


	//$regExp .= '|(?:';										//! einfache Subkategorie mit Daten
// 	$regExp .= '(?P<strSubCat>'      . $reSubCat      . ')\s' . $reInteger . '\s[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strContent>(?:' . $reContent     . '\s' . $reInteger       . '\s[\n\t|\n\r\t])+)';	
// 	$regExp .= ')';
//	$regExp .= '|(?:';										//! einfache Subkategorie mit Daten
// 	$regExp .= '(?P<strSubCatUsed>'      . $reSubCatUsed      . ')\s(?:' . $reInteger . ')?\s[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strAreasUsed>'        . 'Ressourcen'      . ')\s' . $reInteger . '\s*[\n\t|\n\r\t]';
// 	$regExp .= '(?P<strContentUsed>(?:' . $reContent     . '\s' . $reInteger       . '\s[\n\t|\n\r\t])+)';	
// 	$regExp .= ')';
	$regExp .= ')';

    $regExp .= '/m';
    
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



?>
