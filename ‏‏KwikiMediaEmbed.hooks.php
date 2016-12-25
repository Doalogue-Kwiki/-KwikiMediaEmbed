<?php
/**
 * Hooks for ‏‏KwikiMediaEmbed extension
 *
 * @file
 * @ingroup Extensions
 */

class Kwiki‏‏MediaEmbedHooks {
	
	/**
	 * Register the <embedit> tag with the Parser.
	 *
	 * @param $parser Parser instance of Parser
	 * @return Boolean: true
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'embedit', array( 'Kwiki‏‏MediaEmbedHooks', 'render' ) );

		return true;
	}
	
	// Render the input box
	public static function render( $input, $args, Parser $parser ) {
		// Create ‏‏KwikiMediaEmbed
		$‏‏kwikiMediaEmbed = new ‏‏KwikiMediaEmbed( $parser );

		// Configure ‏‏KwikiMediaEmbed
		//$inputBox->extractOptions( $parser->replaceVariables( $input ) );

		// Return output
		return $‏‏kwikiMediaEmbed->kwikiRenderEmbed( $input );
	}
	
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin) {
        $out->addModules( array( "ext.‏‏KwikiMediaEmbed" ) );       
		return true;
	}
}
