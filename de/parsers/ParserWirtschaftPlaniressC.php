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
 * Parses a the Planiress
 *
 * This parser is responsible for parsing the planiress part 1
 *
 * Its identifier: de_wirtschaft_planiress
 */
class ParserWirtschaftPlaniressC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_wirtschaft_planiress');
    $this->setName("KoloRess&Uuml;bersicht Teil1");
    $this->setRegExpCanParseText('/Ressourcenkolo.{1,3}bersicht.*Lager\sund\sBunker\sanzeigen/s');
    $this->setRegExpBeginData( '/Kolonie\s+\w+\s+(?:Erdbeermarmelade|Stahl)\s+\w+\s+\w+\s+\w+\s+\w+\s+(Traubenzucker|Energie)/' );
    $this->setRegExpEndData( '/Lager\sund\sBunker\sanzeigen/' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserWirtschaftPlaniressResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );
    $ress = array('eisen' => 'Eisen','stahl' => 'Stahl','vv4a' => 'VV4A','chemie' => 'Brause','eis' => 'Eis','wasser' => 'Wasser','nrg' => 'Energie');

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      $parserResult->bSuccessfullyParsed = true;

      if( $this->getLagerBunkerVisible() )
      {
        $retVal->bLagerBunkerVisible = true;
      }
      else
      {
        $retVal->bLagerBunkerVisible = false;
      }

      foreach( $aResult as $result )
      {
        $strKoloType = $result['object_type'];
        $strKoloName = $result['planet_name'];
        $strCoords = PropertyValueC::ensureString($result['coords']);
        $iCoordsGal = PropertyValueC::ensureInteger($result['coords_gal']);
        $iCoordsSol = PropertyValueC::ensureInteger($result['coords_sol']);
        $iCoordsPla = PropertyValueC::ensureInteger($result['coords_pla']);
        $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);

        $retVal->aKolos[$strCoords] = new DTOParserWirtschaftPlaniressKoloResultC;
        $retVal->aKolos[$strCoords]->aCoords = $aCoords;
        $retVal->aKolos[$strCoords]->strCoords = PropertyValueC::ensureString( $strCoords );
        $retVal->aKolos[$strCoords]->strObjectType = PropertyValueC::ensureString( $strKoloType );
        $retVal->aKolos[$strCoords]->eObjectType = PropertyValueC::ensureEnum( $strKoloType, 'eObjectTypes' );
        $retVal->aKolos[$strCoords]->strPlanetName = PropertyValueC::ensureString( $strKoloName );

        foreach ($ress as $key => $name)
        {
          $ordr = new DTOParserWirtschaftPlaniressRessResultC();
          $ordr->strResourceName = PropertyValueC::ensureResource($name);
          if (isset($result[$key.'_vorrat']))
              $ordr->iResourceVorrat = PropertyValueC::ensureInteger($result[$key.'_vorrat']);

          if (isset($result[$key.'_production']))
              $ordr->fResourceProduction = PropertyValueC::ensureFloat($result[$key.'_production']);

          if ($retVal->bLagerBunkerVisible) {
              if (isset($result[$key.'_bunker']))
                  $ordr->iResourceBunker = PropertyValueC::ensureInteger($result[$key.'_bunker']);

              if (isset($result[$key.'_lager']))
              {
                $ordr->iResourceLager = PropertyValueC::ensureInteger($result[$key.'_lager']);
              }
          }
          $retVal->aKolos[$strCoords]->aData[] = $ordr;
        }
      }
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }

  }

  /////////////////////////////////////////////////////////////////////////////

  private function getLagerBunkerVisible()
  {
    $retVal = false;
    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      foreach( $aResult as $result )
      {
        if( !empty($result['eisen_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['stahl_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['vv4a_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['chemie_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['eis_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['wasser_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['nrg_bunker']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['eisen_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['stahl_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['vv4a_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['chemie_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['eis_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['wasser_lager']) )
        {
          $retVal = true;
          break;
        }
        if( !empty($result['nrg_lager']) )
        {
          $retVal = true;
          break;
        }
      }
    }

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    $reKoloTypes          = $this->getRegExpKoloTypes();
    $reKoloCoords         = $this->getRegExpKoloCoords();
    $reFloatingDouble     = $this->getRegExpFloatingDouble();
    $reUnsignedDouble     = $this->getRegExpUnsignedDouble();
    $reDecimalNumber      = $this->getRegExpDecimalNumber();
    $reKoloNames          = $this->getRegExpSingleLineText();

    $regExp  = '/';

    $regExp .= '(?P<planet_name>'.$reKoloNames.')';
    $regExp .= '\s';

    $regExp .= '(?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))';
    $regExp .= '\n';
    $regExp .= '\((?P<object_type>'.$reKoloTypes.')\)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<eisen_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<eisen_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<eisen_lager>\-\-\-)\n';
    $regExp .= '(?P<eisen_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<stahl_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<stahl_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<stahl_lager>\-\-\-)\n';
    $regExp .= '(?P<stahl_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<vv4a_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<vv4a_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<vv4a_lager>\-\-\-)\n';
    $regExp .= '(?P<vv4a_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<chemie_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<chemie_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<chemie_lager>'.$reDecimalNumber.')';
    $regExp .= '\n';
    $regExp .= '(?P<chemie_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<eis_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<eis_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<eis_lager>'.$reDecimalNumber.')';
    $regExp .= '\n';
    $regExp .= '(?P<eis_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<wasser_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<wasser_production>'.$reFloatingDouble.')';
    $regExp .= '\)';
    $regExp .= '(?:(\n+(?P<wasser_lager>'.$reDecimalNumber.')';
    $regExp .= '\n';
    $regExp .= '(?P<wasser_bunker>'.$reDecimalNumber.'))|)';

    $regExp .= '[\s\t]+';
    $regExp .= '(?P<nrg_vorrat>'.$reDecimalNumber.')';
    $regExp .= '\n\(';
    $regExp .= '(?P<nrg_production>'.$reFloatingDouble.')';
    $regExp .= '\)\n+';
    $regExp .= '(?:((?P<nrg_lager>'.$reDecimalNumber.')';
    $regExp .= '\n';
    $regExp .= '(?P<nrg_bunker>'.$reDecimalNumber.'))|)';

    $regExp  .= '/mx';

    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
