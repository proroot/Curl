<?php namespace Curl;

class Curl
{
    const DEFAULT_TIMEOUT    = 30;
    const DEFAULT_USER_AGENT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

    private $uCurl;
    private $uBaseUrl  = '';
    private $uUrl      = '';
    private $uResponse = '';
    private $uCookies  = [];
    private $uHeaders  = [];

    public function __construct ($uBaseUrl = '')
    {
        $this->uCurl = curl_init();

        $this->SetOption ([
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true

        ]);

        $this->SetUserAgent (self::DEFAULT_USER_AGENT);
        $this->SetTimeout (self::DEFAULT_TIMEOUT);
        $this->SetURL ($uBaseUrl);
    }

    public function GetResponse()
    {
        return $this->uResponse;
    }

    public function Exec()
    {
        $this->uResponse = curl_exec ($this->uCurl);

        return $this->uResponse;
    }

    public function Get ($uUrl, array $uData = [])
    {
        $this->SetURL ($uUrl, $uData);

        $this->SetOption ([
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET       => true
        ]);

        return $this->Exec();
    }

    public function Post ($uUrl, array $uData = [])
    {
        $this->SetURL ($uUrl);

        $this->SetOption ([
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST          => true,
            CURLOPT_POSTFIELDS    => http_build_query ($uData)
        ]);

        return $this->Exec();
    }

    public function SetHeader (array $uHeaders)
    {
        foreach ($uHeaders as $uKey => $uValue)
            $this->uHeaders[$uKey] = $uValue;

        $this->SetOption ([
            CURLOPT_HTTPHEADER => array_map (function ($uValue, $uKey)
            {
                return $uKey . ':' . $uValue;
            }, $this->uHeaders, array_keys ($this->uHeaders))
        ]);
    }

    public function SetCookie (array $uCookies)
    {
        foreach ($uCookies as $uKey => $uValue)    
            $this->uCookies[$uKey] = $uValue;

        $this->SetOption ([
            CURLOPT_COOKIE => str_replace (
                '+',
                '%20',
                http_build_query ($this->uCookies, '', ';')
            )
        ]);
    }

    public function SetCookieFile ($uFile)
    {
        $this->SetOption ([
            CURLOPT_COOKIEFILE => $uFile
        ]);
    }

    public function SetCookieJar ($uJar)
    {
        $this->SetOption ([
            CURLOPT_COOKIEJAR => $uJar
        ]);
    }

    public function SetURL ($uUrl, array $uData = [])
    {
        $this->uBaseUrl = $uUrl;
        $this->uUrl     = $this->BuildURL ($uUrl, $uData);

        $this->SetOption ([
            CURLOPT_URL => $this->uUrl
        ]);
    }

    public function SetReferrer ($uReferrer)
    {
        $this->SetOption ([
            CURLOPT_REFERER => $uReferrer
        ]);
    }

    public function SetTimeout ($uTimeout)
    {
        $this->SetOption ([
            CURLOPT_TIMEOUT => (int) $uTimeout
        ]);
    }

    public function SetUserAgent ($uUserAgent)
    {
        $this->SetOption ([
            CURLOPT_USERAGENT => $uUserAgent
        ]);
    }

    public function SetOption (array $uOptions)
    {
        foreach ($uOptions as $uKey => $uValue)
            curl_setopt ($this->uCurl, $uKey, $uValue);
    }

    private function BuildURL ($uUrl, array $uData = [])
    {
        return $uUrl . (empty ($uData)
            ? ''
            : '?' . http_build_query ($uData)
        );
    }

    public function GetInfoCurl()
    {
        return curl_getinfo ($this->uCurl);
    }

    public function Close()
    {
        if (is_resource ($this->uCurl))
            curl_close ($this->uCurl);
    }
}
