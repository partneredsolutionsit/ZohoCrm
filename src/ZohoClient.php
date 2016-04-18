<?php

namespace PartneredSolutionsIT\ZohoCrm;

use GuzzleHttp\Client;

class ZohoClient
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
	
    /**
     * Format selected for get response.
     *
     * @var int
     */
    protected $newFormat;	
	
	private $xmlWriter;
	
	public function __construct( $authToken, Client $client, $format = "json", $newFormat = 1)
	{
		$this->authToken	 = $authToken;
		$this->client		 = $client;
		$this->format 		 = $format;
		$this->newFormat 	 = $newFormat;
	}
	
	public function setXMLWriter( $xml )
	{
		$this->xmlWriter = $xml;
	}
	
	public function getMyRecords()
	{
		$response = $this->call( 'getMyRecords' );
		
		return $response;
	}	
	
	public function getRecords()
	{
		$response = $this->call( 'getRecords' );
		
		return $response;
	}
	
	public function getRecordById( $recordId )
	{
		$id = "id";
		
		if(is_array( $recordId ) )
		{
			if( count( $recordId ) > 100 )
			{
				throw new \Exception('To many Records, Limit is 100');
			}
			
			$id = "idlist";
			$recordId = implode(';', $recordId);
		}
		
		$response = $this->call( 'getRecordById', [$id => $recordId] );
		
		return $response;
	}	

	public function getDeletedRecordIds()
	{
		$response = $this->call( 'getDeletedRecordIds', [] );

		//Not sure why this isn't returning properly except in this fashion.
		return $response->getBody()->getContents();
	}
	
	public function insertRecords( $xmlData )
	{
		$response = $this->call( 'insertRecords', ['xmlData' => $xmlData] );
		
		return $response;		
	}
	
	public function updateRecords( $xmlData )
	{
		$data = [
			'version'	=> 4,
			'xmlData'	=> $xmlData,
		];
		
		$response = $this->call( 'updateRecords', $data );
		
		return $response;		
	}
	
	public function getSearchRecordsByPDC(){}
	
	public function deleteRecords( $recordId )
	{
		$data = [
			"id" 		=> $recordId,
		];
		
		$response = $this->call( 'deleteRecords', $data );
		
		return $response;		
	}
	
	public function convertLead( $leadId, $xmlData )
	{
		$data = [
			"leadId" 		=> $leadId,
			"xmlData" 		=> $xmlData,
		];
		
		$response = $this->call( 'convertLead', $data );
		
		return $response;				
		
	}
	
	public function getRelatedRecords(){}

	public function getFields($module = 'Leads')
	{	
		$response = $this->call( 'getFields', [], $module );
		
		return $response;				
	}	
	
	public function updateRelatedRecords(){}
	
	/*
		$types available
		AllUsers
		ActiveUsers
		DeactiveUsers
		AdminUsers
		ActiveConfirmedAdmins	
	*/
	public function getUsers( $type = "AllUsers" )
	{
		$data = [
			"type" 		=> $type,
		];
		
		$response = $this->call( 'getUsers', $data, 'Users' );
		
		return $response;			
	}
	
	//need to add option to stream or save
	public function uploadFile( $recordId, $filePath )
	{
		$data = [
			[
				'name'		=> 'id',
				'contents'	=> $recordId,
			],
			[
				'name'		=> 'content',
				'contents'	=> fopen( $filePath, 'r'),
				//'filename'	=> 'Temp',
			],
		];
		
		$response = $this->calls( 'uploadFile', $data );
		
		return $response;					
	}
	
	public function delink( $recordId, $relatedId, $relatedModule, $module = 'Leads', )
	{
		$data = [
			'id'			=> $recordId,
			'relatedId' 	=> $relatedId,
			'relatedModule'	=> $relatedModule,
		];
		
		$reponse = $this->call( 'delink', $data, $module );
		
		return $response;
	}
	
	//need to add option to stream or save
	public function downloadFile( $id )
	{
		$data = [
			"id" 		=> $id,
		];
		
		$response = $this->call( 'downloadFile', $data );
		
		return $response;					
	}
	
	public function deleteFile( $fileId )
	{
		$data = [ 'id' => $fileId ];

		$response = $this->call( 'deleteFile', $data );
		
		return $response;			
	}
	
	//need to add option to stream or save
	public function uploadPhoto( $recordId, $filePath, $module = 'Leads' )
	{
		$data = [
			[
				'name'		=> 'id',
				'contents'	=> $recordId,
			],
			[
				'name'		=> 'content',
				'contents'	=> fopen( $filePath, 'r'),
				//'filename'	=> 'Temp',
			],
		];
		
		$response = $this->calls( 'uploadPhoto', $data, $module );
		
		return $response;					
	}
	
	public function downloadPhoto( $recordId, $module = 'Leads' )
	{
		$data = ['id' => $recordId];
		
		$response = $this->call( 'downloadPhoto', $data );
		
		return $response;
	}
	
	public function deletePhoto( $recordId, $module = 'Leads')
	{
		$data = ['id' => $recordId];
		
		$response = $this->call( 'deletePhoto', $data );
		
		return $response;		
	}
	
	public function getModules()
	{
		$response = $this->call( 'getModules', [], 'Info' );
		
		return $response;
	}	

	public function searchRecords()
	{
		$data = [
			"criteria" 		=> "(Last Name:Testerson)",
		];
		
		$response = $this->call( 'searchRecords', $data );
		
		return $response;
	}	
	
	protected function call( $command, $data = [], $module = 'Leads' )
	{
		$data['authtoken'] =  $this->authToken;
		$data['scope']	   = 'crmapi';
		
		$res = $this->client->request('post', $this->getRequestURI($module, $command), [
			'form_params' => $data,
		]);		
		
		return $res;
	}
	
	protected function calls( $command, $data = [], $module = "Leads" )
	{
		$multipart =	[
				[
					'name'		=> 'authtoken',
					'contents'	=> $this->authToken,
				],
				[
					'name'		=> 'scope',
					'contents'	=> "crmapi",				
				],			
		];
		
		foreach( $data AS $dats )
		{
			$multipart[] = $dats;
		}
	
		$res = $this->client->request('post', $this->getRequestURI($module, $command), [
			'multipart' => $multipart,
		]);		
		
		return $res;		
		
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