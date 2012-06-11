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
 * Result DTO of parser de_index_geb
 */
class DTOParserIndexGebResultC
{
  /**
   * @soap
   * @var array $aBuildings
   */
  public $aBuildings = array ();

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexGebResultGebC
{
  /**
   * @soap
   * @var string $strPlanetName
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords
   */
  public $aCoords = array();

  /**
   * @soap
   * @var string $strBuilding the name of the building
   */
  public $strBuilding = '';

  /**
   * @soap
   * @var int $iDateToExpire the number of seconds the building need to be built
   */
  public $iDateToExpire = 0;

  /**
   * @soap
   * @var int $iDateOfFinish the number of seconds since 1900
   */
  public $iDateOfFinish = 0;
}