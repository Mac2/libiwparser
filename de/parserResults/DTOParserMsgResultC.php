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
 * @author Mac <MacXY@herr-der-mails.de>
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_msg
 */
class DTOParserMsgResultC
{
  /**
   * @soap
   * @var array $aMsgs an array of objects of type
   *      DTOParserMsgResultMsgC, which represent the messages
   *      in your postbox
   */
  public $aMsgs = array();

  /**
   * @soap
   * @var array $aTransportMsgs an array of references of type
   *      DTOParserMsgResultMsgTransportC, which represent the transport messages
   *      in your postbox
   */
  public $aTransportMsgs = array();

  public $aMassdriverMsgs = array();

  /**
   * @soap
   * @var array $aReverseMsgs an array of references of type
   *      DTOParserMsgResultMsgReverseC, which represent the reverse messages
   *      in your postbox
   */
  public $aReverseMsgs = array();

  /**
   * @soap
   * @var array $aGaveMsgs an array of references of type
   *      DTOParserMsgResultMsgGaveC, which represent the gave messages
   *      in your postbox
   */
  public $aGaveMsgs = array();

  /**
   * @soap
   * @var array $aTransfairMsgs an array of references of type
   *      DTOParserMsgResultMsgTransfairC, which represent the transfair messages
   *      in your postbox
   */
  public $aTransfairMsgs = array();

   /**
   * @soap
   * @var array $aScan*Msgs an array of references of type
   *      DTOParserMsgResultMsgScan*C, which represent the scan messages
   *      in your postbox
   */
  public $aScanSchiffeDefRessMsgs = array();
  public $aScanGebRessMsgs    = array();
  public $aScanGeoMsgs	      = array();
  public $aScanFailMsgs         = array();
  public $aSondierungMsgs       = array();
  
  /**
   * @soap
   * @var integer $iMessageCount
   */
  public $iMessageCount = -1;

  public $bSuccessfullyParsed = false;

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var boolean $bIsSystemNachricht is this a system generated message
   */
  public $bIsSystemNachricht = '';

  /**
   * @soap
   * @var string $strMsgTitle the title of the message
   */
  public $strMsgTitle = '';

  /**
   * @soap
   * @var string $strMsgAuthor the author of the message (or System)
   */
  public $strMsgAuthor = '';

  /**
   * @soap
   * @var string $strParserText the text of the message
   */
  public $strParserText = '';

  /**
   * @soap
   * @var string $eParserType the type of the message
   * @todo check how enums can be transformed and transported
   */
  public $eParserType = '';

  /**
   * @soap
   * @var int $iMsgDateTime since when the message has been created
   */
  public $iMsgDateTime = 0;

  public $bSuccessfullyParsed = false;

  public $aErrors = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgTransportC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var string $strFromUserName the user name of the owner of the fleet
   */
  public $strFromUserName = '';

  /**
   * @soap
   * @var string $strToUserName the other user
   */
  public $strToUserName = '';

  /**
   * @soap
   * @var string $strPlanetName the name of the planet
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var array $aSchiffe array of ident => schiff_name, schiff_count
   */
  public $aSchiffe = array();

  /**
   * @soap
   * @var array $aResources array of ident => resource_name, resource_count
   */
  public $aResources = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgScanSchiffeDefRessC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var int $iTimestamp when has this scan been created
   */
  public $iTimestamp = 0;

  /**
   * @soap
   * @var string $strOwnerName the name of the planets owner
   */
  public $strOwnerName = '';

  /**
   * @soap
   * @var string $strOwnerAllianceTag the name of the planet owners alliance
   */
  public $strOwnerAllianceTag = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var string $ePlanetType the type of the planet
   * @todo check how enums can be transformed and transported
   */
  public $ePlanetType = '';

  /**
   * @soap
   * @var string $eObjectType the type of the object at these coordinates
   * @todo check how enums can be transformed and transported
   */
  public $eObjectType = '';

