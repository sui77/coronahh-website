<?php

class Test extends AbstractController {

    protected $_template = 'test';

    public function action() {
        if ($_POST['action'] == 'screenshot') {

            exec('chromium-browser --no-sandbox --headless --disable-gpu --screenshot=/tmp/screenshot.png --window-size=800,1024 https://hhdata.sui.li/', $out, $err);
            print_r($out); print_r($err);
            exit();
            header('Content-type: application/octett-stream');
            header('Content-Disposition: attachment; filename="coronahh-' . date('Y-m-d-his') . '.png"');
            echo file_get_contents('/tmp/screenshot.png');
            exit();
        }
    }
}