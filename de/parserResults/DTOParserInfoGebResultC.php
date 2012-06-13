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
 * Result DTO of parser de_info_geb
 */
class DTOParserInfoGebResultC
{
   /**
   * @soap
   * @var string $strGebName the name of the building
   */
  public $strGebName = '';

   /**
   * @soap
   * @var string $strGebComment the shortline comment of the building
   */
  public $strGebComment = '';  

   /**
   * @soap
   * @var integer $iaktSufe
   */
  public $bIsStufenGeb = false;
  public $iStufe = 0;    

  /**
   * @soap
   * @var integer $iHS how many points does the building give
   */
  public $iHS = 0;
  public $imaxAnz = 0;  

  /**
   * @soap
   * @var array $aResearchsNeeded
   */
  public $aResearchsNeeded = array();

  /**
   * @soap
   * @var array $aBuildingsNeeded
   */
  public $aBuildingsNeeded = array();
  
  public $aDestroyable = array();

  /**
   * @soap
   * @var array $aResearchsDevelop
   */
  public $aResearchsDevelop = array();

  /**
   * @soap
   * @var array $aBuildingsDevelop
   */
  public $aBuildingsDevelop = array();


  /**
   * @soap
   * @var array $aCosts further Costs, etc for this building
   *      arrays because rise of costs possible
   */
  public $aCosts = array();
  public $aMaintenance = array();
  public $aBuildTime = array();
  public $aEffect = array();


  public $aPlanetPropertiesNeeded = array();
  public $strObjectTypesNeeded = "";
  public $strPlanetNeeded = "";
  public $strRiseType = "";

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
