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
 * Result DTO of parser de_forschung
 */
class DTOParserForschungResultC
{
  /**
   * @soap
   * @var array $aResearchsResearched
   */
  public $aResearchsResearched = array();

  /**
   * @soap
   * @var array $aResearchsProgress
   */
  public $aResearchsProgress = array(); 

  /**
   * @soap
   * @var array $aResearchsOpen
   */
  public $aResearchsOpen = array();

  /**
   * @soap
   * @var integer
   */
  public $iMalusRed = 0;
  public $iBonusRed = 0;
  public $iverschwFP = 0;

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * 
 */
class DTOParserForschungResearchedResultC
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
   * @var string $strAreaName the area
   */
  public $strAreaName = '';      

   /**
   * @soap
   * @var integer $iFP the amount of research points to be needed
   */
  public $iFP = ''; 

   /**
   * @soap
   * @var integer $iPeopleResearched the people step for this research
   */
  public $iPeopleResearched = '';   

   /**
   * @soap
   * @var integer $iResearchCosts the prozent of the full fp because of the people step
   */
  public $iResearchCosts = ''; 

   /**
   * @soap
   * @var integer $iUserResearchTime how long the user needs to research
   */
  public $iUserResearchTime = '';

    /**
   * @soap
   * @var array $aCosts further Costs for this research
   */
  public $aCosts = array();

   /**
   * @soap
   * @var enum $eState researched
   */
  public $eState = 'researched';  

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * 
 */
class DTOParserForschungOpenResultC
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
   * @var string $strAreaName the area
   */
  public $strAreaName = '';    

   /**
   * @soap
   * @var integer $iFP the amount of research points to be needed
   */
  public $iFP = ''; 

   /**
   * @soap
   * @var integer $iPeopleResearched the people step for this research
   */
  public $iPeopleResearched = '';   

   /**
   * @soap
   * @var integer $iResearchCosts the prozent of the full fp because of the people step
   */
  public $iResearchCosts = ''; 

   /**
   * @soap
   * @var integer $iUserResearchTime how long the user needs to research
   */
  public $iUserResearchTime = '';

    /**
   * @soap
   * @var array $aCosts further Costs for this research
   */
  public $aCosts = array();

   /**
   * @soap
   * @var enum $eState open
   */
  public $eState = 'open';  

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * 
 */
class DTOParserForschungProgressResultC
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
   * @var string $strAreaName the area
   */
  public $strAreaName = '';      

   /**
   * @soap
   * @var integer $iFP the amount of research points to be needed
   */
  public $iFP = ''; 

   /**
   * @soap
   * @var integer $iPeopleResearched the people step for this research
   */
  public $iPeopleResearched = '';   

   /**
   * @soap
   * @var integer $iResearchCosts the prozent of the full fp because of the people step
   */
  public $iResearchCosts = ''; 

   /**
   * @soap
   * @var integer $iUserResearchTime how long the user needs to research
   */
  public $iUserResearchTime = '';

    /**
   * @soap
   * @var integer $iUserResearchDuration seconds until research
   */
  public $iUserResearchDuration = ''; 

   /**
   * @soap
   * @var enum $eState progress
   */
  public $eState = 'progress';  

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
