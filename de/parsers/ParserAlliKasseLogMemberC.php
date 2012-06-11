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
 * @author Mac <MacXY@herr-der-mails.de>
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
 * Its identifier: de_alli_kasse_log_member
 */
class ParserAlliKasseLogMemberC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_alli_kasse_log_member');
    $this->setName("Allianzkasse Auszahlungen(Mitglieder)");
    $this->setRegExpCanParseText('/Allianzkasse.*Kasseninhalt.*Auszahlung.*Auszahlungslog.*Auszahlungslog.*der\sletzten\sdrei\sWochen/smU');
    $this->setRegExpBeginData( '/Allianzkasse\sAllianzkasse/sm' );
    $this->setRegExpEndData( '/Auszahlungslog\san\sWings\/etc\sder\sletzten\sdrei\sWochen/smU' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   * @todo: Parsen von eingezahlten Credits, aufgrund Bankmangel noch nicht nachvollziehbar wie das aussieht.
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserAlliKasseLogResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );
    if( $fRetVal !== false && $fRetVal > 0 )
    {
      $parserResult->bSuccessfullyParsed = true;
      $strAlliance = "";
      foreach( $aResult as $result )
      {
        $log = new DTOParserAlliKasseLogMemberResultC;

        $iDateTime = HelperC::convertDateTimeToTimestamp($result['reDateTime']);
        $iCredits = $result['iCredits'];

        $log->strFromUser    = PropertyValueC::ensureString( $result['strFromUser'] );
        $log->strToUser      = PropertyValueC::ensureString( $result['strToUser'] );
        if (isset($result['strReason']))
               $log->strReason      = PropertyValueC::ensureString( $result['strReason'] );
        $log->iDateTime = PropertyValueC::ensureInteger( $iDateTime );
        $log->iCredits   = PropertyValueC::ensureInteger( $iCredits );
        $retVal->aLogs[] = $log;
        if (isset($result['strAlliance']) && !empty($result['strAlliance'])) $strAlliance = PropertyValueC::ensureString( $result['strAlliance'] );
      }
      $retVal->strAlliance      = $strAlliance;
    }
    //! Mac: klappt noch nicht richtig, da das nur eigentlich nur bei "leerem" Input kommen sollte - nicht 0 Matches
    else if( $fRetVal !== false && $fRetVal == 0 )
    {
      $parserResult->bSuccessfullyParsed = true;
      $parserResult->aErrors[] = 'no Data found';
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

  $reDateTime      = $this->getRegExpDateTime();
  $reFromUser         = $this->getRegExpUserName();
  $reToUser         = $this->getRegExpUserName();
  $reInteger       = $this->getRegExpDecimalNumber();
  $reReason       = $this->getRegExpSingleLineText();

  $regExp  = '/^';
  $regExp .= '((\(Wing\s(?P<strAlliance>.*)\)\s*)?';
  $regExp .= '(^.*$\n)+';
  $regExp .= '^Auszahlungslog\san\sSpieler\sw.{1,3}hrend\sder\sletzten\sdrei\sWochen\s)?';
//  $regExp .= '(?:';
  $regExp .= '(?P<reDateTime>'        . $reDateTime       . ')';
  $regExp .= '\svon\s';
  $regExp .= '(?P<strFromUser>'        . $reFromUser       . ')';
  $regExp .= '\san\s';
  $regExp .= '(?P<strToUser>'        . $reToUser       . ')';
  $regExp .= '\s';
  $regExp .= '(?P<iCredits>'        . $reInteger       . ')';  
  $regExp .= '\s(Credits|Kekse)\sausgezahlt';
  $regExp .= '(?:';
  $regExp .= '(?:\sGrund\swar\s';
  $regExp .= '(?P<strReason>'        . $reReason       . ')';  
  $regExp .= '\.)';
  $regExp  .= '|)';
//  $regExp .= ')*';
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
