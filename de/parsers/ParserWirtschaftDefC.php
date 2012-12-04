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
 * @author     Mac <MacXY@herr-der-mails.de>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

namespace libIwParsers\de\parsers;
use libIwParsers\PropertyValueC;
use libIwParsers\DTOParserResultC;
use libIwParsers\ParserBaseC;
use libIwParsers\ParserI;
use libIwParsers\de\parserResults\DTOParserWirtschaftDefResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftDefKoloResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftDefSlotResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftDefDefenceResultC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the defence overview
 *
 * This parser is responsible for parsing the defence overview at economy
 *
 * Its identifier: de_wirtschaft_def
 */
class ParserWirtschaftDefC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_wirtschaft_def');
        $this->setName('Verteidigungs&uuml;bersicht');
        $this->setRegExpCanParseText('/Geb.{1,3}ude.{1,3}bersicht\s+Forschungs.{1,3}bersicht\s+Werft.{1,3}bersicht\s+Defence.{1,3}bersicht.*Verteidigungs.{1,3}bersicht(?:.*Verteidigungs.{1,3}bersicht)?/sm');
        $this->setRegExpBeginData('/Defence.{1,3}bersicht.*Verteidigungs.{1,3}bersicht/sm');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserWirtschaftDefResultC();
        $retVal = & $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();
        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);
        $aKolos = array();

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {

                $strKoloLine = $result['kolo_line'];
                $strSlotLine = $result['slot_line'];
                $strDataLines = $result['data_lines'];

                if (empty($aKolos)) {
                    $regExpKolo = $this->getRegularExpressionKolo();

                    $aResultKolo = array();
                    $fRetValKolo = preg_match_all($regExpKolo, $strKoloLine, $aResultKolo, PREG_SET_ORDER);

                    foreach ($aResultKolo as $resultKolo) {
                        $strKoloType = $resultKolo['kolo_type'];
                        $strCoords = $resultKolo['coords'];
                        $iCoordsGal = PropertyValueC::ensureInteger($resultKolo['coords_gal']);
                        $iCoordsSol = PropertyValueC::ensureInteger($resultKolo['coords_sol']);
                        $iCoordsPla = PropertyValueC::ensureInteger($resultKolo['coords_pla']);
                        $aCoords = array('coords_gal' => $iCoordsGal, 'coords_sol' => $iCoordsSol, 'coords_pla' => $iCoordsPla);

                        $retVal->aKolos[$strCoords] = new DTOParserWirtschaftDefKoloResultC;
                        $retVal->aKolos[$strCoords]->aCoords = $aCoords;
                        $retVal->aKolos[$strCoords]->strCoords = PropertyValueC::ensureString($strCoords);
                        $retVal->aKolos[$strCoords]->strObjectType = PropertyValueC::ensureString($strKoloType);

                        $aKolos[] = $strCoords;
                    }
                }

                $aSlotLines = explode("\n", $strSlotLine);
                foreach ($aSlotLines as $strSlotLine) {
                    $aDataLine = explode("\t", $strSlotLine);

                    $strDefenceType = array_shift($aDataLine);
                    if (strpos($strDefenceType, "orb") !== false) {
                        $strDefenceType = "orbital";
                    } else if (strpos($strDefenceType, "pla") !== false) {
                        $strDefenceType = "planetar";
                    }

                    $slot = new DTOParserWirtschaftDefSlotResultC;
                    $slot->strSlotType = PropertyValueC::ensureString($strDefenceType);

                    if (empty($strDefenceType)) {
                        continue;
                    }
                    foreach ($aDataLine as $i => $strData) {
                        $values = explode("/", $strData);
                        $slot->aAvailable[$aKolos[$i]] = PropertyValueC::ensureInteger($values[0]);
                        $slot->aUsed[$aKolos[$i]] = PropertyValueC::ensureInteger($values[1]);
                        $slot->aTotal[$aKolos[$i]] = PropertyValueC::ensureInteger($values[2]);
                    }
                    $retVal->aSlots[] = $slot;
                }

                $aDataLines = explode("\n", $strDataLines);
                foreach ($aDataLines as $strDataLine) {
                    $aDataLine = explode("\t", $strDataLine);

                    $strDefenceName = array_shift($aDataLine);
                    $defence = new DTOParserWirtschaftDefDefenceResultC;
                    $defence->strDefenceName = PropertyValueC::ensureString($strDefenceName);

                    if (empty($strDefenceName)) {
                        continue;
                    }
                    foreach ($aDataLine as $i => $strData) {
                        $values = explode("/", $strData);
                        $defence->aCounts[$aKolos[$i]] = PropertyValueC::ensureInteger($values[0]);
                        $defence->aMaxCounts[$aKolos[$i]] = PropertyValueC::ensureInteger($values[1]);
                    }

                    $retVal->aDefences[] = $defence;
                }
            }
        } else {
            $parserResult->bSuccessfullyParsed = false;
            $parserResult->aErrors[] = 'Unable to match the pattern.';
        }
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpression()
    {
        /**
         */

        $reKoloTypes = $this->getRegExpKoloTypes();
        $reKoloCoords = $this->getRegExpKoloCoords();
        $reNumber = $this->getRegExpDecimalNumber();

        $regExp = '&';
        $regExp .= '(?P<kolo_line>';
        $regExp .= $reKoloCoords;
        $regExp .= '   (?:';
        $regExp .= '     [\\n\\r]+';
        $regExp .= '     \(' . $reKoloTypes . '\)';
        $regExp .= '     \\t';
        $regExp .= '     ' . $reKoloCoords . '';
        $regExp .= '   )*';
        $regExp .= '   [\\n\\r]+';
        $regExp .= '   \(' . $reKoloTypes . '\)';
        $regExp .= ')';
        $regExp .= '\s+';
        $regExp .= '^Verteidigungsslots\s';
        $regExp .= '(?P<slot_line>';
        $regExp .= '   (?:(?:pla|orb)\sfrei/belegt/gesamt(?:\s+' . $reNumber . "\s/\s" . $reNumber . "\s/\s" . $reNumber . ')*\s+';
        $regExp .= '   )+';
        $regExp .= ')';
        $regExp .= '^Verteidigungsanlagen\s\(Anzahl/Baubar\)';
        $regExp .= '(?P<data_lines>(?:';
        $regExp .= '      [\\n\\r]+'; //! Zeilenumbruch
        $regExp .= '      [^\\t\\n]+'; //! ein Wort
        $regExp .= '      (?:\t\d*\s/\s\d*)+'; //! Bundle von (Nr / Nr)
        $regExp .= '      )+';
        $regExp .= ')\s+';
        $regExp .= '&mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKolo()
    {
        /**
         */

        $reKoloTypes = $this->getRegExpKoloTypes();

        $regExpKolo = '/
                          (?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))
                          [\n\r]+
                          \((?P<kolo_type>' . $reKoloTypes . ')\)
                       /mx';

        return $regExpKolo;
    }

}