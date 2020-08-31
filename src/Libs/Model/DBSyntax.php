<?php
declare(strict_types=1);

namespace App\Libs\Model;

/**
 * SQL expression value object
 */
class DBSyntax
{
    private $content;

    /**
     * __construct
     * @param string  $param  DB expression
     */
    public function __construct($param)
    {
        $this->content = $param;
    }

    /**
     * eval SQL string
     * @return string
     */
    public function getVal()
    {
        return $this->content;
    }
}
