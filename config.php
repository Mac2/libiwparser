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

namespace libIwParsers;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

return array(
    'lib'  => array(
        'aRegisteredParsers'  => array(
            array(
                'class' => '\libIwParsers\de\parsers\ParserInfoGebC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserWirtschaftGebC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserIndexC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAlliMemberlisteC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserMilSchiffUebersichtC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserWirtschaftPlaniressC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserWirtschaftPlaniress2C',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAlliKasseInhaltC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAlliKasseLogAllisC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAlliKasseLogMemberC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAlliKasseMemberC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserInfoForschungC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserForschungC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserInfoDefenceC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserWirtschaftDefC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserMsgTransfairC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserMsgTransportC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserMsgC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserXmlC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserInfoUserC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserAccountHighscoreC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserInfoSchiffC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserHighscoreC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserPersonalStatForschungenC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserPersonalStaatsformC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserBauenAktuellC',
            ),
            array(
                'class' => '\libIwParsers\de\parsers\ParserUniversumC',
            ),
        ),
    ),
    'test' => array(),
    'path' => array(
        'rng'      => dirname(__FILE__) . DIRECTORY_SEPARATOR .
            'xml' . DIRECTORY_SEPARATOR .
            'relaxng',
        'xslt'     => dirname(__FILE__) . DIRECTORY_SEPARATOR .
            'xml' . DIRECTORY_SEPARATOR .
            'xslt',
    ),
);