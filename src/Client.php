<?php

namespace PartneredSolutionsIT\ZohoCrm;

use GuzzleHttp\Client;

class Client
{
    /**
     * URL for call request.
     *
     * @var string
     */
    const BASE_URI = 'https://crm.zoho.com/crm/private';	
	
    /**
     * Auth Token for get request.
     *
     * @var string
     */	
	protected $authToken;

    /**
     * Format selected for get request.
     *
     * @var string
     */	
	protected $client;
	
    /**
     * Format selected for get request.
     *
     * @var string
     */
    protected $format;	
	
	public function __construct( $authToken, Client $client, $format = "json" )
	{
		$this->authToken = $authToken;
		$this->client = $client;
		$this->format = $format;
	}
	
	protected function call( $command, $parameters, $data = [] )
	{
		
	}

    /**
     * Get the current request uri.
     *
     * @param  $module The module to use
     * @param string $command Command for get uri
     *
     * @return string
     */
    protected function getRequestURI($module, $command)
    {
        if( empty( $module ) )
		{
            throw new \Exception('End');
        }
		
        $parts = [
			self::BASE_URI, 
			$this->format, 
			$module, 
			$command
		];
		
		$uri = implode('/', $parts);
		
        return $uri;
    }	
}