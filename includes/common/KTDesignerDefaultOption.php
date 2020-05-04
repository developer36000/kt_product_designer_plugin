<?php
namespace includes\common;


class KTDesignerDefaultOption
{
	/**
	 * Returns an array of default settings
	 * @return array
	 */
	public static function getDefaultOptions()
	{
		$defaults = array(
			'starter' => array(
				'marker' => ' ',
				'token' => ' ',
				'checkbox' => '0'
			)
		);
		// Filter to which you can connect and
		// change the array of default settings
		$defaults = apply_filters('kt_designer_default_option', $defaults );
		return $defaults;
	}
}