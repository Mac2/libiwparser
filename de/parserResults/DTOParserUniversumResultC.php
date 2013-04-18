<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <masel789@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * masel
 * ----------------------------------------------------------------------------
 */
/**
 * @author     masel <masel789@googlemail.com>
 * @package    libIwParsers
 * @subpackage parsers_de
 */

namespace libIwParsers\de\parserResults;

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_universum
 */
class DTOParserUniversumResultC
{
    /**
     * @soap
     * @var int $iTimestamp when has the info has been populated
     */
    public $iTimestamp = 0;

    /**
     * @soap
     * @var array $aPlanets of object DTOParserUniversumPlanetResultC
     */
    public $aPlanets = array();

}