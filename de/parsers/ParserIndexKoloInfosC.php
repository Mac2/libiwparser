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
 * Parser for Mainpage
 *
 * This parser is responsible for the Kolonieinfo section on the Mainpage
 *
 * Its identifier: de_index_koloinfos
 */
class ParserIndexKoloInfosC extends ParserMsgBaseC implements ParserMsgI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_index_koloinfos');
    $this->setCanParseMsg('KoloInfos');
  }

 /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserMsgI::parseMsg()
   */
  public function parseMsg( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserIndexKoloInfosResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;
    
    $regExp = $this->getRegularExpression();
    $msg = $this->getMsg();
    
    $parserResult->strIdentifier = 'de_index_koloinfos';
    $aResult = array();
    $fRetVal = preg_match_all( $regExp, $msg->strParserText, $aResult, PREG_SET_ORDER );

    if( $fRetVal !== false && $fRetVal > 0 )
    {
        $parserResult->bSuccessfullyParsed = true;
      
        $retObj = new DTOParserIndexKoloInfosResultKoloInfoC();
        $deff_type="";

        foreach( $aResult as $result )
        {
            
            if (isset($result['strPlanetName']) && !empty($result['strPlanetName']))
            {
                $retObj->strPlanetName = PropertyValueC::ensureString($result['strPlanetName']);
                $iCoordsPla = PropertyValueC::ensureInteger($result['iCoordsPla']);
                $iCoordsGal = PropertyValueC::ensureInteger($result['iCoordsGal']);
                $iCoordsSol = PropertyValueC::ensureInteger($result['iCoordsSol']);
                $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);
                $strCoords = $iCoordsGal.':'.$iCoordsSol.':'.$iCoordsPla;

                $retObj->aCoords = $aCoords;
                $retObj->strCoords = $strCoords;

                if (isset($result['strKoloTyp']))
                    $retObj->strObjectTyp = PropertyValueC::ensureString( $result['strKoloTyp'] );
                
                $lastScan = array();
                if (isset($result['dtLastScan']))
                    $lastScan['datetime'] = HelperC::convertDateTimeToTimestamp( $result['dtLastScan'] );

                if (isset($result['strScanUsername']))
                    $lastScan['username'] = PropertyValueC::ensureString( $result['strScanUsername'] );

                $retObj->aLastScan = $lastScan;
                if (isset($result['mtScanRange']))
                    $retObj->aScanRange = HelperC::convertMixedTimeToTimestamp( $result['mtScanRange'] );
                if (isset($result['iLB']))
                    $retObj->iLB = PropertyValueC::ensureInteger($result['iLB']);

                if (isset($result['aktKolo'])) {
                    $obj=array('akt'=>PropertyValueC::ensureInteger($result['aktKolo']),'max'=>PropertyValueC::ensureInteger($result['maxKolo']));
                    $retObj->aKolo = $obj;
                }

                if (isset($result['aktKB']))
                {
                    $obj=array('akt'=>PropertyValueC::ensureInteger($result['aktKB']),'max'=>PropertyValueC::ensureInteger($result['maxKB']));
                    $retObj->aKB = $obj;
                }

                if (isset($result['aktAB']))
                {
                    $obj=array('akt'=>PropertyValueC::ensureInteger($result['aktAB']),'max'=>PropertyValueC::ensureInteger($result['maxAB']));
                    $retObj->aAB = $obj;
                }

                if (isset($result['aktSB']))
                {
                    $obj=array('akt'=>PropertyValueC::ensureInteger($result['aktSB']),'max'=>PropertyValueC::ensureInteger($result['maxSB']));
                    $retObj->aSB = $obj;
                }
            }
            else if (isset($result['strObjecte']) && !empty($result['strObjecte']))
            {
                if (isset($result['deff_type']) && strpos($result['deff_type'], "Schiffs") !== false) $deff_type="ship";
                else if (isset($result['deff_type']) && (strpos($result['deff_type'],"Verteidigungs") !== false || strpos($result['deff_type'],"Sondenverteidigungs") !== false) ) $deff_type="plan";
                $aoResult = array();
                $foRetVal = preg_match_all( $this->getRegularExpressionObject() , $result['strObjecte'], $aoResult, PREG_SET_ORDER );
                if ($foRetVal) foreach ($aoResult as $ores)
                {
                    $ores['iCount'] = PropertyValueC::ensureInteger($ores['iCount']);
                    $ores['strObject'] = PropertyValueC::ensureString($ores['strObject']);
                    if ($deff_type == "ship")
                                $retObj->aSchiffe[] = array('count' => $ores['iCount'], 'object' => trim($ores['strObject']));				
                    else if ($deff_type == "plan")
                                $retObj->aPlanDeff[] = array('count' => $ores['iCount'], 'object' => trim($ores['strObject']));				
                }
//              $retVal->aFleets[] = $retObj;
            }
	    else if (isset($result['problems']) && !empty($result['problems'])) {
		$retObj->strProblems = $result['problems'];
	    }
        }
        $retVal->aKolos[] = $retObj;
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
      $parserResult->aErrors[] = $msg->strParserText;
    }

  }

  /////////////////////////////////////////////////////////////////////////////
  
  private function getRegularExpressionObject()
  {
    $reObject             = $this->getRegExpSingleLineText3();
    $reCount             = $this->getRegExpDecimalNumber();
    
    $regExp  = '/
                (?P<strObject>'.$reObject.')
                \s+?
                (?P<iCount>'.$reCount.')
                ';
    $regExp .= '/mxs';
    
    return $regExp;
  }

  /**
   */
  private function getRegularExpression()
  {
    $rePlanetName       = $this->getRegExpSingleLineText();
    $reDateTime         = $this->getRegExpDateTime();
    $reMixedTime        = $this->getRegExpMixedTime();
    $reObject           = $this->getRegExpSingleLineText3();
    $reCount            = $this->getRegExpDecimalNumber();
    $reUserName         = $this->getRegExpUserName();
    $reKoloType         = $this->getRegExpKoloTypes();
    $reAreas            = $this->getRegExpAreas();
    $reProblem          = "(Bev.{1,3}lkerungsmangel|Scannerabschaltung\swegen\sChemiemangel|Werften\ssind\sruntergefallen\s\*n.{1,3}l\*|Energiemangel|Forschungsausfall\sdurch\sEnergiemangel|Wassermangel)";

    $regExp  = '/ ';
    
    $regExp  .= '((?P<strKoloTyp>'.$reKoloType.')';
    $regExp  .= '\s';
    $regExp  .= '(?P<strPlanetName>'.$rePlanetName.')';
    $regExp  .= '\s';
    $regExp  .= '\((?P<iCoordsGal>\d+)\:(?P<iCoordsSol>\d+)\:(?P<iCoordsPla>\d+)\)';
    $regExp  .= '(:?';
    $regExp  .= '\nLebensbedingungen[\s\t]+';
    $regExp  .= '(?P<iLB>'.'\d+'.')';
    $regExp  .= '\s(?:\%|\\\%|\\\\\%)';
    $regExp  .= '\nFlottenscannerreichweite\s+\(normal\)\s+';
    $regExp  .= '(?P<mtScanRange>'.$reMixedTime.')';
    $regExp  .= '\n';
    $regExp  .= '(?:Letzter\s+erfolgreicher\s+Feindscan\s+am\s+';
    $regExp  .= '(?P<dtLastScan>'.$reDateTime.')';
    $regExp  .= '\s+von\s+';
    $regExp  .= '(?P<strScanUsername>'.$reUserName.')\s*|\s*)';
    $regExp  .= '\s+Kolonien\s+aktuell\s+\/\s+maximal';
    $regExp  .= '\s';
    $regExp  .= '(?P<aktKolo>'.$reCount.')';
    $regExp  .= '\s\/\s';
    $regExp  .= '(?P<maxKolo>'.$reCount.')';
    $regExp  .= '(?:\s+aufgebaute\s+Kampfbasen\s+aktuell\s+\/\s+maximal';
    $regExp  .= '\s+';
    $regExp  .= '(?P<aktKB>'.$reCount.')';
    $regExp  .= '\s+\/\s+';
    $regExp  .= '(?P<maxKB>'.$reCount.')|)';
    $regExp  .= '(?:\s+aufgebaute\sRessbasen\saktuell\s\/\smaximal';
    $regExp  .= '\s+';
    $regExp  .= '(?P<aktSB>'.$reCount.')';
    $regExp  .= '\s+\/\s+';
    $regExp  .= '(?P<maxSB>'.$reCount.')|)';
    $regExp  .= '(?:\s+aktuelle\s\/\smaximale\saufgebaute\sArtefaktbasen';
    $regExp  .= '\s+';
    $regExp  .= '(?P<aktAB>'.$reCount.')';
    $regExp  .= '\s+\/\s+';
    $regExp  .= '(?P<maxAB>'.$reCount.')|)';
    $regExp  .= ')?';
//     $regExp  .= '(\s*.*\s){1,1}';
    $regExp  .= ')|';
    $regExp  .= '((?P<deff_type>(Schiffs.{1,3}bersicht|Verteidigungs.{1,3}bersicht|Sondenverteidigungs.{1,3}bersicht)\s)';
    $regExp  .= '((?P<strSchiffeArea>'.$reAreas.')\s)?';
    $regExp  .= '(?P<strObjecte>(?:
             \s*?'.$reObject.'\s*'.$reCount.'\s*?
             )+)';
    $regExp  .= ')|';
    $regExp  .= '((Dauer\sder\sRunde\s+'.$reObject.'\s+)';
    $regExp  .= '(Noobstatus\sBis\:\s(?P<noobstatus>('.$reDateTime.'))\s\(\d{1,2}\sTage\s\d{2}\:\d{2}\:\d{2}\)\s*)?';
    $regExp  .= '(Probleme\s+(?P<problems>(?:
             ^'.$reProblem.'\s+
             )+)';
    $regExp  .= ')?';
    $regExp  .= ')';
    $regExp .= '/mx';
    
    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * For debugging with "The Regex Coach" which doesn't support named groups
   */
  private function getRegularExpressionWithoutNamedGroups()
  {
    $retVal = $this->getRegularExpression();
    
    $retVal = preg_replace( '/\?P<\w+>/', '', $retVal );
    
    return $retVal;
  }
  
  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
