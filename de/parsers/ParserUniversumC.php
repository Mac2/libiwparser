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
 * @author     Martin Martimeo <martin@martimeo.de>
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

namespace libIwParsers\de\parsers;

use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\ConfigC;
use libIwParsers\de\parserResults\DTOParserUniversumResultC;
use libIwParsers\de\parserResults\DTOParserUniversumPlanetResultC;
use libIwParsers\de\parserResults\DTOCoordinatesC;
use libIwParsers\de\parserResults\DTOParserUniversumXmlTextC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Sol Information
 *
 * This parser is responsible for parsing the information of a solsystem
 *
 * It uses two child parsers: ParserUniversumPlainTextC and ParserUniversumXmlC.
 *
 * Its identifier: de_universum
 */
class ParserUniversumC extends ParserBaseC implements ParserI
{
    const ID_PARSER_PLAIN_TEXT = 'plainText';
    const ID_PARSER_XML        = 'xml';

    private $_parserXml;
    private $_parserPlainText;
    private $_strParserToUse = '';

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->_parserPlainText = new ParserUniversumPlainTextC();
        $this->_parserXml       = new ParserUniversumXmlC();

        $this->setIdentifier('de_universum');
        $this->setName('Universum');
        $this->setRegExpCanParseText('');
        $this->setRegExpBeginData('');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getParserToUse()
    {
        $retVal = null;

        switch ($this->_strParserToUse) {
            case self::ID_PARSER_XML:
                $retVal = $this->_parserXml;
                break;
            case self::ID_PARSER_PLAIN_TEXT:
            default:
                $retVal = $this->_parserPlainText;
                break;
        }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::canParseText()
     *
     * This function checks if one parser (xml or plainText) can
     * parse the provided text.
     */
    public function canParseText($text)
    {
        $retVal = false;

        if ($this->_parserXml->canParseText($text)) {
            $retVal                = true;
            $this->_strParserToUse = self::ID_PARSER_XML;
        }

        //! XML sollte ueber SimpleXML und nicht per RegEx geparsed werden
//    elseif( $this->_parserPlainText->canParseText($text) )
//    {
//      $retVal = true;
//      $this->_strParserToUse = self::ID_PARSER_PLAIN_TEXT;
//    }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parser = $this->getParserToUse();

        return $parser->parseText($parserResult);
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Sol Information
 *
 * This parser is responsible for parsing the information of a solsystem
 *
 * Its identifier: de_universum
 *
 * This parser is the former ParserUniversumC. It currently parses the xml
 * data of the universe view, but based on regular expressions.
 * The new parser ParserUniversumXmlC will do the same job, but is based
 * on SimpleXML.
 */
class ParserUniversumPlainTextC extends ParserBaseC implements ParserI
{
    private $_stringCanParseText;

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_universum');
        $this->setName('Universum (Text)');
        //because the parser currently only can parse xml, we include the <?xml
        $this->setStringCanParseText('Das\sUniversum\s\-\sunendliche\sWeiten.+\<?xml', 's');
        $this->setRegExpBeginData('');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getStringCanParseText()
    {
        return $this->_stringCanParseText;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function setStringCanParseText($value, $modifier)
    {
        $value    = PropertyValueC::ensureString($value);
        $modifier = PropertyValueC::ensureString($modifier);

        $this->setRegExpCanParseText("/$value/$modifier");
        $this->_stringCanParseText = $value;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserUniversumResultC();
        $retVal =& $parserResult->objResultData;
        $this->stripTextToData();

        $regText = $this->getText();
        $regExp  = $this->getRegularExpression();
        $aResult = array();
        $fRetVal = preg_match_all($regExp, $regText, $aResult, PREG_SET_ORDER);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            $iCoordsGal = 0;
            $iCoordsSol = 0;
            foreach ($aResult as $result) {
                $planet         = new DTOParserUniversumPlanetResultC();
                $objCoordinates = new DTOCoordinatesC();

                //! Gal & Sol gelten jeweils fuer alle Planeten
                if (!empty($result['iCoordsGal'])) {
                    $iCoordsGal = PropertyValueC::ensureInteger($result['iCoordsGal']);
                }

                if (!empty($result['iCoordsSol'])) {
                    $iCoordsSol = PropertyValueC::ensureInteger($result['iCoordsSol']);
                }

                $iCoordsPla = PropertyValueC::ensureInteger($result['iCoordsPla']);


                $aCoords   = array(
                    'coords_gal' => $iCoordsGal,
                    'coords_sol' => $iCoordsSol,
                    'coords_pla' => $iCoordsPla
                );
                $strCoords = $iCoordsGal . ':' . $iCoordsSol . ':' . $iCoordsPla;

                $objCoordinates->iGalaxy = $iCoordsGal;
                $objCoordinates->iPlanet = $iCoordsPla;
                $objCoordinates->iSystem = $iCoordsSol;

                $planet->aCoords        = $aCoords;
                $planet->objCoordinates = $objCoordinates;
                $planet->strCoords      = $strCoords;

                $planet->strUserName     = PropertyValueC::ensureString($result['strUserName']);
                $planet->strUserAlliance = PropertyValueC::ensureString($result['strAlliance']);
                $planet->strPlanetName   = trim(PropertyValueC::ensureString($result['strPlanetName']));
                if ($planet->strPlanetName == "-" && empty($planet->strUserName)) {
                    $planet->strPlanetName = "";
                }

                $planet->strObjectType = PropertyValueC::ensureString($result['strObjectType']);
                if (empty($planet->strObjectType)) //! damit das ensureEnum korrekt funktioniert
                {
                    $planet->strObjectType = "---";
                }

                $planet->strPlanetType = PropertyValueC::ensureString($result['strPlanetType']);
                if ($iCoordsPla == 0 && empty($planet->strPlanetType)) //! damit das ensureEnum korrekt funktioniert
                {
                    $planet->strPlanetType = "Sonne";
                }

                //! Mac: Problem Opera liefert keine Informationen über den Planetentyp!
                $planet->eObjectType = PropertyValueC::ensureEnum($planet->strObjectType, "eObjectTypes");
                $planet->ePlanetType = PropertyValueC::ensureEnum($planet->strPlanetType, "ePlanetTypes");

//              if (isset($result['strNebel']) && !empty($result['strNebel'])) {
//                  $planet->strNebula = PropertyValueC::ensureString($result['strNebel']);
//                  $planet->bHasNebula = true;
//                  $planet->eNebula = PropertyValueC::ensureEnum( $result['strNebel'], 'ePlanetSpecials' );
//              }

//              if ($result['strObjectType'] == "Raumstation") {
//                  $retVal->aPlanets[$iCoordsGal.':'.$iCoordsSol.':0'] = new DTOParserUniversumPlanetResultC();
//                  $retVal->aPlanets[$iCoordsGal.':'.$iCoordsSol.':0']->strPlanetType = 'Raumstation';
//              } else {
                    $retVal->aPlanets[$strCoords] = $planet;
//              }
            }

        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the pattern.';
        }

    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         */
        $reCoordsGal = '\d+';
        $reCoordsSol = '\d+';

        $rePlanetType = $this->getRegExpPlanetTypes();
        $reObjectType = $this->getRegExpKoloTypes();
        $reName       = $this->getRegExpUserName();
        $reAlliance   = $this->getRegExpSingleLineText();
        $rePlanetName = $this->getRegExpSingleLineText();
        $reText       = $this->getRegExpSingleLineText();
        $reCoordsPla  = '\d+';
//      $rePlanetPoints     = '\d+';
//      $reNebel       = $this->getRegExpSingleLineText();
        $reRank = $this->getRegExpUserRank_de();

        $regExp = '/';
        //! Header
        $regExp .= '(?:';
        $regExp .= 'Galaxy\s(?P<iCoordsGal>' . $reCoordsGal . '),\sSonnensystem\s(?P<iCoordsSol>' . $reCoordsSol . ')';
        $regExp .= '\s*';
        $regExp .= $reAlliance;
        $regExp .= '\s*';
        $regExp .= '\s+Name\s+Allianztag\s+Planetenname\s+Aktionen\s*';
        $regExp .= ')?';
        //! Planetlines
        $regExp .= '(?:';
        $regExp .= '^\s*(?P<iCoordsPla>' . $reCoordsPla . ')'; // bei Opera gibts ein zus. Leerzeichen nach Zeilenanfang
        $regExp .= '\s*';
        $regExp .= '^(?P<strObjectType>' . $reObjectType . '|)';
        $regExp .= '\s*';
        $regExp .= '^(?P<strPlanetType>' . $rePlanetType . '|schwarzes\sLoch|Sonne|)';
        $regExp .= '\s*';
        $regExp .= '(?:^(?:' . $rePlanetType . '|schwarzes\sLoch|Sonne)\s*){0,3}';
        $regExp .= '^\s(?P<strUserName>' . $reName . '|)';
        $regExp .= '\s*';
        $regExp .= '(?:\[(?P<strAlliance>' . $reAlliance . ')\])?';
        $regExp .= '\s*';
        $regExp .= '(?:\((?P<strAllianceRank>' . $reRank . ')\))?';
        $regExp .= '\s*';
        $regExp .= '(?P<strPlanetName>' . $rePlanetName . '|-|)';
        $regExp .= '\s*';
        $regExp .= '(?:Flottenlink\sanlegen'; //! Header Link
        $regExp .= '|'; //! oder Versendelinks
        $regExp .= 'Flotte\sversenden';
        $regExp .= '(?:(?:\s+' . $reText . '){1,5}(?=\s+^\s*' . $reCoordsPla . '\s*))?'; //! User abh. Flottenlinks (max. 5 Stck)
        $regExp .= ')';
        $regExp .= '\s+';
        $regExp .= ')';

        $regExp .= '/mx';

        return $regExp;
    }

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parses a Sol Information (uses the xml data)
 *
 * This parser is responsible for parsing the (xml-)information of a solsystem.
 * The xml will be validated against the relaxNG Schema that can be found at
 * lib/xml/relaxng/universe.rng
 *
 * The parser can handle both complete page input (<Ctrl> + <a>, <Ctrl> + <c>)
 * and also xml-only input (for cases when s.o. clicks the xml-Data and then
 * copies it).
 *
 * Its identifier: de_universum
 */


class ParserUniversumXmlC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_universum');
        $this->setName('Universum (XML)');
        $this->setRegExpBeginData('/(?=\<\?xml)/s');
        $this->setRegExpEndData('/(?<=\<\/planeten_data\>)/');
    }

    /////////////////////////////////////////////////////////////////////////////

    protected function getRngFileUniverse()
    {
        return ConfigC::get('path.rng') . DIRECTORY_SEPARATOR . 'universe.rng';
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::canParseText()
     *
     * This function checks if the parser can handle the text that is provided.
     * To do so, it tries to load the text as xml and to validate it against
     * the universe schema.
     */
    public function canParseText($text)
    {
        $retVal          = false;
        $fRetVal         = false;
        $dom             = new \DOMDocument();
        $xmlString       = '';
        $rngFileUniverse = $this->getRngFileUniverse();

        //dont modify the text that is provided!
        $textCopy = $text;

        $this->setText($textCopy);
        $this->stripTextToData();

        $xmlStrings = $this->getText(); //! kann auch mehrere Berichte enthalten, deswegen nochmal aufsplitten

        $reStartData = $this->getRegExpStartData();

        if ($reStartData !== '') {
            $aStart = preg_split($reStartData, $xmlStrings);

            for ($n = 1; $n < count($aStart); $n++) { //! bei Mehrfach-Berichten (Universum, Highscore, etc) alle verarbeiten
                if (!empty($aStart[$n])) {
                    //supress errors, otherwise this parser may crash the
                    //application if it is provided non-xml data!
                    $fRetVal = @$dom->loadXml($aStart[$n], LIBXML_NOERROR);

                    if ($fRetVal === true) {
                        $retVal = $dom->relaxNGValidate($rngFileUniverse);
                        if ($retVal === true) {
                            return $retVal;
                        }
                    }
                }
            }
        }

        return $retVal;
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserUniversumXmlTextC();
        $retVal                      =& $parserResult->objResultData;
        $this->stripTextToData();

        $xmlStrings = array();

        $reStartData = $this->getRegExpStartData();

        if ($reStartData !== '') {
            $aStart = preg_split($reStartData, $this->getText());

            for ($n = 1; $n < count($aStart); $n++) { //! bei Mehrfach-Berichten (Universum, Highscore, etc) alle verarbeiten
                if (!empty($aStart[$n])) {
                    $xmlStrings[] = $aStart[$n];
                }
            }
        }

        $retVal->aXmlText = $xmlStrings; //give the text to unixml-parser -> nothing more here

        $parserResult->bSuccessfullyParsed = true;
    }

}