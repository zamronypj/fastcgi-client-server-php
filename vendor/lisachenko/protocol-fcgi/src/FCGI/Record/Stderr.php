<?php
/**
 * @author Alexander.Lisachenko
 * @date 08.09.2015
 */

namespace Protocol\FCGI\Record;

use Protocol\FCGI;
use Protocol\FCGI\Record;

/**
 * Stderr binary stream
 *
 * FCGI_STDERR is a stream record for sending arbitrary data from the application to the Web server
 */
class Stderr extends Record
{
    public function __construct($contentData = '')
    {
        $this->type = FCGI::STDERR;
        $this->setContentData($contentData);
    }
}
