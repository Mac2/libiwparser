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
 * This parser is responsible for the global structure of the main page
 *
 * Its identifier: de_index
 */
class ParserIndexC extends ParserBaseC implements ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_index');
    $this->setName("Startseite");
    $this->setRegExpCanParseText('/Notizblock.*Umwandlung.*Serverzeit/smU');        //! Mac: requires Ungreedy U Modifier because charsize could be too large!
    $this->setRegExpBeginData('/Lade\sneue\sSpieler\sein\sund\sgewinne\seine\s.{1,3}berraschung\s*?/s' );
    $this->setRegExpEndData('/__\s+X/s' );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::parseText()
   */
  public function parseText( DTOParserResultC $parserResult )
  {
    $parserResult->objResultData = new DTOParserIndexResultC();
    $retVal =& $parserResult->objResultData;
    $fRetVal = 0;

    $this->stripTextToData();

    $regExp = $this->getRegularExpression();

    $aResult = array();
    $aResult = preg_split( $regExp, $this->getText(), -1, PREG_SPLIT_DELIM_CAPTURE );

    if( !empty($aResult) )
    {
      $parserResult->bSuccessfullyParsed = true;

      $parser = "";

      foreach( $aResult as $result )
      {
        if (empty($result))
        {
          continue;
        }

        $treffer = array();
        if (preg_match( $regExp, $result, $treffer ))
        {

          $parser = "";
          if (isset($treffer['FleetOwn']) && !empty($treffer['FleetOwn']))
          {
            $fleetType = 'own';
            $parser = 'Fleet';
          }
          if (isset($treffer['FleetOpposit']) && !empty($treffer['FleetOpposit']))
          {
            $fleetType = 'opposit';
            $parser = 'Fleet';
          }
          if (isset($treffer['Research']) && !empty($treffer['Research']))
          {
            $parser = 'Research';
          }
          if (isset($treffer['KoloInfos']) && !empty($treffer['KoloInfos']))
          {
            $parser = 'KoloInfos';
            $temp = $treffer['KoloInfos'];
          }
          if (isset($treffer['Geb']) && !empty($treffer['Geb']))
          {
            $parser = 'Geb';
          }
          if (isset($treffer['Schiff']) && !empty($treffer['Schiff']))
          {
            $parser = 'Schiff';
          }
          if (isset($treffer['Ressen']) && !empty($treffer['Ressen']))
          {
            $parser = 'Ressen';
          }
          if (isset($treffer['shoutbox']) && !empty($treffer['shoutbox']))
          {
            $parser = '';   //! erstmal skippen, da zuviele falsch positiven Ergebnisse
          }
          if (isset($treffer['MessagePostit']) && !empty($treffer['MessagePostit']))
          {
            if (isset($treffer['unreadMsg']) && !empty($treffer['unreadMsg']))
                $retVal->iUnreadMsg = $treffer['unreadMsg'];
            if (isset($treffer['unreadAMsg']) && !empty($treffer['unreadAMsg']))
                $retVal->iUnreadAllyMsg = $treffer['unreadAMsg'];
          }
          continue;
        }
        if (!empty($parser))
        {
          $msg = new DTOParserIndexResultIndexC;

          $msg->eParserType = $parser;

          if ($parser == 'Fleet')
          {
            $parser = new ParserIndexFleetC;
            $parser->setType( $fleetType );
          }
          else if ($parser == 'Research')
          {
            $retVal->bOngoingResearch = true;
            $parser = new ParserIndexResearchC;
          }
          else if ($parser == 'KoloInfos')
          {
            $parser = new ParserIndexKoloInfosC;
            $temp .= $result;
            $result = $temp;
          }
          else if ($parser == 'Geb')
          {
            $parser = new ParserIndexGebC;
          }
          else if ($parser == 'Schiff')
          {
            $parser = new ParserIndexSchiffC;
          }
          else if ($parser == 'Ressen')
          {
            $parser = new ParserIndexRessourcenC;
          }

          $msg->strParserText = $result;

          $return = new DTOParserResultC ($parser);
          $b = $parser->canParseMsg($msg);
          if (!$b) break;
          $parser->parseMsg ($return);
          $retVal->aContainer[] = $return;

          $parser = '';
          $fleetType = '';
          continue;
        }
      }
    }
    else
    {
      $parserResult->bSuccessfullyParsed = false;
      $parserResult->aErrors[] = 'Unable to match the pattern.';
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   */
  private function getRegularExpression()
  {
    /**
     * die Daten sind Blöcke, Wobei die Reihenfolge ungewiss ist
     */
    $rePlanetName       = $this->getRegExpSingleLineText();

    #Just even don't think to ask anything about this regexp, fu!
    $regExp  = '/
        \s*?(?:
            (?P<Ressen>\sRessourcen\s(?!abholen))|
            Globale\sNachricht\s+?Votings|
            Votings|
            (?P<KoloInfos>Kolonieinformation|^Kolonie\s'.$rePlanetName.'\s\(\d+\:\d+\:\d+\)\nLebensbedingungen)|
            (?P<Research>Forschungsstatus)|
            (?P<Noob>Noobstatus)|
            (?P<Geb>Geb.{1,3}udebau\s+?Ausbaustatus|
            Ausbaustatus)|
            (?P<Werften>^Werft-\s\/\sSchiffbau.{1,3}bersicht)|
            (?P<Schiff>^Schiffbau.{1,3}bersicht)|
            (?P<FleetOwn>(?:Eigene\sFlotten\s+?Eigene\sFlotten|Eigene\sFlotten)
                (?:\s+Ziel\s+Start\s+Ankunft\s+Aktionen\s+(?:(?:\*\s)?\+))?
            )|
            (?P<MessagePostit>Nachrichten\s+?
                (?:(?P<unreadMsg>\d+)\sneue\sNachrichten\s+?)?
                (?:(?P<unreadAMsg>\d+)\s+neue\sAllimsg(?:s)?\s+?)?
                (?:\(Durch\sSittermodus\snicht\sabrufbar\.\)\s+?)?
            )|
            (?P<FleetOpposit>(?:fremde\sFlotten\s+?Fremde\sFlotten|Fremde\sFlotten)
                (?:\s+\(Es\ssind\sfremde\sFlotten\s.{1,3}ber\sdem\sPlaneten\sstationiert\.\))?
                (?:\s+Ziel\s+Start\s+Ankunft\s+Aktionen\s+(?:(?:\*\s)?\+))?
            )|
            (?P<shoutbox>Allianz\sShoutbox\s*Inhalt.+neue\sMitteilung\s+Mitteilung)
            )\s*?
        ';
    $regExp .= '/mxs';

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
