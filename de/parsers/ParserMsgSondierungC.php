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
 * Parser for SubMessages Sondierungen
 *
 * This parser is responsible for parsing messages and selecting waste
 *
 * Its identifier: de_msg_sondierungen
 */
class ParserMsgSondierungC extends ParserMsgBaseC implements ParserMsgI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_msg_sondierungen');
    $this->setCanParseMsg('');
  }

 /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserMsgI::parseMsg()
   */
  public function parseMsg( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserMsgResultMsgSondierungC();
    $retVal =& $parserResult->objResultData;

    $regExpText = $this->getRegularExpressionText();
    $msg = $this->getMsg();
    
    foreach($msg as $key => $value) {
        $retVal->$key = $value;
    }

    $aResultText = array();
    $fRetValText = preg_match( $regExpText, $msg->strParserText, $aResultText);

    if( $fRetValText !== false && $fRetValText > 0)
    {

        $retVal->bSuccessfullyParsed = true;

        $aResultTitle = array();
        $fRetValTitle = preg_match( $this->getRegularExpressionTitle(), $msg->strMsgTitle, $aResultTitle);
        if( $fRetValTitle !== false && $fRetValTitle > 0)
        {   
            $c = explode(":",$aResultTitle['coords_to']);
            $retVal->strCoordsTo = $aResultTitle['coords_to'];

            $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
            $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
            $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
            $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
            $retVal->aCoordsTo = $aCoords;
            if ($aResultTitle['status'] == 'Eigener Planet wurde sondiert')
                $retVal->bSuccess = true;
            else
                $retVal->bSuccess = false;
        }

        if (!empty($aResultText['ally'])) {
            
            $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally']);
            $c = explode(":",$aResultText['coords_ally']);
            $retVal->strCoordsFrom = $aResultText['coords_ally'];

            $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
            $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
            $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
            $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
            $retVal->aCoordsFrom = $aCoords;
        }
        else {
            if (!empty($aResultText['coords1']) && $aResultText['coords1'] != $retVal->strCoordsTo) {
                $c = $aResultText['coords1'];
            }
            else if (!empty($aResultText['coords_name']) && $aResultText['coords_name'] != $retVal->strCoordsTo) {
                $c = $aResultText['coords_name'];
            }
            else if (!empty($aResultText['coords2']) && $aResultText['coords2'] != $retVal->strCoordsTo) {
                $c = $aResultText['coords2'];
            }
            else if (isset($aResultText['coords3']) && !empty($aResultText['coords3']) && $aResultText['coords3'] != $retVal->strCoordsTo) {
                $c = $aResultText['coords3'];
            }
            
            $retVal->strCoordsFrom = $c;
            $c = explode(":",$c);
            $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
            $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
            $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
            $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
            $retVal->aCoordsFrom = $aCoords;
        }       
    }
    else
    {
	  $retVal->bSuccessfullyParsed = false;
	  $retVal->aErrors[] = 'Unable to match the pattern (Sondierungen).';
	  $retVal->aErrors[] = '...'.$msg->strParserText;
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   */

  private function getRegularExpressionTitle()
  {
    $reCoords       = $this->getRegExpKoloCoords();
        
    $regExp = '/';
    $regExp .= '(?P<status>Sondierung\svereitelt|Eigener\sPlanet\swurde\ssondiert)\s+(?:\((?P<coords_to>'.$reCoords.')\))';
    $regExp .= '/s';
       
    return $regExp;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   */

  private function getRegularExpressionText()
  {

    $reUserName     = $this->getRegExpUserName();
    $reCoords       = $this->getRegExpKoloCoords();
    $reAlliance     = $this->getRegExpSingleLineText();
    $reText         = $this->getRegExpSingleLineText3();
        
    $regExp = '/';
    $regExp .= $reText.'(?:\((?P<coords1>'.$reCoords.')\)'.$reText.')?';
    $regExp .= '(?:';
    $regExp .= '((?P<name_ally>'.$reUserName.')\s+(?:\[(?P<ally>(?:'.$reAlliance.'))\])\s+(?:\((?P<coords_ally>'.$reCoords.')\)))';
    $regExp .= '|';
    $regExp .= '((?P<name>'.$reUserName.')\s+(?:\((?P<coords_name>'.$reCoords.')\)))';
    $regExp .= ')';
    $regExp .= '(('.$reText.')?(?:\((?P<coords2>'.$reCoords.')\)'.$reText.'))?';
    $regExp .= '(('.$reText.')?(?:\((?P<coords3>'.$reCoords.')\)'.$reText.'))?';
    $regExp .= '/s';
       
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