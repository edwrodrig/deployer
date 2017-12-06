<?php

namespace edwrodrig\deployer;

class Utils {

public static function call($command, $error_message = 'Some error had occurred') {
  passthru($command, $r);
  if ( $r > 0 ) {
    throw new \Exception($error_message);
  }
}

}
