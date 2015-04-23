<?php namespace Curl;

class Curl
{
    const DEFAULT_TIMEOUT    = 30;
    const DEFAULT_USER_AGENT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

    private $uCurl;
    private $uUrl      = '';
    private $uResponse = '';
    private $uCookies  = [];
    private $uHeaders  = [];

    public function __construct()
    {
        $this->uCurl = curl_init();

        $this->setOption ([
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true

        ]);

        $this->setUserAgent(self::DEFAULT_USER_AGENT);
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }

    public function getResponse()
    {
        return $this->uResponse;
    }

    public function exec()
    {
        $this->uResponse = curl_exec($this->uCurl);

        return $this;
    }

    public function get($uUrl, array $uData = [])
    {
        $this->setURL($uUrl, $uData);

        $this->setOption([
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET       => true
        ]);

        $this->exec();

        return $this;
    }

    public function post($uUrl, array $uData = [])
    {
        $this->setURL($uUrl);

        $this->setOption([
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST          => true,
            CURLOPT_POSTFIELDS    => http_build_query($uData)
        ]);

        $this->exec();

        return $this;
    }

    public function setHeader(array $uHeaders)
    {
        foreach ($uHeaders as $uKey => $uValue)
            $this->uHeaders[$uKey] = $uValue;

        $this->setOption([
            CURLOPT_HTTPHEADER => array_map(function ($uValue, $uKey)
            {
                return $uKey . ':' . $uValue;
            }, $this->uHeaders, array_keys($this->uHeaders))
        ]);

        return $this;
    }

    public function setCookie(array $uCookies)
    {
        foreach ($uCookies as $uKey => $uValue)    
            $this->uCookies[$uKey] = $uValue;

        $this->setOption([
            CURLOPT_COOKIE => str_replace(
                '+',
                '%20',
                http_build_query($this->uCookies, '', ';')
            )
        ]);

        return $this;
    }

    public function setCookieFile($uFile)
    {
        $this->setOption([
            CURLOPT_COOKIEFILE => $uFile
        ]);

        return $this;
    }

    public function setCookieJar($uJar)
    {
        $this->setOption([
            CURLOPT_COOKIEJAR => $uJar
        ]);

        return $this;
    }

    public function setURL($uUrl, array $uData = [])
    {
        $this->uUrl = $this->buildURL($uUrl, $uData);

        $this->setOption([
            CURLOPT_URL => $this->uUrl
        ]);

        return $this;
    }

    public function setReferrer($uReferrer)
    {
        $this->setOption([
            CURLOPT_REFERER => $uReferrer
        ]);

        return $this;
    }

    public function setTimeout($uTimeout)
    {
        $this->setOption([
            CURLOPT_TIMEOUT => (int) $uTimeout
        ]);

        return $this;
    }

    public function setUserAgent($uUserAgent)
    {
        $this->setOption([
            CURLOPT_USERAGENT => $uUserAgent
        ]);

        return $this;
    }

    public function setOption(array $uOptions)
    {
        foreach ($uOptions as $uKey => $uValue)
            curl_setopt($this->uCurl, $uKey, $uValue);

        return $this;
    }

    private function buildURL($uUrl, array $uData = [])
    {
        return $uUrl . (empty ($uData)
            ? ''
            : '?' . http_build_query($uData)
        );
    }

    public function getInfo()
    {
        return curl_getinfo($this->uCurl);
    }

    public function close()
    {
        if (is_resource($this->uCurl))
            curl_close($this->uCurl);
    }
}
