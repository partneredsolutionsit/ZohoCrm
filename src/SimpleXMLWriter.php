<?php

namespace PartneredSolutionsIT\ZohoCrm;

use SimpleXMLElement;

class SimpleXMLWriter
{
	public function toXML( array $array )
	{
		return $this->convertToXml( $array );
	}
	
	private function convertToXML( $array, $type = "Leads" )
	{
		$i = 1;
		$xml = new SimpleXMLElement('<'.$type.'/>');		
		foreach( $array AS $data )
		{
			$row = $xml->addChild('row');
			$row->addAttribute('no', $i);
			
			foreach( $data AS $key => $value )
			{
				$row->addChild('FL', $value)->addAttribute('val', $key);
			}
			$i++;
		}
		
		return $xml->asXML();		
	}
}