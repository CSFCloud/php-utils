<?php

namespace CSFCloud\Azure;

use CSFCloud\Shell\CommandRunner;

class Storage {

    private $connection_string;
    private $container;

    private $account_name;
    private $account_key;

    private $exec;

    public function __construct(string $container, string $connection_string) {
        $this->exec = new CommandRunner();

        $this->connection_string = $connection_string;
        $this->container = $container;

        $dt = explode(";", $this->connection_string);
        foreach ($dt as $pair) {
            $pdt = explode("=", $pair);
            if ($pdt[0] == "AccountName") {
                $this->account_name = str_ireplace("AccountName=", "", $pdt[1]);
            } else if ($pdt[0] == "AccountKey") {
                $this->account_key = str_ireplace("AccountKey=", "", $pdt[1]);
            }
        }
    }

    protected function run($cmd) : string {
        $logfile = $this->exec->run(CommandRunner::COMMAND_SYNC, __DIR__, $cmd, true);
        $log = $logfile->getText();
        $logfile->delete();
        return $log;
    }

    public function getContainer() : string {
        return $this->container;
    }

    public function getAccountName() : string {
        $this->account_name;
    }

    public function uploadToBlob(string $file, string $name, string $content_type = null) {
        if (!file_exists($file) || !is_file($file)) {
            throw new Exception("Invalid file");
        }

        $cmd = "az storage blob upload";

        $cmd .= " -c " . escapeshellarg($this->container);
        $cmd .= " -f " . escapeshellarg($file);
        $cmd .= " -n " . escapeshellarg($name);

        if ($content_type != null) {
            $cmd .= " --content-type " . escapeshellarg($content_type);
        } else {
            $cmd .= " --content-type " . escapeshellarg(mime_content_type($file));
        }

        $cmd .= " --connection-string " . escapeshellarg($this->connection_string);

        $log = $this->run($cmd);

        return $log;
    }

    public function GenerateBlobSas(string $name, int $expire = 5 * 60 * 60, string $ip = null) : string {
        $signedPermissions = "r";
        //$signedStart = gmdate("Y-m-d\TH:i\Z", time());
        $signedExpiry = gmdate("Y-m-d\TH:i\Z", time() + $expire);
        $signedProtocol = "https";
        $signedVersion = "2017-07-29";//"2016-05-31";

        $parameters = array();
        $parameters[] = $signedPermissions;
        $parameters[] = ""; // signedStart;
        $parameters[] = $signedExpiry;
        $parameters[] = "/blob/" . $this->account_name . "/" . $this->container . "/" . $name;
        $parameters[] = ""; // signedIdentifier
        if ($ip == null) {
            $parameters[] = "";
        } else {
            $parameters[] = $ip;
        }
        $parameters[] = $signedProtocol;
        $parameters[] = $signedVersion;
        $parameters[] = ""; // cacheControl
        $parameters[] = ""; // contentDisposition
        $parameters[] = ""; // contentEncoding
        $parameters[] = ""; // contentLanguage
        $parameters[] = ""; // contentType

        $stringToSign = utf8_encode(implode("\n", $parameters));
        $decodedAccountKey = base64_decode($this->account_key);
        $signature = hash_hmac("sha256", $stringToSign, $decodedAccountKey, true);
        $sig = urlencode(base64_encode($signature));

        $sas  = 'sv=' . $signedVersion;
        $sas .= '&sr=' . "b";
        //$sas .= $buildOptQueryStr($cacheControl, '&rscc=');
        //$sas .= $buildOptQueryStr($contentDisposition, '&rscd=');
        //$sas .= $buildOptQueryStr($contentEncoding, '&rsce=');
        //$sas .= $buildOptQueryStr($contentLanguage, '&rscl=');
        //$sas .= $buildOptQueryStr($contentType, '&rsct=');
        //$sas .= $buildOptQueryStr($signedStart, '&st=');
        $sas .= '&se=' . $signedExpiry;
        $sas .= '&sp=' . $signedPermissions;
        if ($ip != null) {
            $sas .= '&sip=' . $ip;
        }
        $sas .= '&spr=' . $signedProtocol;
        //$sas .= $buildOptQueryStr($signedIdentifier, '&si=');
        $sas .= '&sig=' . $sig;

        return $sas;
    }

    public function getBlobUrl(string $name) {
        $url = "https://" . $this->account_name . ".blob.core.windows.net/" . $this->container . "/" . $name;
        return $url;
    }

}