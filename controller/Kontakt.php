<?php

class Kontakt extends AbstractController {

    protected $_template = 'kontakt';

    protected $topics = [
        'Ich habe einen Fehler gefunden',
        'Ich habe einen Verbesserungsvorschlag / Feature Request',
        'Lob',
        'Kritik',
        'Sonstiges'
    ];

    public function action() {

        $this->assign('topics', $this->topics);
        $captcha = imagecreatetruecolor(200, 20);
        $code = rand(10000, 99999);

        $token = json_encode( ['timestamp' => time(), 'code' => $code]);


        $subject = $_POST['subject'] ?? count($this->topics)-1;
        $this->assign('subject', $subject);

        if (isset($_POST['subject']) && isset($_POST['body']) && !empty($_POST['body'])) {

            $reply = (isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : 'Anonym') .
                    '<' . (isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : 'noreply@mail.sui.li') . '>';


            mail('s.oezguer@gmail.com, coronahh@merlinwolf.de', 'coronahh.de (' . $this->topics[$subject] . ')', "From: " . $reply . "\n" . $_POST['body'], "From: coronahh@mail.sui.li\nReply-to: " . $reply);

            $this->assign('sent', true);
        } else {
            $this->assign('sent', false);
        }
    }

}