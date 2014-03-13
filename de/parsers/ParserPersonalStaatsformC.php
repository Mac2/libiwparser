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

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the staatsform selected by a user
 *
 * This parser is responsible for parsing the staatsform of a person
 *
 * Its identifier: de_personal_staatsform
 */
class ParserPersonalStaatsformC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_personal_staatsform');
        $this->setName('Staatsform');
        $this->setRegExpCanParseText('/Wahl\sder\sStaatsform/s');
        $this->setRegExpBeginData($this->getRegExpCanParseText());
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserPersonalStaatsformResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        /**
         * die Daten 2 Zeilen:
         * aktuelle Staatsform  Monarchie|Demokratie|...
         * aktuelle Vor und Nachteile ...
         */

        $reStaatsform = $this->getRegExpStaatsform_de();
        $reVorteile   = $this->getRegExpText();

        $regExp = '/^';
        $regExp .= 'aktuelle\sStaatsform\s+';
        $regExp .= '(?P<staatsform_name>' . $reStaatsform . ')\s*';
        $regExp .= '\n+';
        $regExp .= 'Aktuelle\sVor-\sund\sNachteile\s+?';
        $regExp .= '(?P<advantage>' . $reVorteile . ')\s*';
        $regExp .= '$/m';

        $aResult = array();
        $fRetVal = preg_match($regExp, $this->getText(), $aResult);

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            $retVal->strStaatsform = PropertyValueC::ensureString($aResult[1]);
            $retVal->strVorteile   = PropertyValueC::ensureString($aResult[2]);
            if (stripos($retVal->strStaatsform, "barbar") === false) {
                $retVal->bStaatsformChosen = true;
            } else {
                $retVal->strStaatsform = "Barbarei";
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[]           = 'Unable to match the de_personal_staatsform pattern.';
        }

    }

}