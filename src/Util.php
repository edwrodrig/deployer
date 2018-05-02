<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 30-04-18
 * Time: 16:03
 */

namespace edwrodrig\deployer;


class Util
{
    /**
     * @param string $command Command to execute
     * @param null|string $current_working_dir
     * @param array $env Environment variables
     * @return array|bool
     */
    public static function runCommand(string $command, ?string $current_working_dir = null, array $env = []) {
        $process =  proc_open(
            $command,
            [
                0 => ['pipe', 'r'], // STDIN
                1 => ['pipe', 'w'], // STDOUT
                2 => ['pipe', 'w']  // STDERR
            ],
            $pipes,
            $current_working_dir,
            $env
        );

        if ( !is_resource($process) ) return false;

        // If you want to write to STDIN
        fwrite($pipes[0], '...');
        fclose($pipes[0]);

        $std_out = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $std_err = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return = proc_close($process);

        return [
            'exit_code' => $return,
            'std' => [
                'out' => $std_out,
                'err' => $std_err
            ]
        ];

    }

    public static function createTempFolder() {
        $tempfile = tempnam(sys_get_temp_dir(),'edwrodrig_deployer');
        // you might want to reconsider this line when using this snippet.
        // it "could" clash with an existing directory and this line will
        // try to delete the existing one. Handle with caution.
        if ( file_exists($tempfile) ) {
            unlink($tempfile);
        }
        mkdir($tempfile);

        if ( is_dir($tempfile) ) {
            return $tempfile;
        } else
            throw new exception\TempFolderCreationException($tempfile);
    }


}