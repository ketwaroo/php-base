<?php

namespace Ketwaroo\Config;

use Ketwaroo\Config;

/**
 * singletom instance of config
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class GlobalRegistry extends Config
{

    use \Ketwaroo\Pattern\TraitMultiSingleton;
}
