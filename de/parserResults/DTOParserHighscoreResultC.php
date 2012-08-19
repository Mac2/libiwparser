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
 * Result DTO of parser de_highscore
 */
class DTOParserHighscoreResultC
{
  /**
   * @soap
   * @var array $aMembers an array of objects of type
   *      DTOParserAlliMemberlisteResultMemberC, which represent the members
   *      in your alliance
   */
  public $aMembers = array();

  /**
   * @soap
   * @var boolean $bDateOfEntryVisible
   */
  public $bDateOfEntryVisible = false;

  /**
   * @soap
   * @var integer $iTimestamp
   */
  public $iTimestamp = 0;
  
  /**
   * @soap
   * @var string $strType Type of Highscore (e.g. Demokraten,Kommunisten,..)
   */
  public $strType = '';
  
}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



class DTOParserHighscoreResultMemberC
{
  /**
   * @soap
   * @var string $strName the name of the member
   */
  public $strName = '';

  /**
   * @soap
   * @var string $strAllianz the name of the member alliance
   */
  public $strAllianz = '';

  /**
   * @soap
   * @var int $iDabeiSeit since when the member is in IW
   */
  public $iDabeiSeit = -1;

  public $iGesamtP = 0;
  public $iFP = 0;
  public $iGebP = 0;
  public $iPperDay = 0;

  public $iPosChange = 0;
  public $iPos = 0;

}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
