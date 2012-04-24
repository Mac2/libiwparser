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
class DTOParserWirtschaftPlaniress2ResultC
{
  /**
   * @soap
   * @var array $aKolos of DTOParserWirtschaftPlaniressKoloResultC
   */
  public $aKolos = array();

  /**
   * @soap
   * @var integer $iFPProduction the fp production with all mods
   */
  public $iFPProduction = 0;

  /**
   * @soap
   * @var float $fFPProductionWithoutMods the fp production only from the flabs without mods
   */
  public $fFPProductionWithoutMods = 0.00;

  /**
   * @soap
   * @var float $fResearchModGlobal global research mod
   */
  public $fResearchModGlobal = 0.00;

  /**
   * @soap
   * @var float $fCreditProduction the credit produktion
   */
  public $fCreditProduction = 0.00;

  /**
   * @soap
   * @var float $fCreditAmount the credit amount
   */
  public $fCreditAmount = 0.00;

  /**
   * @soap
   * @var float $fCreditAlliance the credit alliance tax
   */
  public $fCreditAlliance = 0.00;

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 */
class DTOParserWirtschaftPlaniress2KoloResultC
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
   * @var string $strPlanetName the Koloname
   */

  public $strPlanetName = '';

  /**
   * @soap
   * @var float $fFPProduction the fp production with all mods
   */
  public $fFPProduction = 0.00;

  /**
   * @soap
   * @var float $fFPProductionWithoutMods the fp production only from the flabs without mods
   */
  public $fFPProductionWithoutMods = 0.00;

  /**
   * @soap
   * @var float $fResearchModGlobal global research mod
   */
  public $fResearchModGlobal = 0.00;

  /**
   * @soap
   * @var float $fResearchModPlanet local research mod
   */
  public $fResearchModPlanet = 0.00;

  /**
   * @soap
   * @var float $fCreditProduction the credit produktion
   */
  public $fCreditProduction = 0.00;

  /**
   * @soap
   * @var integer $iSteuersatz from 0 to 100% the taxes
   */
  public $iSteuersatz = 0;

  /**
   * @soap
   * @var integer $iPeopleWithoutWork
   */
  public $iPeopleWithoutWork = 0;

  /**
   * @soap
   * @var integer $iPeopleWithWork
   */
  public $iPeopleWithWork = 0;

  /**
   * @soap
   * @var integer $iPeopleCouldWork the maximum people
   */
  public $iPeopleCouldWork = 0;

  /**
   * @soap
   * @var integer $iSexRate the growing rate of the people
   */
  public $iSexRate = 0;

  /**
   * @soap
   * @var float $fZufr Zufriedenheit
   */
  public $fZufr = 0.00;

  /**
   * @soap
   * @var float $fZufrGrowing Zufriedenheitsänderung
   */
  public $fZufrGrowing = 0.00;

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
