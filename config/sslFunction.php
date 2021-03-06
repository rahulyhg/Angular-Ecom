<?php

class SSLSms {

    public function SendOrderNotification($user_t = 'ticketchai', $order_id_t = '[000000-0000]', $mobile = '00',$amount='0.00', $st = 0) {

        if ($st != 0) {
            $user = urlencode("ticketchai");
            $pass = urlencode("ticketchai@123");
            $sid = "TicketChai";
            $phone = $this->PhoneNumberVerfiy(1, $mobile);
            $sms = $this->DefineSMS($st,$user_t,$order_id_t,$amount);
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
            $param = "user=" . $user . "&pass=" . $pass . "&sms[0][0]=" . $phone . " &sms[0][1]=" . $sms . "&sms[0][2]=" . time() . "&sid=$sid";
            $sslResponse = $this->CurlData($url, $param);
            $sms_status = $this->CheckResponse($sslResponse);
        } else {
            return "Failed";
        }
        return $sms_status;
    }

    private function CurlData($url, $param) {
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);

        return $response;
    }

    private function CheckResponse($sslResponse) {
        preg_match("'<PARAMETER>(.*?)</PARAMETER>'si", $sslResponse, $parameter);
        preg_match("'<STAKEHOLDERID>(.*?)</STAKEHOLDERID>'si", $sslResponse, $stakeholder);
        preg_match("'<PERMITTED>(.*?)</PERMITTED>'si", $sslResponse, $permitted);
        preg_match("'<LOGIN>(.*?)</LOGIN>'si", $sslResponse, $login);
        preg_match("'<PUSHAPI>(.*?)</PUSHAPI>'si", $sslResponse, $pushapi);
        preg_match("'<MSISDNSTATUS>(.*?)</MSISDNSTATUS>'si", $sslResponse, $invalidno);


        @$str_parameter = str_replace("<PARAMETER>", "", $parameter[0]);
        @$str_parameter2 = str_replace("</PARAMETER>", "", $str_parameter);

        @$str_stakeholder = str_replace("<STAKEHOLDERID>", "", $stakeholder[0]);
        @$str_stakeholder2 = str_replace("</STAKEHOLDERID>", "", $str_stakeholder);

        @$str_permitted = str_replace("<PERMITTED>", "", $permitted[0]);
        @$str_permitted2 = str_replace("</PERMITTED>", "", $str_permitted);

        @$str_login = str_replace("<LOGIN>", "", $login[0]);
        @$str_login2 = str_replace("</LOGIN>", "", $str_login);

        @$str_pushapi = str_replace("<PUSHAPI>", "", $pushapi[0]);
        @$str_pushapi2 = str_replace("</PUSHAPI>", "", $str_pushapi);

        @$str_invalidno = str_replace("<MSISDNSTATUS>", "", $invalidno[0]);
        $not_invalid = 0;
        if ($str_invalidno == 'Invalid Mobile No') {
            return "INVALID PHONE NUMBER";
        } else {
            $sms_status = 0;
            if ($str_parameter2 == "OK") {
                $sms_status+=1;
            }
            if ($str_stakeholder2 == "OK") {
                $sms_status+=1;
            }
            if ($str_permitted2 == "OK") {
                $sms_status+=1;
            }
            if ($str_login2 == "SUCCESSFULL") {
                $sms_status+=1;
            }
            if ($str_pushapi2 == "ACTIVE") {
                $sms_status+=1;
            }


            if ($sms_status == 5) {
                $not_invalid = 1;
            }

            if ($not_invalid == 1) {
                return "SENT";
            } else {
                return "FAILED";
            }
        }
    }

    private function MakePhoneNumber($phone) {
        return urlencode("88" . $phone);
    }

    private function PhoneNumberVerfiy($st, $phone) {
        if ($st == 0) {
            return 3;
        } elseif ($st == 1) {
            return $this->MakePhoneNumber($phone);
        }
    }

    private function DefineSMS($st = 0, $name, $order_id,$amount) {
        if ($st == 1) {
            return $this->BookingOrderSMS($name, $order_id);
        } elseif ($st == 2) {
            return $this->ConfirmOrderSMS($name, $order_id,$amount);
        } elseif ($st == 3) {
            return $this->PaidOrderSMS($name, $order_id,$amount);
        }
    }

    private function BookingOrderSMS($name, $order_id) {
        return urldecode("Your order has been placed with order#" . $order_id . ". The order will be CONFIRMED soon and you will be notified through SMS. Thank you. ticketchai.com");
    }

    private function ConfirmOrderSMS($name, $order_id,$amount) {
        return urldecode("Your order has been CONFIRMED. Order# " . $order_id . ". Total Amount: ".$amount.". Thank you. ticketchai.com");
    
        
    }

    private function PaidOrderSMS($name, $order_id,$amount) {
        return urldecode("Your order# " . $order_id . " has been CONFIRMED and we have received total payment for Tk. ".$amount." Thank you.");
        
    }

}

?>
