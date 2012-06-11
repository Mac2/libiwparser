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
 * Result DTO of parser de_index_research
 */
class DTOParserIndexResearchResultC
{
  /**
   * @soap
   * @var array $aFleets
   */
  public $aResearch = array ();

}

/**
 * Sub DTO with the Data
 */
class DTOParserIndexResearchResultResearchC
{
  /**
   * @soap
   * @var string $strResearchName
   */
  public $strResearchName = '';
   
  /**
   * @soap
   * @var date 
   */
  public $iResearchEnd = array();
  
  /**
   * @soap
   * @var date 
   */
  public $iResearchEndIn = array();
  

}