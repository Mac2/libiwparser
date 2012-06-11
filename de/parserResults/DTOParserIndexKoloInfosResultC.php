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
 * Result DTO of parser de_index_koloinfo
 */
class DTOParserIndexKoloInfosResultC
{
  /**
   * @soap
   * @var array $aKolos
   */
  public $aKolos = array ();

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexKoloInfosResultKoloInfoC
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
   * @var string $strObjectTyp
   */
  public $strObjectTyp = '';
  
  /**
   * @soap
   * @var array $aCoords
   */
  public $aCoords = array();

  /**
   * @soap
   * @var array $aScanRange
   */
  public $aScanRange = array();

  /**
   * @soap
   * @var array $aLastScan
   */
  public $aLastScan = array();

  /**
   * @soap
   * @var string $iLB
   */
  public $iLB = "";

  /**
   * @soap
   * @var array $aKB
   */
  public $aKB = array();

  /**
   * @soap
   * @var array $aSB
   */
  public $aSB = array();

  /**
   * @soap
   * @var array $aAB
   */
  public $aAB = array();

  /**
   * @soap
   * @var array $aSB
   */
  public $aKolo = array();

  public $aPlanDeff = array();

  public $aSchiffe = array();

  public $strProblems = "";
}