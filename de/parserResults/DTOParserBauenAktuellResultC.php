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

class DTOParserBauenAktuellResultC
{
  /**
   * @soap
   * @var array $aBuildings
   */
  public $aBuildings = array ();

}

/**
 * Result DTO of parser de_bauen_aktuell
 */
class DTOParserBauenAktuellResultBuildingC
{
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

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////