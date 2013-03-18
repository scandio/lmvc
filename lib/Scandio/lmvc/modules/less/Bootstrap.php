<?php

namespace Scandio\lmvc\modules\less;

use \Scandio\lmvc\framework\LVC;

class Bootstrap extends \Scandio\lmvc\framework\Bootstrap {
    public function initialize() {
        LVC::registerControllerNamespace(new controllers\Less);
    }
}
