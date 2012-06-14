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
 * Result DTO of parser de_xml
 */
class DTOParserXmlResultC
{
  /**
   * @soap
   * @var array $aKbLinks an array of objects of type
   *      DTOParserXmlResultLinkC
   */
  public $aKbLinks = array();

  /**
   * @soap
   * @var array $aSbLinks an array of objects of type
   *      DTOParserXmlResultLinkC
   */
  public $aSbLinks = array();

  /**
   * @soap
   * @var array $aUniversumLinks an array of objects of type
   *      DTOParserXmlResultLinkC
   */
  public $aUniversumLinks = array();

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserXmlResultLinkC
{
  /**
   * @soap
   * @var string $strUrl the url of the xml-link
   */
  public $strUrl = '';

  /**
   * @soap
   * @var integer $iId the ID of the xml-link
   */
  public $iId = 0;

  /**
   * @soap
   * @var string $strHash the hash of the xml-link
   */
  public $strHash = '';

  /**
   * @soap
   * @var string $strType the type of the xml-link
   */
  public $strType = '';

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
