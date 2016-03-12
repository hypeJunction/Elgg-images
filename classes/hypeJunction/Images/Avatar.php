<?php

namespace hypeJunction\Images;

use hypeJunction\Images\Image;

class Avatar extends Image {

	const SUBTYPE = 'avatar';
	
	/**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_PRIVATE;
	}
}
