<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Funct{


    public function telegram_notification($mess,$option1, $option2, $filename, $chatID, $token) {
        if($mess == 'new_convert'){
            $text = "<b>New Convert file apk2aab </b> \n"
                . "<b>Email Address: </b>\n"
                . "$option1 \n"
                . "<b>Phone: </b>\n"
                . "$option2 \n"
                . "<b>File: </b>\n"
                . "$filename";
        }elseif ($mess == 'accept-convert'){
            $text = "<b>Accept convert file demo </b>\n"
                . "<b>Price: </b>\n"
                . "$option1 - $option2  \n"
                . "<b>File: </b>\n"
                . "$filename";
        }elseif ($mess == 'reject-convert'){
            $text = "<b>Reject convert file demo </b>\n"
                . "<b>Price: </b>\n"
                . "$option1 - $option2  \n"
                . "<b>File: </b>\n"
                . "$filename";
        }
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?parse_mode=html&chat_id=" . $chatID;
        $url = $url . "&text=" . urlencode($text);
        file_get_contents($url);
    }





}
