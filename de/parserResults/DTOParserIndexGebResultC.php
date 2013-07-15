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
   * @var array $aGeb
   */
  public $aGeb = array ();

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexGebResultGebC
{
  /**
   * @soap
   * @var string $strPlanetName string with the name of the planet
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords string with planetary coordinates
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords associative array with planetary coordinates
   */
  public $aCoords = array();

  /**
 * @soap
 * @var array $aGebName associative array with the buildings in the queue
 */
 public $aGebName = array();

}