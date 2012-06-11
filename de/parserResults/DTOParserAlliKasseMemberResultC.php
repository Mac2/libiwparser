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
 * Result DTO of parser de_alli_kasse_member
 */
class DTOParserAlliKasseMemberResultC
{

   /**
   * @soap
   * @var array $aMember the array of DTOParserAlliKasseMemberResultMemberC
   */
  public $aMember = array(); 
  public $strAlliance = ""; 

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class DTOParserAlliKasseMemberResultMemberC
{

   /**
   * @soap
   * @var boolean $bHasAccepted has the user accepted the alli taxes?
   */
  public $bHasAccepted = false;

  /**
   * @soap
   * @var string $strUser user
   */
  public $strUser = ""; 

  /**
   * @soap
   * @var integer $iCreditsPerDay how many credits does the user pay the alliance per day
   */
  public $iCreditsPerDay = ""; 

  /**
   * @soap
   * @var integer $iDateTime when does the user acceptet the taxes?
   */
  public $iDateTime = 0; 

  /**
   * @soap
   * @var float $fCreditsPaid the sum of credits tha the user paid the alliance
   */
  public $fCreditsPaid = 0;   

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
