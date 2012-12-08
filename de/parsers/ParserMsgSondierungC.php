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

    $regExpTextHardcoded = $this->getRegularExpressionTextHardcoded();
    $msg = $this->getMsg();

    foreach($msg as $key => $value) {
        $retVal->$key = $value;
    }

    //try hardcoded parser
    $aResultText = array();
    $fRetValText = preg_match($regExpTextHardcoded, $msg->strParserText, $aResultText);

    if( $fRetValText !== false && $fRetValText > 0)
    {

        $retVal->bSuccessfullyParsed = true;
        $retVal->bHardcodedParserUsed = true;

        $retVal->strCoordsTo = $aResultText['coords_to'];
        $c = explode(":",$aResultText['coords_to']);
        $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
        $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
        $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
        $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
        $retVal->aCoordsTo = $aCoords;

        $c = explode(":",$aResultText['coords_from']);
        $retVal->strCoordsFrom = $aResultText['coords_from'];
        $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
        $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
        $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
        $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
        $retVal->aCoordsFrom = $aCoords;

        $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally_from']);
        $retVal->strNameFrom = PropertyValueC::ensureString($aResultText['name_from']);
        
        if (!empty($aResultText['success'])) {
            $retVal->bSuccess = true;
        } else {
            $retVal->bSuccess = false;
        }
        
        if (!empty($aResultText['tauben'])) {    
            $retVal->iTauben = PropertyValueC::ensureInteger( $aResultText['tauben'] );
        }
        
    } else {
        //try a more flexible parser

        $regExpText= $this->getRegularExpressionText();
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

            if (!empty($aResultText['ally1'])) {
                $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally1']);
                $c = explode(":",$aResultText['coords1']);
                $retVal->strCoordsFrom = $aResultText['coords1'];

                $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
                $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
                $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsFrom = $aCoords;
            }
            else if (!empty($aResultText['ally2'])) {
                $retVal->strAllianceFrom = PropertyValueC::ensureString($aResultText['ally2']);
                $c = explode(":",$aResultText['coords2']);
                $retVal->strCoordsFrom = $aResultText['coords2'];

                $iCoordsGal = PropertyValueC::ensureInteger( $c[0] );
                $iCoordsSol = PropertyValueC::ensureInteger( $c[1] );
                $iCoordsPla = PropertyValueC::ensureInteger( $c[2] );
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $retVal->aCoordsFrom = $aCoords;
            }
            else {
                $c="";
                if (!empty($aResultText['coords1']) && $aResultText['coords1'] != $retVal->strCoordsTo) {
                    $c = $aResultText['coords1'];
                }
                else if (!empty($aResultText['coords2']) && $aResultText['coords2'] != $retVal->strCoordsTo) {
                    $c = $aResultText['coords2'];
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

  private function getRegularExpressionTextHardcoded()
  {

    $reUserName     = $this->getRegExpUserName();
    $reCoords       = $this->getRegExpKoloCoords();
    $reAlliance     = $this->getRegExpSingleLineText();
        
    $regExp = '/.*(?J)';
    //erfolgreiche
    $regExp .= '(?P<success>Eilmeldung: Heute wurde einer unserer Planeten \((?P<coords_to>'.$reCoords.')\) von (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? ausspioniert. Diese unerhörte Art der Aggression ging vom Planeten \((?P<coords_from>'.$reCoords.')\) aus.*)|';
    $regExp .= '(?P<success>Planet \((?P<coords_to>'.$reCoords.')\) wurde ausspioniert. Von (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\).*)|';
    $regExp .= '(?P<success>Der MEEP MEEEEP MEEPMEEEEP MEEEEP MEEEP (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) hat unseren wichtigen Planeten \((?P<coords_to>'.$reCoords.')\) ausspioniert..*)|';
    $regExp .= '(?P<success>Ok, der Planet \((?P<coords_to>'.$reCoords.')\) wurde ausspioniert. Und auch erfolgreich. Nämlich von (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\).*)|';
    $regExp .= '(?P<success>Der\/Die\/Das \(unzutreffendes bitte streichen\) (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) war gaaaaaanz gemein und hat dich ausspioniert \((?P<coords_to>'.$reCoords.')\).*)|';
    $regExp .= '(?P<success>Heute hat es der fiese (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) gewagt, unseren schönen Planeten \((?P<coords_to>'.$reCoords.')\) auszuspionieren.*)|';
    $regExp .= '(?P<success>Heute wurde unser schöner Planet \((?P<coords_to>'.$reCoords.')\) von dem bösen (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) ausspioniert! Die Sonden fielen in den Stadtpark und erschlugen (?P<tauben>\d+) Tauben.*)|';
    //nicht erfolgreiche
    $regExp .= '(?P<failed>Der\/Die\/Das \(unzutreffendes bitte streichen\) (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) war gaaaaaanz gemein und hat versucht dich auszuspähen \((?P<coords_to>'.$reCoords.')\).*)|';
    $regExp .= '(?P<failed>Der eindeutig unfähige (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) hat vergeblich versucht, einen unserer Planeten \((?P<coords_to>'.$reCoords.')\) auszuspionieren.*)|';
    $regExp .= '(?P<failed>Heute gab es einen wunderschönen feindlichen Sondenregen über \((?P<coords_to>'.$reCoords.')\). Die Trümmer der Sonden zauberten ein wundervolles Spektakel an den nächtlichen Himmel. (?P<name_from>'.$reUserName.')(?: \[(?P<ally_from>'.$reAlliance.')\])? \((?P<coords_from>'.$reCoords.')\) wird sich schwarz ärgern, das er keinerlei Informationen bekommen hat.)';    
    $regExp .= '.*/sU';
       
    return $regExp;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   */

  private function getRegularExpressionText()
  {

    $reCoords       = $this->getRegExpKoloCoords();
    $reAlliance     = $this->getRegExpSingleLineText();

    $regExp = '/';
    $regExp .= '(?P<name1>'.$reAlliance.')';
    $regExp .= '   (?:\s+\[(?P<ally1>'.$reAlliance.')\])?';
    $regExp .= '   \s+(?:\((?P<coords1>'.$reCoords.')\))';
    $regExp .= '\s*';
    $regExp .= '(?:[^\[\]]+)?';    
    $regExp .= '(?P<name2>'.$reAlliance.')';
    $regExp .= '   (?:\s*\[(?P<ally2>'.$reAlliance.')\])?';
    $regExp .= '   \s+(?:\((?P<coords2>'.$reCoords.')\))';
    $regExp .= '/sxU';
       
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