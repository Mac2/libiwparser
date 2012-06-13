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
 * Result DTO of parser de_wirtschaft_def
 */
class DTOParserWirtschaftDefResultC
{

  /**
   * @soap
   * @var array $aKolos an array of objects of type
   *      DTOParserWirtschaftDefKoloResultC, which represent the complete parsed kolos
   *      at the defence economy list
   */
  public $aKolos = array();  
  
  public $aDefences = array();
  
  public $aSlots = array();
  
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftDefKoloResultC
{
  /**
   * @soap
   * @var array $aCoords the coords
   */
  public $aCoords = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';  

  /**
   * @soap
   * @var string $strObjectType the type of this kolo
   */
  public $strObjectType = '';  
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftDefSlotResultC
{
  
  /**
   * @soap
   * @var string $strSlotType the name of the DefenceType
   */
  public $strSlotType = '';    
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftDefDefenceResultC
{

  /**
   * @soap
   * @var array (coords => defence_count) $aCounts 
   */
  public $aCounts = '';    

  /**
   * @soap
   * @var array (coords => defence_count) $aCounts 
   */
  public $aMaxCounts = '';    
  
  /**
   * @soap
   * @var string $strDefenceName the name of the defence
   */
  public $strDefenceName = '';     
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////