  /**
   * @soap
   * @var array $aResources array of ident => resource_name, resource_count
   */
  public $aResources = array();

  /**
   * @soap
   * @var array $aShips array of ident => schiffe_name, schiffe_count
   */
  public $aSchiffe = array();
  public $astatSchiffe = array();

  /**
   * @soap
   * @var array $aDefences array of ident => defence_name, defence_count
   */
  public $aDefences = array();  

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgScanFailC extends DTOParserMsgResultMsgC
{
  
  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgSondierungC extends DTOParserMsgResultMsgC
{
  
  /**
   * @soap
   * @var string $strCoordsFrom the coords
   */
  public $strCoordsFrom = '';

  /**
   * @soap
   * @var array $aCoordsFrom array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoordsFrom = array();

  /**
   * @soap
   * @var string $strAllianceFrom
   */
  public $strAllianceFrom = '';
  
  /**
   * @soap
   * @var string $strCoordsTo the coords
   */
  public $strCoordsTo = '';

  /**
   * @soap
   * @var array $aCoordsTo array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoordsTo = array();
  
  /**
   * @soap
   * @var bool $bSuccess 
   */
  public $bSuccess=false;
          
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgScanGebRessC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var int $iTimestamp when has this geoscan been created
   */
  public $iTimestamp = 0;

  /**
   * @soap
   * @var string $strOwnerName the name of the planets owner
   */
  public $strOwnerName = '';

  /**
   * @soap
   * @var string $strOwnerAllianceTag the name of the planet owners alliance
   */
  public $strOwnerAllianceTag = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var string $ePlanetType the type of the planet
   * @todo check how enums can be transformed and transported
   */
  public $ePlanetType = '';

  /**
   * @soap
   * @var string $eObjectType the type of the object at these coordinates
   * @todo check how enums can be transformed and transported
   */
  public $eObjectType = '';

  /**
   * @soap
   * @var array $aResources array of ident => resource_name, resource_count
   */
  public $aResources = array();

  /**
   * @soap
   * @var array $aBuildings array of ident => building_name, building_count
   */
  public $aBuildings = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgGaveC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var string $strFromUserName the user name of the owner of the fleet
   */
  public $strFromUserName = '';

  /**
   * @soap
   * @var string $strToUserName the other user
   */
  public $strToUserName = '';

  /**
   * @soap
   * @var string $strPlanetName the name of the planet
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var boolean $bOutOfOrbit wurde die Flotte aus dem orbit übergeben.
   */
  public $bOutOfOrbit = false;

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var array $aSchiffe array of ident => schiff_name, schiff_count
   */
  public $aSchiffe = array();

  /**
   * @soap
   * @var array $aResources array of ident => resource_name, resource_count
   */
  public $aResources = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgTransfairC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var string $strFromUserName the user name of the owner of the fleet
   */
  public $strFromUserName = '';

  /**
   * @soap
   * @var string $strToUserName the other user
   */
  public $strToUserName = '';

  /**
   * @soap
   * @var string $strPlanetName the name of the planet
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var array $aCarriedResources array of ident => resource_name, resource_count (die mitgebrachten Resourcen)
   */
  public $aCarriedResources = array();

  /**
   * @soap
   * @var array $aFetchedResources array of ident => resource_name, resource_count (die abgeholten Resourcen)
   */
  public $aFetchedResources = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserMsgResultMsgReverseC extends DTOParserMsgResultMsgC
{
  /**
   * @soap
   * @var string $strPlanetName the name of the planet
   */
  public $strPlanetName = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';

  /**
   * @soap
   * @var array $aCoords array of (coords_gal,coords_sol,coords_pla) => int
   */
  public $aCoords = array();

  /**
   * @soap
   * @var array $aSchiffe array of ident => schiff_name, schiff_count
   */
  public $aSchiffe = array();

  /**
   * @soap
   * @var array $aResources array of ident => resource_name, resource_count
   */
  public $aResources = array();
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
