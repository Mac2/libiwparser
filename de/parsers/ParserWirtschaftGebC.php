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
use libIwParsers\de\parserResults\DTOParserWirtschaftGebAreaResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftGebResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftGebKoloResultC;
use libIwParsers\de\parserResults\DTOParserWirtschaftGebBuildingResultC;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Parser for the building overview
 *
 * This parser is responsible for parsing the building overview at economy
 *
 * Its identifier: de_wirtschaft_geb
 */
class ParserWirtschaftGebC extends ParserBaseC implements ParserI
{

    /////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->setIdentifier('de_wirtschaft_geb');
        $this->setName('Geb&auml;ude&uuml;bersicht');
        $this->setRegExpCanParseText('/Geb.{1,3}ude.{1,3}bersicht\s+Forschungs.{1,3}bersicht\s+Werft.{1,3}bersicht\s+Defence.{1,3}bersicht.*Geb.{1,3}ude.{1,3}bersicht(?:.*Geb.{1,3}ude.{1,3}bersicht)?/sm');
        $this->setRegExpBeginData('/Geb.+ude.+bersicht/sm');
        $this->setRegExpEndData('');
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * @see ParserI::parseText()
     */
    public function parseText(DTOParserResultC $parserResult)
    {
        $parserResult->objResultData = new DTOParserWirtschaftGebResultC();
        $retVal =& $parserResult->objResultData;

        $this->stripTextToData();

        $regExp = $this->getRegularExpression();

        $aResult = array();
        $fRetVal = preg_match_all($regExp, $this->getText(), $aResult, PREG_SET_ORDER);

        $aKolos = array();

        if ($fRetVal !== false && $fRetVal > 0) {
            $parserResult->bSuccessfullyParsed = true;

            foreach ($aResult as $result) {
                $strAreaName = $result['area_name'];

                $strKoloLine  = $result['kolo_line'];
                $strDataLines = $result['data_lines'];

                $area              = new DTOParserWirtschaftGebAreaResultC;
                $area->strAreaName = PropertyValueC::ensureString($strAreaName);

                if (empty($aKolos)) {
                    $regExpKolo = $this->getRegularExpressionKolo();

                    $aResultKolo = array();
                    $fRetValKolo = preg_match_all($regExpKolo, $strKoloLine, $aResultKolo, PREG_SET_ORDER);

                    foreach ($aResultKolo as $resultKolo) {
                        $strKoloType = $resultKolo['kolo_type'];
                        $strCoords   = $resultKolo['coords'];
                        $iCoordsGal  = PropertyValueC::ensureInteger($resultKolo['coords_gal']);
                        $iCoordsSol  = PropertyValueC::ensureInteger($resultKolo['coords_sol']);
                        $iCoordsPla  = PropertyValueC::ensureInteger($resultKolo['coords_pla']);
                        $aCoords     = array(
                            'coords_gal' => $iCoordsGal,
                            'coords_sol' => $iCoordsSol,
                            'coords_pla' => $iCoordsPla
                        );

                        $retVal->aKolos[$strCoords]                = new DTOParserWirtschaftGebKoloResultC;
                        $retVal->aKolos[$strCoords]->aCoords       = $aCoords;
                        $retVal->aKolos[$strCoords]->strCoords     = PropertyValueC::ensureString($strCoords);
                        $retVal->aKolos[$strCoords]->strObjectType = PropertyValueC::ensureString($strKoloType);

                        $aKolos[] = $strCoords;
                    }
                }

                $aDataLines = explode("\n", $strDataLines);
                foreach ($aDataLines as $strDataLine) {
                    $aDataLine = explode("\t", $strDataLine);

                    $strBuildingName           = array_shift($aDataLine);
                    $building                  = new DTOParserWirtschaftGebBuildingResultC;
                    $building->strBuildingName = PropertyValueC::ensureString($strBuildingName);

                    if (empty($strBuildingName)) {
                        continue;
                    }
                    foreach ($aDataLine as $i => $strData) {
                        if ($i == count($aDataLine) - 1) {
                            continue;
                        } //! Mac: letzte Spalte Summe ignorieren
                        $building->aCounts[$aKolos[$i]] = PropertyValueC::ensureInteger($strData);
                    }

                    $area->aBuildings[] = $building;

                }

                $retVal->aAreas[] = $area;
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

        $reKoloTypes  = $this->getRegExpKoloTypes();
        $reKoloCoords = $this->getRegExpKoloCoords();

        $regExp = '/^
          (?P<area_name>[\&\w\süÜäÄöÖ]+)\n+
          \t?
          (?P<kolo_line>
          ' . $reKoloCoords . '
            (
              \n+
              \(' . $reKoloTypes . '\)
              \t
              ' . $reKoloCoords . '
            )*
            \n+
            \(' . $reKoloTypes . '\)\sSumme
          )
          (?P<data_lines>(?:
            \n+
            [^\t\n]+
            (?:\t\d*)+
          )+)
    $/mx';

        return $regExp;
    }

    /////////////////////////////////////////////////////////////////////////////

    private function getRegularExpressionKolo()
    {
        /**
         */

        $reKoloTypes  = $this->getRegExpKoloTypes();

        $regExpKolo = '/
          (?P<coords>(?P<coords_gal>\d{1,2})\:(?P<coords_sol>\d{1,3})\:(?P<coords_pla>\d{1,2}))
          \n+
          \((?P<kolo_type>' . $reKoloTypes . ')\)
          /mx';

        return $regExpKolo;
    }

}