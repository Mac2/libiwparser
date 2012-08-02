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
 * Parses a Forschung Information
 *
 * This parser is responsible for parsing the information of a research
 *
 * Its identifier: de_info_forschung
 */
class ParserInfoForschungC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_info_forschung');
    $this->setName("Forschungsinfo");
    $this->setRegExpCanParseText('/Forschungsinfo\s*Forschungsinfo|Forschungsinfo:.+Status.+Farbenlegende:/sm');
    $this->setRegExpBeginData( '' );
    $this->setRegExpEndData( '' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserInfoForschungResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();
    $regExpRess = $this->getRegularExpressionRess();

    $aResult = array();
    $fRetVal = preg_match( $regExp, $this->getText(), $aResult );
//     print_r($aResult);
//     die();
    if( $fRetVal !== false && $fRetVal > 0 )
    {
        $parserResult->bSuccessfullyParsed = true;
        
        $retVal->aResearchsNeeded = HelperC::convertBracketStringToArray($aResult['strResearchsNeeded']);
        $retVal->aBuildingsNeeded = HelperC::convertBracketStringToArray($aResult['strBuildingsNeeded']);
        $retVal->aObjectsNeeded = HelperC::convertBracketStringToArray($aResult['strObjectsNeeded']);

        if (isset($aResult['strResearchsDevelop']))
            $retVal->aResearchsDevelop = HelperC::convertBracketStringToArray($aResult['strResearchsDevelop']);
        if (isset($aResult['strBuildingsDevelop']))
            $retVal->aBuildingsDevelop = HelperC::convertBracketStringToArray($aResult['strBuildingsDevelop']);
        if (isset($aResult['strBuildingLevelsDevelop']))
            $retVal->aBuildingLevelsDevelop = HelperC::convertBracketStringToArray($aResult['strBuildingLevelsDevelop']);
        if (isset($aResult['strDefencesDevelop']))
            $retVal->aDefencesDevelop = HelperC::convertBracketStringToArray($aResult['strDefencesDevelop']);
        if (isset($aResult['strGeneticsDevelop']))
            $retVal->aGeneticsDevelop = HelperC::convertBracketStringToArray($aResult['strGeneticsDevelop']);
        
        $retVal->strResearchName = PropertyValueC::ensureString( $aResult['strResearchName'] );
        $retVal->strAreaName = PropertyValueC::ensureString( $aResult['strAreaName'] );
        $retVal->strStatus = PropertyValueC::ensureString( $aResult['strStatus'] );
        
        if (isset($aResult['first']) && !empty($aResult['first'])) {
            $retVal->strResearchComment = PropertyValueC::ensureString( $aResult['comment'] );
            $retVal->strResearchFirst = PropertyValueC::ensureString( $aResult['first'] );
        }
        else
            $retVal->strResearchComment = PropertyValueC::ensureString( $aResult['commentonly'] );
        
        $retVal->iFP = PropertyValueC::ensureInteger( $aResult['fp'] );
        // $retVal->iHS = PropertyValueC::ensureInteger( $aResult['iPoints'] );
        $retVal->iPeopleResearched = PropertyValueC::ensureInteger( $aResult['count'] );

        if (empty($aResult['prozent'])) $aResult['prozent'] = 100;
        if (empty($aResult['malus'])) $aResult['malus'] = 100;
        $aResult['faktor'] = (float) $aResult['prozent'] * $aResult['malus'] / 100;

        $retVal->iResearchCosts = PropertyValueC::ensureInteger( $aResult['faktor'] );
        
        if (isset($aResult['strPrototypName']) && !empty($aResult['strPrototypName']))
        {
            $retVal->bIsPrototypResearch = true;
            $retVal->strPrototypName = PropertyValueC::ensureString( $aResult['strPrototypName'] );
        }
        
        $treffer = array();
        preg_match_all ($regExpRess, $aResult['kosten'], $treffer, PREG_SET_ORDER );
        foreach ($treffer as $teff)
        {
            $retVal->aCosts[] = array('strResourceName' => PropertyValueC::ensureResource( $teff['resource_name'] ), 'iResourceCount' => PropertyValueC::ensureInteger($teff['resource_count']));
        }
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }
    
  }

  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpressionRess()
  {
    /**
    */

    $reResource                = $this->getRegExpResource();

    $regExpRess  = '/';
    $regExpRess  .= '(?P<resource_name>'.$reResource.')\:\s(?P<resource_count>'.$this->getRegExpDecimalNumber().')';
    $regExpRess  .= '/mx';
    
    return $regExpRess;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  private function getRegularExpression()
  {
    /**
    */

//     $reResearch                = '[a-zA-Z][^\n\t\r\:\+\%\.]{3,}';
    $reResearch         = $this->getRegExpSingleLineText3();        //! accepted also numbers in ResearchName
    $reSchiffeName      = $this->getRegExpSingleLineText3();
    $reBracketString    = $this->getRegExpBracketString();
    $reAreas            = $this->getRegExpAreas();
    $reFP               = $this->getRegExpDecimalNumber();
    $reCosts            = $this->getRegExpDecimalNumber();
    $reResource         = $this->getRegExpResource();

    
    $regExp  = '/';
    $regExp  .= 'Forschungsinfo\:\s';
    $regExp  .= '(?P<strResearchName>'.$reResearch.')\s*?';
    $regExp  .= '[\n\r]+';
    $regExp  .= 'Status\s+?';
    $regExp  .= '(?P<strStatus>'.'(?:erforscht|erforschbar|nicht\serforschbar|erforschbar,\saber\sGeb.{1,3}ude\sfehlt)'.')\s*?';
    $regExp  .= '[\n\r]+';
    $regExp  .= 'Gebiet\s+?';
    $regExp  .= '(?P<strAreaName>'.$reAreas.')\s*?';
    $regExp  .= '[\n\r]+';
    $regExp  .= '(?:';
    //! schon erforschte Variante
    $regExp  .= '   (?:(?P<comment>[\s\S]*?)';
    $regExp  .= '   [\n\r]+|)';
    $regExp  .= '   (?:Zuerst\serforscht\svon\s*(?P<first>[^\n\r]+?';
    $regExp  .= '   [\n\r]+))';
    $regExp  .= '|';
    //! noch nicht erforschte Variante
    $regExp  .= '   (?:(?P<commentonly>[\s\S]*?)';
    $regExp  .= '   [\n\r]+|)';
    $regExp  .= ')';
    
    $regExp  .= 'Kosten\s+?';
    $regExp .= '(?P<fp>'        . $reFP       . ')\sForschungspunkte';
    $regExp .= '(?P<kosten>(?:\s'.$reResource.'\:\s'.$reCosts.')*)';
    $regExp  .= '[\n\r\s\t]+';

    $regExp  .= '(\s+?';
    $regExp  .= '(?P<strMiscCosts>'.'Die\srealen\sForschungskosten\ssind\svon\sweiteren\sParametern\sabh.{1,3}ngig\.)';
    $regExp  .= '\s+?)?';

    $regExp .= '(?:\s*?\(von\s(?P<count>\d+)(?:\%|\\\%)\sLeuten\serforscht,\s(?P<prozent>\d+)(?:\%|\\\%)\sFPKosten\)';
    $regExp  .= '[\n\r]+';
    $regExp  .= '|)';

    $regExp .= '(?:[\s\t\n\r]+?Aufgrund\svon\sgenerellen\stechnischen\sUnverst.{1,3}ndnis\sim\sUniversum,\sliegen\sdie\sForschungskosten\sbei\s(?P<malus>\d+)\s(?:\%|\\\%)\.';
    $regExp  .= '[\n\r]+';
    $regExp  .= '|)';
  
    // $regExp  .= 'Highscorepunkte\s+?';
    // $regExp  .= '(?P<iPoints>'.$rePoints.')\s*?';
    // $regExp  .= '[\n\r]+';
    $regExp  .= '(?:';
    $regExp  .= '\s*Prototyp\s+?';
    $regExp  .= 'Die\sForschung\sbringt\seinen\sPrototyp\svon\s(?P<strPrototypName>'        . $reSchiffeName       . ')\s*?';
    $regExp  .= '[\n\r]+';
    $regExp  .= '|)';

    $regExp  .= '\s*Voraussetzungen\sForschungen\s+?';
    $regExp  .= '(?P<strResearchsNeeded>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= 'Voraussetzungen\sGeb.{1,3}ude\s+?';
    $regExp  .= '(?P<strBuildingsNeeded>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= 'Voraussetzungen\sObjekte\s+?';
    $regExp  .= '(?P<strObjectsNeeded>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= '(?:\s+?';
    $regExp  .= '(?P<strMiscNeeded>'.'Es\ssind\sweitere\sVoraussetzungen\szu\serf.{1,3}llen,\sum\sdiese\sForschung\serforschen\szu\skönnen\.'.'){0,1}';
    $regExp  .= '\s+?)?';

    $regExp  .= 'Erm.{1,3}glicht\sForschungen\s+?';
    $regExp  .= '(?P<strResearchsDevelop>('.$reBracketString.'){0,1})';
    $regExp  .= '\s+?';
    $regExp  .= 'Erm.{1,3}glicht\sGeb.{1,3}ude\s+?';
    $regExp  .= '(?P<strBuildingsDevelop>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= 'Erm.{1,3}glicht\sGeb.{1,3}udestufen\s+?';
    $regExp  .= '(?P<strBuildingLevelsDevelop>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= 'Erm.{1,3}glicht\sVerteidigungsanlagen\s+?';
    $regExp  .= '(?P<strDefencesDevelop>'.$reBracketString.'){0,1}';
    $regExp  .= '\s+?';
    $regExp  .= 'Erm.{1,3}glicht\sGenetikoptionen\s+?';
    $regExp  .= '(?P<strGeneticsDevelop>'.$reBracketString.'){0,1}';

    $regExp  .= '/mx';
    
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

