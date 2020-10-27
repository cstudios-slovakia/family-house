<?php
/**
 * filterResult module for Craft CMS 3.x
 *
 * Helper for twigs layout filtering
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\filterresultmodule\variables;



/**
 * @author    @lostika
 * @package   FilterResultModule
 * @since     1.0.0
 */
class FilterResultModuleVariable
{
    // Public Methods
    // =========================================================================

    protected $result;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }


}
