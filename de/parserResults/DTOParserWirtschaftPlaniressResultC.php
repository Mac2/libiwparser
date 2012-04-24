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
 * Result DTO of parser de_wirtschaft_planiress
 */
class DTOParserWirtschaftPlaniressResultC
{
  /**
   * @soap
   * @var array $aKolos of DTOParserWirtschaftPlaniressKoloResultC
   */
  public $aKolos = array();

  /**
   * @soap
   * @var boolean $bLagerBunkerVisible
   */
  public $bLagerBunkerVisible = false;

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 */
class DTOParserWirtschaftPlaniressKoloResultC
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

  /**
   * @soap
   * @var string $eObjectType the type of the object at these coordinates
   * @todo check how enums can be transformed and transported
   */
  public $eObjectType = '';

  /**
   * @soap
   * @var string $strPlanetName name of the object
   */
  public $strPlanetName = '';

   /**
   * @soap
   * @var array $aData of DTOParserWirtschaftPlaniressRessResultC
   */
  public $aData = array();      
}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 */
class DTOParserWirtschaftPlaniressRessResultC
{
  /**
   * @soap
   * @var string $strResourceName name of the resource
   */
  public $strResourceName = array();

  /**
   * @soap
   * @var integer $iResourceVorrat amount of ress at the planet
   */
  public $iResourceVorrat = 0;

  /**
   * @soap
   * @var float $fResourceProduction production of the resource
   */
  public $fResourceProduction = 0.00;

  /**
   * @soap
   * @var integer $iResourceBunker what can be stored without to be raided
   */
  public $iResourceBunker = 0;

  /**
   * @soap
   * @var integer $iResourceLager storage capa (or 0 at eisen,vv4a,stahl)
   */
  public $iResourceLager = 0;

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////