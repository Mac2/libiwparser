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
 * Result DTO of parser de_info_research
 */
class DTOParserInfoForschungResultC
{
   /**
   * @soap
   * @var string $strResearchName the name of the research
   */
  public $strResearchName = '';

   /**
   * @soap
   * @var string $strResearchComment the shortline comment of the research
   */
  public $strResearchComment = '';  

  /**
   * @soap
   * @var string $strResearchFirst the shortline of the first researcher
   */
  public $strResearchFirst = '';  
  
   /**
   * @soap
   * @var string $strStatus erforscht oder erforschbar
   */
  public $strStatus  = '';  

   /**
   * @soap
   * @var string $strAreaName the area
   */
  public $strAreaName = '';    

   /**
   * @soap
   * @var integer $iFP the amount of research points to be needed
   */
  public $iFP = 0; 

  /**
   * @soap
   * @var integer $iHS how many points does the research give
   */
  public $iHS = 0; 

   /**
   * @soap
   * @var integer $iPeopleResearched the people step for this research
   */
  public $iPeopleResearched = 0;   

   /**
   * @soap
   * @var integer $iResearchCosts the prozent of the full fp because of the people step
   */
  public $iResearchCosts = 0; 

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

  /**
   * @soap
   * @var array $aObjectsNeeded
   */
  public $aObjectsNeeded = array();
  
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
   * @var array $aBuildingLevelsDevelop
   */
  public $aBuildingLevelsDevelop = array();

  /**
   * @soap
   * @var array $aDefencesDevelop
   */
  public $aDefencesDevelop = array();

  /**
   * @soap
   * @var array $aGeneticsDevelop
   */
  public $aGeneticsDevelop = array();

  /**
   * @soap
   * @var array $aCosts further Costs for this research
   */
  public $aCosts = array();  

  /**
   * @soap
   * @var boolean $bIsPrototypResearch does this research allow to build a ship?
   */
  public $bIsPrototypResearch = false;

  /**
   * @soap
   * @var string $strPrototypName if the research is a prototyp research - the name of the prototyp
   */
  public $strPrototypName = '';

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////