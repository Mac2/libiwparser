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
 * Result DTO of parser de_wirtschaft_schiff
 */
class DTOParserMilSchiffUebersichtResultC
{
  /**
   * @soap
   * @var array $aKolos an array of objects of type
   *      DTOParserMilSchiffUebersichtKoloResultC, which represent the complete parsed kolos
   *      at the schiff economy list
   */
  public $aKolos = array();
  
  /**
   * @soap
   * @var array $aBuildings an array of objects of type
   *      DTOParserMilSchiffUebersichtSchiffResultC, which represent a building line
   *      at the building economy list
   */
  public $aSchiffe = array();
}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



class DTOParserMilSchiffUebersichtKoloResultC
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



class DTOParserMilSchiffUebersichtSchiffResultC
{
  
  /**
   * @soap
   * @var array (coords => schiff_count) $aCounts
   */
  public $aCounts = '';

  /**
   * @soap
   * @var integer $iCountGesamt count of all ships
   */
  public $iCountGesamt = '';

  /**
   * @soap
   * @var integer $iCountStat count of all ships waiting in orbit
   */
  public $iCountStat = '';

  /**
   * @soap
   * @var integer $iCountFlug count of all ships flying
   */
  public $iCountFlug = '';
  
  /**
   * @soap
   * @var string $strSchiffName the name of the schiff
   */
  public $strSchiffName = '';
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
