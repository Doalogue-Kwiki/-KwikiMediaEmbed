<?php
/**
 * Classes for ‏‏KwikiMediaEmbed extension
 *
 * @file
 * @ingroup Extensions
 */

// ‏‏KwikiMediaEmbed class
class ‏‏KwikiMediaEmbed {
		
	/* Fields */

	private $mParser;
	private $mType;
	
	
	/* Functions */
	public function __construct( $parser ) {
		$this->mParser = $parser;
		$this->mType = "";		
	}	
	
	private function createOutput( $preText, $regexMatches, $postOutput ) {
		$preOutput = '<div class="embedWrapper ' . $this->mType . 'Embed ">';
		$match = $regexMatches[1];
		return $preOutput . $preText . htmlspecialchars($match) . $postOutput;
	}
	
	# The callback function for converting the input text to HTML output
	public function kwikiRenderEmbed($input){
		
		if (empty($input)) {
			return '';
		}

		$regexMatches = array();
		$out = "";
		$postIframeOutput = '" frameborder="0" allowfullscreen></iframe></div>';
				
		// Regular expression for extracting only YouTube Url
		/*
			https://www.youtube.com/watch?v={{someKeyID}}
		*/
		if ( empty($out) && preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $input, $regexMatches) ) {
			$this->mType = "youTube";
			$preText = '<iframe src="https://www.youtube.com/embed/';
			$out = createOutput( $preText, $regexMatches, $postIframeOutput);
		}
		
		// Regular expression for extracting only Google Drive
		/*
			https://drive.google.com/drive/u/0/folders/{{someKeyID}}
		*/
		if ( empty($out) && preg_match('/(?<=drive\.google\.com\/drive\/u\/0\/folders\/)(.*)/', $input, $regexMatches) ) {			
			$this->mType = "googleDrive";
			$preText = '<iframe src="https://drive.google.com/embeddedfolderview?id=';
			$postText = '#grid ' . $postIframeOutput;
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Docs Url
		/*
			<iframe src="https://docs.google.com/document/d/{{someKeyID}}/pub?embedded=true"></iframe>
		*/
		if ( empty($out) && preg_match('/(docs\.google\.com\/document\/d\/.*?\/)/', $input, $regexMatches) ) {
			$this->mType = "googleDocs";
			$preText = '<iframe src="https://';
			$postText = 'pub?embedded=true ' . $postIframeOutput;
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Spreadsheets Url
		/*
		<iframe src="https://docs.google.com/spreadsheets/d/{{someKeyID}}/pubhtml?widget=true&amp;headers=false"></iframe>
		*/
		if ( empty($out) && preg_match('/(docs\.google\.com\/spreadsheets\/d\/.*?\/)/', $input, $regexMatches) ) {
			$this->mType = "googleSpreadsheets";
			$preText = '<iframe src="https://';
			$postText = 'pubhtml?widget=true&amp;headers=false ' . $postIframeOutput;
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Presentation Url
		/*
		<iframe src="https://docs.google.com/presentation/d/{{someKeyID}}/embed?start=false&loop=true&delayms=5000" frameborder="0" width="960" height="569" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
		*/
		if ( empty($out) && preg_match('/(docs\.google\.com\/presentation\/d\/.*?\/)/', $input, $regexMatches) ) {
			$this->mType = "googlePresentation";
			$preText = '<iframe src="https://';
			$postText = 'embed?start=false&loop=true&delayms=5000" frameborder="0" width="960" height="569" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe></div>';
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Forms Url
		/*
		<iframe src="https://docs.google.com/forms/d/e/{{someKeyID}}/viewform?embedded=true" width="760" height="500" frameborder="0" marginheight="0" marginwidth="0">טוען...</iframe>
		*/
		if ( empty($out) && preg_match('/(docs\.google\.com\/forms\/d\/e\/.*?\/)/', $input, $regexMatches) ) {
			$this->mType = "googleForms";
			$preText = '<iframe src="https://';
			$postText = 'viewform?embedded=true" frameborder="0" width="760" height="500" marginheight="0" marginwidth="0" allowfullscreen></iframe></div>';
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Drawings Url
		/*
			<img src="https://docs.google.com/drawings/d/{{someKeyID}}/pub?w=960&amp;h=720">
		*/
		if ( empty($out) && preg_match('/(docs\.google\.com\/drawings\/d\/.*?\/)/', $input, $regexMatches) ) {
			$this->mType = "googleDrawings";
			$preText = '<img src="https://';
			$postText = 'pub?w=960&amp;h=720"></div>';
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only Google Calender Url
		/*
			<iframe src="{{someKeyID}}" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
		*/
		if ( empty($out) && preg_match('/(calendar\.google\.com\/calendar\/.*?")/', $input, $regexMatches) ) {
			$this->mType = "googleCalender";
			$preText = '<iframe src="https://';
			$postText = '" scrolling="no' . $postIframeOutput;
			$out = createOutput( $preText, $regexMatches, $postText);
		}
		
		// Regular expression for extracting only the iframe URL
		/*
			For example google maps
			<iframe src="https://www.google.com/maps/embed/v1/place?q=place_id:..&key=..." ></iframe>
		*/
		if ( empty($out) && preg_match('/src="([^"]+)"/', $input, $regexMatches) ) {
			$this->mType = "any";
			$preText = '<iframe src="https://';
			$out = createOutput( $preText, $regexMatches, $postIframeOutput);
		}
			
		if ( empty($out) ) 
		{
			$console = "<script>console.log( 'Not found: " . $input . "' );</script>";
			$out = $console;               
		}

		return $out;
	}
}