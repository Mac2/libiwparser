<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <benjamin.woester@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Benjamin Wöster
 * ----------------------------------------------------------------------------
 */
/**
 * @author     Benjamin Wöster <benjamin.woester@googlemail.com>
 * @package    libIwParsers
 * @subpackage helpers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

return array(
    'lib'  => array(
        'aThousandSeperators' => array('.', ' ', "'", '"', 'k', '`', '´', ','),
        'aRegisteredParsers'  => array(
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserInfoGebC.php',
                'classname' => 'ParserInfoGebC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserWirtschaftGebC.php',
                'classname' => 'ParserWirtschaftGebC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserIndexC.php',
                'classname' => 'ParserIndexC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAlliMemberlisteC.php',
                'classname' => 'ParserAlliMemberlisteC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserMilSchiffUebersichtC.php',
                'classname' => 'ParserMilSchiffUebersichtC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserWirtschaftPlaniressC.php',
                'classname' => 'ParserWirtschaftPlaniressC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserWirtschaftPlaniress2C.php',
                'classname' => 'ParserWirtschaftPlaniress2C',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAlliKasseInhaltC.php',
                'classname' => 'ParserAlliKasseInhaltC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAlliKasseLogAllisC.php',
                'classname' => 'ParserAlliKasseLogAllisC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAlliKasseLogMemberC.php',
                'classname' => 'ParserAlliKasseLogMemberC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAlliKasseMemberC.php',
                'classname' => 'ParserAlliKasseMemberC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserInfoForschungC.php',
                'classname' => 'ParserInfoForschungC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserForschungC.php',
                'classname' => 'ParserForschungC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserInfoDefenceC.php',
                'classname' => 'ParserInfoDefenceC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserWirtschaftDefC.php',
                'classname' => 'ParserWirtschaftDefC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserWirtschaftUniverseC.php',
                'classname' => 'ParserWirtschaftUniverseC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserMsgTransfairC.php',
                'classname' => 'ParserMsgTransfairC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserMsgTransportC.php',
                'classname' => 'ParserMsgTransportC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserMsgC.php',
                'classname' => 'ParserMsgC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserXmlC.php',
                'classname' => 'ParserXmlC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserInfoUserC.php',
                'classname' => 'ParserInfoUserC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserAccountHighscoreC.php',
                'classname' => 'ParserAccountHighscoreC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserInfoSchiffC.php',
                'classname' => 'ParserInfoSchiffC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserHighscoreC.php',
                'classname' => 'ParserHighscoreC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserPersonalStatForschungenC.php',
                'classname' => 'ParserPersonalStatForschungenC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserPersonalStaatsformC.php',
                'classname' => 'ParserPersonalStaatsformC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserBauenAktuellC.php',
                'classname' => 'ParserBauenAktuellC',
            ),
            array(
                'filename'  => dirname(__FILE__) . '/de/parsers/ParserUniversumC.php',
                'classname' => 'ParserUniversumC',
            )
        ),
        'aPathesAutoload'     => array(
            dirname(__FILE__),
            dirname(__FILE__) . '/de',
            dirname(__FILE__) . '/enums',
            dirname(__FILE__) . '/de/parsers',
            dirname(__FILE__) . '/de/parserResults',
        ),
    ),
    'test' => array(),
    'path' => array(
        'rng'  => dirname(__FILE__) . '/xml/relaxng',
        'xslt' => dirname(__FILE__) . '/xml/xslt',
    ),
);