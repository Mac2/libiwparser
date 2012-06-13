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
 * @author Martin Martimeo <martin@martimeo.de>
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a the Planiress Part 2
 *
 * This parser is responsible for parsing the planiress part 2
 *
 * Its identifier: de_wirtschaft_planiress2
 */
class ParserWirtschaftPlaniress2C extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_wirtschaft_planiress2');
    $this->setName("KoloRess&Uuml;bersicht Teil2");
    $this->setRegExpCanParseText('/Ressourcenkolo.+bersicht\sTeil\s2/s');
    $this->setRegExpBeginData( '/Kolonie\sFP\s\w+\sSteuersatz\s(?:Bev.+lkerung|blubbernde\sGallertmasse)\sZufr/' );
    $this->setRegExpEndData( '' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserWirtschaftPlaniress2ResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    /**
    */

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
    $parserResult->bSuccessfullyParsed = true;

    $steuer=0;
    foreach( $aResult as $result )
    {
      if (!empty($result['is_sum']))
      {
        $retVal->iFPProduction = PropertyValueC::ensureInteger( $result['fp_amount'] );
        $retVal->fFPProductionWithoutMods = PropertyValueC::ensureFloat( $result['fp_clean'] );  
        $retVal->fResearchModGlobal = PropertyValueC::ensureFloat( $result['fp_mod_planet']) + PropertyValueC::ensureFloat($result['fp_mod_global'] );  
        $retVal->fCreditProduction = PropertyValueC::ensureFloat( $result['credits_production'] );
        $retVal->fCreditAmount = PropertyValueC::ensureFloat( $result['credits_amount'] );
        $retVal->fCreditAlliance = PropertyValueC::ensureFloat( $result['credits_alliance'] );
        if (($retVal->fCreditProduction+$retVal->fCreditAlliance) != 0)
            $steuer=$retVal->fCreditAlliance/($retVal->fCreditProduction+$retVal->fCreditAlliance);
        $retVal->iPeopleWithoutWork = PropertyValueC::ensureInteger( $result['people_free'] );  
        $retVal->iPeopleWithWork = PropertyValueC::ensureInteger( $result['people_there'] );
        $retVal->iPeopleCouldWork = PropertyValueC::ensureInteger( $result['people_max'] );
        
      } else {

        $strCoords = $result['coords'];
        $iCoordsGal = PropertyValueC::ensureInteger($result['coords_gal']);
        $iCoordsSol = PropertyValueC::ensureInteger($result['coords_sol']);
        $iCoordsPla = PropertyValueC::ensureInteger($result['coords_pla']);
        $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);  

        $retVal->aKolos[$strCoords] = new DTOParserWirtschaftPlaniress2KoloResultC;
        $retVal->aKolos[$strCoords]->aCoords = $aCoords;
        $retVal->aKolos[$strCoords]->strCoords = PropertyValueC::ensureString( $strCoords );          
        $retVal->aKolos[$strCoords]->strPlanetName = PropertyValueC::ensureString( $result['planet_name'] );         

        $retVal->aKolos[$strCoords]->fFPProduction = PropertyValueC::ensureFloat( $result['fp_amount'] );  
        $retVal->aKolos[$strCoords]->fFPProductionWithoutMods = PropertyValueC::ensureFloat( $result['fp_clean'] );  
        $retVal->aKolos[$strCoords]->fResearchModGlobal = PropertyValueC::ensureFloat( $result['fp_mod_global'] );  
        $retVal->aKolos[$strCoords]->fResearchModPlanet = PropertyValueC::ensureFloat( $result['fp_mod_planet'] );        

        $retVal->aKolos[$strCoords]->fCreditProduction = PropertyValueC::ensureFloat( $result['credits_production'] );  
        $retVal->aKolos[$strCoords]->iSteuersatz = PropertyValueC::ensureInteger( $result['steuersatz'] );  

        $retVal->aKolos[$strCoords]->iPeopleWithoutWork = PropertyValueC::ensureInteger( $result['people_free'] );  
        $retVal->aKolos[$strCoords]->iPeopleWithWork = PropertyValueC::ensureInteger( $result['people_there'] );
        $retVal->aKolos[$strCoords]->iPeopleCouldWork = PropertyValueC::ensureInteger( $result['people_max'] );
        $retVal->aKolos[$strCoords]->iSexRate = PropertyValueC::ensureInteger( $result['people_production'] );

        $retVal->aKolos[$strCoords]->fZufr = PropertyValueC::ensureFloat( $result['zufr'] );
        $retVal->aKolos[$strCoords]->fZufrGrowing = PropertyValueC::ensureFloat( $result['zufr_production'] );

      }
    }
	# die Steuer kann leider erst berechnet werden, nachdem die Gesamtwerte ausgelesen wurden -> deshalb extra Schleife
	foreach( $retVal->aKolos as $kolo ){
		$kolo->fCreditAlliance = ($kolo->fCreditProduction > 0)?($kolo->fCreditProduction/(1-$steuer)-$kolo->fCreditProduction):0; 
	}
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
    $reKoloTypes         = $this->getRegExpKoloTypes();
    $reKoloCoords        = $this->getRegExpKoloCoords();
    $reFloatingDouble    = $this->getRegExpFloatingDouble();
    $reUnsignedDouble    = $this->getRegExpUnsignedDouble();
    $reDecimalNumber     = $this->getRegExpDecimalNumber();
    $reKoloNames         = $this->getRegExpSingleLineText();	//! Mac: nicht getRegExpSingleLineText3, da KoloNames auch kuerzer als 3 Zeichen sein koennen

    $regExp  = '/';

    $regExp .= '(?:(?P<is_sum>Gesamt)|';
    $regExp .= '(?P<planet_name>'.$reKoloNames.')';
    $regExp .= '\s';
    $regExp .= '(?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))';
    $regExp .= ')';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<fp_amount>'.$reUnsignedDouble.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<fp_clean>'.$reFloatingDouble.')';
    $regExp .= '\*';
    $regExp .= '\(';
    $regExp .= '(?P<fp_mod_planet>'.$reUnsignedDouble.')';
    $regExp .= '\+';
    $regExp .= '(?P<fp_mod_global>'.$reUnsignedDouble.')';
    $regExp .= '\)\)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?:(?P<credits_amount>'.$reFloatingDouble.')\n\(|)';
    $regExp .= '(?P<credits_production>'.$reFloatingDouble.')';
    $regExp .= '(?:\)\nAllisteuer\:\s(?P<credits_alliance>'.$reFloatingDouble.')|)';

    $regExp .= '(?:';
    $regExp .= '[\s\t]+';
    $regExp .= '(?P<steuersatz>\d{1,3})';
    $regExp .= '(?:\\\%|\%)';
    $regExp .= '[\s\t]+';
    $regExp .= '|[\s\t]+)';

    $regExp .= '(?P<people_free>'.$reDecimalNumber.')';
    $regExp .= '\s\/\s';
    $regExp .= '(?P<people_there>'.$reDecimalNumber.')';
    $regExp .= '\s\/\s';
    $regExp .= '(?P<people_max>'.$reDecimalNumber.'|\-\-)';
    $regExp .= '\n';
    $regExp .= '\((?P<people_production>'.$reDecimalNumber.')\)';

    $regExp .= '(?:[\s\t]+';
    $regExp .= '(?P<zufr>'.$reUnsignedDouble.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<zufr_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '|)';

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
