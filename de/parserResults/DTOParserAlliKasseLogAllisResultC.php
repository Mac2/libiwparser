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
 * @package    libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_alli_kasse_log_allis
 */
class DTOParserAlliKasseLogAllisResultC
{
    /**
     * @soap
     * @var integer $iDateTime unix timestamp of log
     */
    public $iDateTime = 0;

    /**
     * @soap
     * @var string $strFromUser user that paid
     */
    public $strFromUser = "";

    /**
     * @soap
     * @var string $strAlliName name of alliance that got paid
     */
    public $strAlliName = "";

    /**
     * @soap
     * @var string $strAlliTag tag of alliance that got paid
     */
    public $strAlliTag = "";

    /**
     * @soap
     * @var integer $iCredits amount of paid credits
     */
    public $iCredits = 0;
}