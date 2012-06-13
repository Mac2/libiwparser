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

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'ParserBaseC.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'ParserI.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'HelperC.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .
              '..'              . DIRECTORY_SEPARATOR .
              'parserResults'   . DIRECTORY_SEPARATOR .
              'DTOParserMsgResultC.php' );

/**
 * Parser for SubMessages Abholung
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_transfair
 */
class ParserMsgTransfairC extends ParserMsgBaseC implements ParserMsgI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_msg_transfair');
    $this->setCanParseMsg('Ressourcen abholen');
  }

 /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserMsgI::parseMsg()
   */
  public function parseMsg( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserMsgResultMsgTransfairC();
    $retVal =& $parserResult->objResultData;

    $regExpText = $this->getRegularExpressionText();
    $msg = $this->getMsg();

    foreach($msg as $key => $value)
    {
      $retVal->$key = $value;
    }

    $aResultText = array();
    $fRetValText = preg_match($regExpText, $msg->strParserText, $aResultText);

    if( $fRetValText !== false && $fRetValText > 0)
    {
      $parserResult->bSuccessfullyParsed = true;

      $strPlanetName = '';
      $strFromUserName = '';
      $strToUserName = '';
      $strCoords = '';
      $aCoords = array();
      $iCoordsGal = -1;
      $iCoordsSol = -1;
      $iCoordsPla = -1;
      $aCarriedResources = array();
      $aFetchedResources = array();

      $strPlanetName = $aResultText['planet_name'];
      $strFromUserName = isset($aResultText['from_user_name']) ? $aResultText['from_user_name'] : "";
      $strToUserName = isset($aResultText['to_user_name']) ? $aResultText['to_user_name'] : "";
      $strCoords =  $aResultText['coords'];
      $iCoordsGal = PropertyValueC::ensureInteger( $aResultText['coords_gal'] );
      $iCoordsSol = PropertyValueC::ensureInteger( $aResultText['coords_sol'] );
      $iCoordsPla = PropertyValueC::ensureInteger( $aResultText['coords_pla'] );
      $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);

      if (isset($aResultText['carried_resources']))
      {
        $aResultResources = array();
        $regExpResources = $this->getRegularExpressionResources();
        $fRetValResources = preg_match_all( $regExpResources, $aResultText['carried_resources'], $aResultResources, PREG_SET_ORDER );

        if( $fRetValResources !== false && $fRetValResources > 0 )
        {
          foreach( $aResultResources as $result )
          {
            $strResourceName = $result['resource_name'];
            $iResourceCount = $result['resource_count'];
            $strResourceName = PropertyValueC::ensureResource( $strResourceName );
            $iResourceCount = PropertyValueC::ensureInteger( $iResourceCount );
            $aCarriedResources[md5($strResourceName)] = array('resource_name' => $strResourceName,'resource_count' => $iResourceCount);
          }
        }
      }

      if (isset($aResultText['fetched_resources']))
      {
        $aResultResources = array();
        $regExpResources = $this->getRegularExpressionResources();
        $fRetValResources = preg_match_all( $regExpResources, $aResultText['fetched_resources'], $aResultResources, PREG_SET_ORDER );

        if( $fRetValResources !== false && $fRetValResources > 0 )
        {
          foreach( $aResultResources as $result )
          {
            $strResourceName = $result['resource_name'];
            $iResourceCount = $result['resource_count'];
            $strResourceName = PropertyValueC::ensureResource( $strResourceName );
            $iResourceCount = PropertyValueC::ensureInteger( $iResourceCount );
            $aFetchedResources[md5($strResourceName)] = array('resource_name' => $strResourceName,'resource_count' => $iResourceCount);
          }
        }
      }

      $retVal->strPlanetName = PropertyValueC::ensureString( $strPlanetName );
      $retVal->strFromUserName = PropertyValueC::ensureString( $strFromUserName );
      $retVal->strToUserName = PropertyValueC::ensureString( $strToUserName );
      $retVal->strCoords = PropertyValueC::ensureString( $strCoords );
      $retVal->aCoords = $aCoords;
      $retVal->aCarriedResources = $aCarriedResources;
      $retVal->aFetchedResources = $aFetchedResources;
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the TransfairMsg pattern.';
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   */
  private function getRegularExpressionText()
  {
    $reUserName = $this->getRegExpUserName();

    #Just even don't think to ask anything about this regexp, fu!
    $regExp  = '/
        Eine\sFlotte\sist\sauf\sdem\sPlaneten
        (?:\s(?P<planet_name>.*)\s|\s)
        (?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))
        \sangekommen\.
        (?:
        \sDer\sAbsender\sist\s
        (?P<from_user_name>'.$reUserName.')
        |)
        (?:
        \sDer\sEmpf.nger\sist\s
        (?P<to_user_name>'.$reUserName.')
        |)
        [\s\n\r\t]+
        Es\swurden\sfolgende\sSachen\sangeliefert
        (?:
        [\s\n\r\t]+
        Ressourcen
        [\s\n\r\t]+
        (?P<carried_resources>
        ([\w\süöä]+[\s\t]+\d+[\s\n\r\t]*)+
        )
        |)
        [\s\n\r\t]+
        Es\swurden\sfolgende\sSachen\sabgeholt
        (?:
        [\s\n\r\t]+
        Ressourcen
        [\s\n\r\t]+
        (?P<fetched_resources>
        ([\w\süöä]+[\s\t]+\d+[\s\n\r\t]*)+
        )
        |)

        /mx';
    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * For debugging with "The Regex Coach" which doesn't support named groups
   */
  private function getRegularExpressionWithoutNamedGroups()
  {
    $retVal = $this->getRegularExpressionText();

    $retVal = preg_replace( '/\?P<\w+>/', '', $retVal );

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////