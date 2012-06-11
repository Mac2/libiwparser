<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <benjamin.woester@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Benjamin Wöster
 * ----------------------------------------------------------------------------
 */
/**
 * @author Benjamin Wöster <benjamin.woester@googlemail.com>
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * Parser for the alliance member list
 *
 * This parser is responsible for parsing the alliance member list.
 *
 * Its identifier: de_alli_memberliste
 */
class ParserAlliMemberlisteC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_alli_memberliste');
    $this->setName("Allianzmitgliederliste");
    $this->setRegExpCanParseText('/Mitgliederliste\s+?Name\s+?Rang/sm');
    $this->setRegExpBeginData( $this->getRegExpCanParseText() );
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
    $parserResult->objResultData = new DTOParserAlliMemberlisteResultC();
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

      if( $this->getUserTitleVisible() )
      {
        $retVal->bUserTitleVisible = true;
      }
      else
      {
        $retVal->bUserTitleVisible = false;
      }

      foreach( $aResult as $result )
      {
        $iDateOfEntry = -1;
        $strUserTitle = '';

        if( $retVal->bDateOfEntryVisible === true )
        {
          $iDateOfEntry = HelperC::convertDateToTimestamp($result['dateOfEntry']);
        }
        
        if( $retVal->bUserTitleVisible === true )
        {
          $strUserTitle = $result['userTitle'];
        }

        $member = new DTOParserAlliMemberlisteResultMemberC;

        $member->strName    = PropertyValueC::ensureString( $result['userName'] );
        $member->eRank      = PropertyValueC::ensureString( $result['userRank'] );
        $member->iDabeiSeit = PropertyValueC::ensureInteger( $iDateOfEntry );
        $member->strTitel   = PropertyValueC::ensureString( $strUserTitle );

        $member->iGesamtP = PropertyValueC::ensureInteger( $result['userGesP'] );
        $member->iFP      = PropertyValueC::ensureInteger( $result['userFP'] );
        $member->iGebP    = PropertyValueC::ensureInteger( $result['userGebP'] );
        $member->iPperDay = PropertyValueC::ensureInteger( $result['userPerDay'] );

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

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    /**
    * die Daten sind Zeilen, von denen jede folgendermaßen aussieht:
    * Name | Rang | dabei seit | Titel
    */

    $reName       = $this->getRegExpUserName();
    $reRang       = $this->getRegExpUserRank_de();
    $reDabeiSeit  = $this->getRegExpDate();
    $reTitel      = $this->getRegExpUserTitle();
    $rePoints     = $this->getRegExpDecimalNumber();

    $regExp  = '/^';
    $regExp .= '(?P<userName>'        . $reName       . ')\s+?';
    $regExp .= '(?P<userRank>'        . $reRang       . ')\s+?';

    $regExp .= '(?P<userGebP>'        . $rePoints       . ')\s+?';
    $regExp .= '(?P<userFP>'        . $rePoints       . ')\s+?';
    $regExp .= '(?P<userGesP>'        . $rePoints       . ')\s+?';
    $regExp .= '(?P<userPerDay>'        . $rePoints       . ')\s+?';

    $regExp .= '(?P<dateOfEntry>'     . $reDabeiSeit  . '|---)\s*?';
    $regExp .= '(?P<userTitle>'       . $reTitel      . '|)\s*';  //title might be empty
    $regExp .= '\n/m';
    
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
