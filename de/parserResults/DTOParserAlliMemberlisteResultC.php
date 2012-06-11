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
 * @author Mac <MacXY@herr-der-mails.de> 
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * Result DTO of parser de_alli_memberliste
 */
class DTOParserAlliMemberlisteResultC
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
   * @var boolean $bUserTitleVisible
   */
  public $bUserTitleVisible = false;
}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



class DTOParserAlliMemberlisteResultMemberC
{
  /**
   * @soap
   * @var string $strName the name of the member
   */
  public $strName = '';

  /**
   * @soap
   * @var string $eRank the rank of the member
   * @todo check how enums can be transformed and transported
   */
  public $eRank = '';

  /**
   * @soap
   * @var int $iDabeiSeit since when the member is in the alliance
   */
  public $iDabeiSeit = 0;

  /**
   * @soap
   * @var string $strTitle the members title
   */
  public $strTitel = '';

  public $iGesamtP = 0;
  public $iFP = 0;
  public $iGebP = 0;
  public $iPperDay = 0;


}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
