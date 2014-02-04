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
              'DTOParserXmlResultC.php' );

/**
 * Parser for XML Links
 *
 * This parser is responsible for collecting and forwarding xml-links, because parsing at this point is not useful
 * This can be done easily by using e.g. simple_xml_load($strUrl) afterwards
 *
 * Its identifier: de_xml
 */
class ParserXmlC extends ParserBaseC implements ParserI
{
  /////////////////////////////////////////////////////////////////////////////

  public function __construct()
  {
    parent::__construct();

    $this->setIdentifier('de_xml');
    $this->setName('XML Link');
    $this->setRegExpCanParseText($this->getRegularExpression());
    $this->setRegExpBeginData('');
    $this->setRegExpEndData('');
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  public function parseText( DTOParserResultC $parserResult )
  {
        $parserResult->objResultData = new DTOParserXmlResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        if ( $fRetVal !== FALSE && $fRetVal > 0)
        {
            foreach ($aResult as $xmlinfo)
            {
                $link = new DTOParserXmlResultLinkC();
                if (isset($xmlinfo['unilink']) && !empty($xmlinfo['unilink'])) {
                    $link->strUrl = $xmlinfo['unilink'];
                    $link->strType = "universe";
                    $retVal->aUniversumLinks[] = $link;
                }
                else {
                    $link->iId = $xmlinfo['id'];
                    $link->strHash = $xmlinfo['hash'];
                    $link->strUrl = $xmlinfo[0] . '&typ=xml';
                    $link->strType = $xmlinfo['type'];

                    if ($link->strType == 'kb') {
                        $retVal->aKbLinks[] = $link;
                    }
                    else if ($link->strType == 'sb') {
                        $retVal->aSbLinks[] = $link;
                    }
                }
            }
            $parserResult->bSuccessfullyParsed = true;
        }
        else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[] = "unable to match Pattern of xml-link";
        }
        
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private function getRegularExpression()
  {
      $regExp  = '%';
      $regExp .= '(?:(?<=\s)|(?<=^))';  //shall start in a whitesace or start of line
      $regExp .= '(?:https?:\/\/www.?\.icewars\.de\/portal\/kb\/de\/(?P<type>kb|sb)\.php\?id=(?P<id>\d+)\&md_hash=(?P<hash>[\w\d]+)(?:&server_id=)?';
      $regExp .= '|(?P<unilink>https?:\/\/www.?\.icewars\.de/xml/user_univ_scan/\w+\.xml))';
      $regExp .= '(?=\s|$)';            //shall end in a whitespace or end of line     
      $regExp .= '%';
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
  
}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////