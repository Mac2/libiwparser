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
 * Parser for the alli bank
 *
 * This parser is responsible for parsing the alli bank
 *
 * Its identifier: de_alli_kasse
 */
class ParserAlliKasseInhaltC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_alli_kasse_inhalt');
    $this->setName("Allianzkasse Kontostand");
    $this->setRegExpCanParseText('/Allianzkasse.*Kasseninhalt.*Auszahlung.*Auszahlungslog.*Auszahlungslog.*der\sletzten\sdrei\sWochen/smU');
    $this->setRegExpBeginData( '/Allianzkasse\sAllianzkasse/sm' );
    $this->setRegExpEndData( '/Auszahlung/sm' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserAlliKasseInhaltResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match( $regExp, $this->getText(), $aResult );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      $parserResult->bSuccessfullyParsed = true;

    $fCredits = $aResult['fCredits'];
    $retVal->fCredits    = PropertyValueC::ensureFloat( $fCredits );

    $strAlliance = $aResult['strAlliance'];
    $retVal->strAlliance    = PropertyValueC::ensureString( $strAlliance );
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

  $reFloatingDouble       = $this->getRegExpFloatingDouble();

    $regExp  = '/^';
    $regExp .= '(\(Wing (?P<strAlliance>.*)\)\s*)?';
    $regExp .= '(^.*$\n)+';
    $regExp .= '^Kasseninhalt\s';
    $regExp .= '(?P<fCredits>'.$reFloatingDouble.')\s(?:Credits|Kekse)';
    $regExp .= '$/m';

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
