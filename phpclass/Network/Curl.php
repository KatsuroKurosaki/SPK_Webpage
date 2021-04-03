<?php

namespace Network;

class Curl
{

	const HTTP_REQUEST_GET = "GET";
	const HTTP_REQUEST_POST = "POST";
	const HTTP_REQUEST_PUT = "PUT";
	const HTTP_REQUEST_DELETE = "DELETE";
	const DEFAULT_TIMEOUT = 10;

	private $_useragent;
	private $_url;
	private $_timeout;
	private $_cookieJar;
	private $_curl;
	private $_responseInfo;
	private $_responseHeader;
	private $_responseBody;
	private $_responseError;
	private $_responseErrorMsg;

	public function __construct($url = "")
	{
		$curlinfo = curl_version();
		$this->_useragent = "PHP/" . PHP_VERSION . " (" . PHP_OS . "; " . $curlinfo['host'] . ") cURL/" . $curlinfo['version'] . " " . $curlinfo['ssl_version'];
		unset($curlinfo);

		$this->_curl = curl_init();
		curl_setopt_array($this->_curl, [
			CURLOPT_VERBOSE => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FAILONERROR => true,
			CURLOPT_URL => $this->_url,
			CURLOPT_HEADER => true,
			CURLINFO_HEADER_OUT => true,
			CURLOPT_USERAGENT => $this->_useragent,
			CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
		]);
		$this->setUrl($url);
	}

	public function __destruct()
	{
		curl_close($this->_curl);
		if (isset($this->_cookieJar) && file($this->_cookieJar)) {
			unlink($this->_cookieJar);
		}
	}

	public function setParam($option, $value)
	{
		// More info on: http://php.net/manual/en/function.curl-setopt.php
		return curl_setopt($this->_curl, $option, $value);
	}

	public function setUrl(string $url)
	{
		$this->_url = $url;
		$this->setParam(CURLOPT_URL, $this->_url);
	}

	public function setUserAgent(string $userAgent)
	{
		$this->_useragent = $userAgent;
		$this->setParam(CURLOPT_USERAGENT, $this->_useragent);
	}

	public function setSslCheck($value)
	{
		$this->setParam(CURLOPT_SSL_VERIFYPEER, $value);
		$this->setParam(CURLOPT_SSL_VERIFYHOST, $value);
	}

	public function setHttpMethod($value)
	{
		$this->setParam(CURLOPT_CUSTOMREQUEST, $value);
	}

	public function setPostData($value)
	{
		$this->setParam(CURLOPT_POSTFIELDS, $value);
	}

	public function setCookieJar($file)
	{
		$this->_cookieJar = $file;
		$this->setParam(CURLOPT_COOKIEJAR, $this->_cookieJar);
		$this->setParam(CURLOPT_COOKIEFILE, $this->_cookieJar);
	}

	// - $headers: ['Content-Type: application/json']
	public function setHeaders($headers)
	{
		$this->setParam(CURLOPT_HTTPHEADER, $headers);
	}

	// Proxy types:
	// - CURLPROXY_HTTP
	// - CURLPROXY_SOCKS4
	// - CURLPROXY_SOCKS5
	// - CURLPROXY_SOCKS4A
	// - CURLPROXY_SOCKS5_HOSTNAME
	public function setProxy($proxyType, $proxyURL)
	{
		$this->setParam(CURLOPT_PROXY, $proxyURL);
		$this->setParam(CURLOPT_PROXYTYPE, $proxyType);
	}

	public function getInfo()
	{
		return $this->_responseInfo;
	}

	public function getHeader()
	{
		return $this->_responseHeader;
	}

	public function getBody()
	{
		return $this->_responseBody;
	}

	public function getError()
	{
		return $this->_responseError;
	}

	public function getErrorMsg()
	{
		return $this->_responseErrorMsg;
	}

	public function execute()
	{
		$result = curl_exec($this->_curl);
		$this->_responseError = curl_errno($this->_curl);
		$this->_responseErrorMsg = curl_error($this->_curl);
		$this->_responseInfo = curl_getinfo($this->_curl);
		$this->_responseHeader = substr($result, 0, $this->_responseInfo['header_size']);
		$this->_responseBody = substr($result, $this->_responseInfo['header_size']);
		return $this->_responseError;
	}
}
