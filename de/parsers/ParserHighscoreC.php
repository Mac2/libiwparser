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
 * Parser for the icewars highscore
 *
 * This parser is responsible for parsing the global icewars highscore
 *
 * Its identifier: de_highscore
 */
class ParserHighscoreC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_highscore');
    $this->setName('Ingame Highscore (Spieler)');
    $this->setRegExpCanParseText('/Highscore\s+Highscore.+Highscore.+Pos\s+?Name\s+?Allianz/sm');
    $this->setRegExpBeginData('/Highscore\s-\sMen.{1,3}.+Highscore\s+Highscore/sm');
    $this->setRegExpEndData( '' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   * @todo Points may be hidden (FP,Geb,Gesamt)
   * @todo If points are hidden, does this also affect points/day?
   * @todo date of entry may be hidden
   * @todo user title may be hidden
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserHighscoreResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;
    $this->stripTextToData();
    
    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText()."\n", $aResult, PREG_SET_ORDER );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      $parserResult->bSuccessfullyParsed = true;
      
      if( $this->getDateOfEntryVisible() )
      {
        $retVal->bDateOfEntryVisible = true;
      }
      else
      {
        $retVal->bDateOfEntryVisible = false;
      }

      foreach( $aResult as $result )
      {
        $iDateOfEntry = -1;

        if( $retVal->bDateOfEntryVisible === true )
        {
          $iDateOfEntry = HelperC::convertDateToTimestamp($result['dateOfEntry']);
        }

        $member = new DTOParserHighscoreResultMemberC;

        $member->strName    = PropertyValueC::ensureString( $result['userName'] );
        $member->strAllianz    = PropertyValueC::ensureString( $result['userAllianz'] );
        $member->iDabeiSeit = PropertyValueC::ensureInteger( $iDateOfEntry );

        $member->iGesamtP = PropertyValueC::ensureInteger( $result['userGesP'] );
        $member->iFP      = PropertyValueC::ensureInteger( $result['userFP'] );
        $member->iGebP    = PropertyValueC::ensureInteger( $result['userGebP'] );
        $member->iPperDay = PropertyValueC::ensureInteger( $result['userPerDay'] );

        //! Position und Aenderung nicht ablegen, da abh. von angezeigter Sortierung
        $retVal->aMembers[] = $member;
      }
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }

  }

  /////////////////////////////////////////////////////////////////////////////

  private function getDateOfEntryVisible()
  {
    $retVal = false;
    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match( $regExp, $this->getText(), $aResult );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      if( $aResult['dateOfEntry'] !== '---' )
      {
        $retVal = true;
      }
    }

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////
/*
  private function getUserTitleVisible()
  {
    $retVal = false;
    $regExp = $this->getRegularExpression();

    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $this->getText(), $aResult, PREG_SET_ORDER );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
      foreach( $aResult as $result )
      {
        if( $result['userTitle'] !== '---' )
        {
          $retVal = true;
          break;
        }
      }
    }

    return $retVal;
  }
*/
  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    /**
    * die Daten sind Zeilen, von denen jede folgendermaßen aussieht:
    * Pos | Name | Allianz | Gebpkt | FP | Gesamtpkt | Pkt/Tag | Pos Aenderung| dabei seit
    */

    $reName       = $this->getRegExpUserName();
    $reAllianz    = $this->getRegExpSingleLineText();
    $reRang       = $this->getRegExpUserRank_de();
    $reDabeiSeit  = $this->getRegExpDate();
    $reTitel      = $this->getRegExpUserTitle();
    $rePoints     = $this->getRegExpDecimalNumber();
    $rePoints2     = $this->getRegExpFloatingDouble();

    $regExp  = '/^';
    $regExp .= '(?P<userPos>'        . '\d+'       . ')\s+?';
    $regExp .= '(?P<userName>'        . $reName       . ')\s+?';
    $regExp .= '(?:\[(?P<userAllianz>'        . $reAllianz       . ')\]\s+?)?';

    $regExp .= '(?P<userGebP>'        . $rePoints       . ')\s+?';
    $regExp .= '(?P<userFP>'        . $rePoints       . ')\s+?';
    $regExp .= '(?P<userGesP>'        . $rePoints2       . ')\s+?';
    $regExp .= '(?P<userPerDay>'        . $rePoints2       . ')\s+?';
    $regExp .= '(?P<userChange>(?:'        . $rePoints       . '|neu))\s+?';
//
    $regExp .= '(?P<dateOfEntry>'     . $reDabeiSeit  . '|---)\s*?';
    $regExp .= '[\n\r\t]+/m';
    
    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * For debugging with "The Regex Coach" which doesn't support named groups
   */
  private function getRegularExpressionWithoutNamedGroups()
  {
    return HelperC::removeNamedGroups( $this->getRegularExpression() );
  }
  
  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
