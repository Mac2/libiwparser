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
 * Result DTO of parser de_index_fleet
 */
class DTOParserIndexFleetResultC
{
  /**
   * @soap
   * @var string $strType
   */
  public $strType = '';


  /**
   * @soap
   * @var array $aFleets
   */
  public $aFleets = array ();

  /**
   * @soap
   * @var bool $bObjectsVisible
   */
  public $bObjectsVisible = false;

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexFleetResultFleetC
{
  /**
   * @soap
   * @var string $strPlanetNameTo
   */
  public $strPlanetNameTo = '';

  /**
   * @soap
   * @var string $strCoordsTo
   */
  public $strCoordsTo = '';

  /**
   * @soap
   * @var array $aCoordsTo
   */
  public $aCoordsTo = array();

  /**
   * @soap
   * @var string $strPlanetNameFrom
   */
  public $strPlanetNameFrom = '';

  /**
   * @soap
   * @var string $strUserNameFrom
   */
  public $strUserNameFrom = '';

  /**
   * @soap
   * @var string $strCoordsFrom
   */
  public $strCoordsFrom = '';

  /**
   * @soap
   * @var array $aCoordsFrom
   */
  public $aCoordsFrom = array();

  /**
   * @soap
   * @var unix $iAnkunft
   */
  public $iAnkunft = array();

  /**
   * @soap
   * @var unix $iAnkunftIn
   */
  public $iAnkunftIn = array();

  /**
   * @soap
   * @var enum $eTransfairType (übergabe|Transport)
   */
  public $eTransfairType = array();

  /**
   * @soap
   * @var array $aObjects the things that will be transporter or that are flying to the planet
   */
  public $aObjects = array();

}