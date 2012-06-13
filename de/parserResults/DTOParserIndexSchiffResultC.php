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
 * Result DTO of parser de_index_schiff
 */
class DTOParserIndexSchiffResultC
{
  /**
   * @soap
   * @var array $aSchiff
   */
  public $aSchiff = array ();

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexSchiffResultSchiffC
{
  /**
   * @soap
   * @var string $strPlanetName
   */
  public $strPlanetName = '';

  public $strSchiffName = '';
  public $strCoords = '';
  public $strWerftTyp = '';
   
  /**
   * @soap
   * @var date 
   */
  public $iSchiffEnd = 0;
  
  /**
   * @soap
   * @var date 
   */
  public $iSchiffEndIn = 0;

  public $iAnzSchiff = 0;
  public $iAnzWerften = 0;

}