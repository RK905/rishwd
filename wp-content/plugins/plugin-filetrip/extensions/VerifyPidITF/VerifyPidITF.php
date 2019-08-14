<?php

/**
 * Verify Purchase ID Extension
 *  This extension wil run with plugin installation and will verify the evanto purchase id
 *
 * @method init() as initialization after active
 */
namespace iTechFlare\WP\iTechFlareExtension;

use iTechFlare\WP\Plugin\FileTrip\Core\Abstracts\FlareExtension;
use iTechFlare\WP\Plugin\FileTrip\Core\Helper\LoaderOnce;

/**
 * Class Example
 * @package iTechFlare\WP\iTechFlareExtension
 */
class VerifyPIDITF /*extends FlareExtension*/
{
	/**
	 * @var string
	 */
	protected $extension_name = 'Verify Purchase ID';

	/**
	 * @var string
	 */
	protected $extension_uri = 'https://itechflare.com'; // with or without http://

	/**
	 * @var string
	 */
	protected $extension_author = 'iTechFlare';

	/**
	 * @var string
	 */
	protected $extension_author_uri = \Filetrip_Constants::ITF_WEBSITE_LINK;

	/**
	 * @var string
	 */
	protected $extension_version = '1.0.0';

	/**
	 * @var string
	 */
	protected $extension_description = 'Run on filetrip plugin activation and ask user to submit his plugin purchase ID';
	/**
	 * @var string
	 *      fill with full URL to Extension icon
	 *      please use Square recommendation is :
	 *      128px square max 256px
	 *      Extension must be jpg or jpeg
	 */
	protected $extension_icon; // fill with icon url

	/**
	 * @var capability
	 */
	protected $capability = 'none';

	/**
	 * Initials
	 */
	public function init()
	{
	
	// ************************* Verify Purchase ID *************
		// include
		// LoaderOnce::load(__DIR__ . '/verifypid/classes/Filetrip_verifypid.php');


		// Call verify purchase id extension
        // Filetrip_verifypid::verify_id();	
	}
}

