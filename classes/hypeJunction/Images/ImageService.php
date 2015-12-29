<?php

namespace hypeJunction\Images;

use Elgg\Http\Request;
use ElggEntity;
use ElggFile;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WideImage\Exception\Exception;

/**
 * Image service
 * @access private
 */
class ImageService {

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var ImagineInterface
	 */
	private $imagine;

	/**
	 * Constructor
	 * @param \hypeJunction\Images\ImagineInterface $imagine
	 */
	public function __construct(Request $request, ImagineInterface $imagine) {
		$this->request = $request;
		$this->imagine = $imagine;
	}

	/**
	 * Write uploaded file to a file object
	 * If no $file is provided, a new object of subtype 'file' will be created
	 * 
	 * @param string   $input_name Input name
	 * @param ElggFile $file       Optional file object to write to
	 * @return ElggFile|false
	 */
	public function createFromUpload($input_name, ElggFile $file = null) {

		$upload = $this->request->files->get($input_name);
		/* @var $upload UploadedFile */
		if (!$upload || !$upload->isValid() || !preg_match('~^image/(jpeg|gif|png)~', $upload->getClientMimeType())) {
			return false;
		}

		if (!isset($file)) {
			$file = new ElggFile();
			$file->subtype = 'file';
			$file->owner_guid = elgg_get_logged_in_user_guid();
		}

		if (!$file instanceof ElggFile || !$file->owner_guid) {
			// files need an owner to load a filestore
			return false;
		}

		if ($file->guid && $file->exists()) {
			// remove file written to the filestore previously
			unlink($file->getFilenameOnFilestore());
		}

		$originalfilename = $upload->getClientOriginalName();
		$basename = elgg_strtolower(time() . $originalfilename);
		$directory = $this->getDirectory($file);
		$filename = $this->getFilename($file, $basename);

		$file->setFilename("$directory/$filename");

		$file->open('write');
		$file->close();
		move_uploaded_file($upload->getPathname(), $file->getFilenameOnFilestore());

		$file->mimetype = ElggFile::detectMimeType($upload->getPathname(), $upload->getClientMimeType());
		$file->simpletype = 'image';
		$file->originafilename = $originalfilename;
		if (!isset($file->title)) {
			$file->title = $file->originalfilename;
		}

		if (!$file->exists() || !$file->save()) {
			// faled to write the file
			$file->delete();
			return false;
		}

		return false;
	}

	/**
	 * Write a file resource into a file object
	 * If no $file is provided, a new object of subtype 'file' will be created
	 *
	 * @param string   $path Full path to file
	 * @param ElggFile $file Optional file object to write to
	 * @return ElggFile|false
	 */
	public function createFromResource($path, ElggFile $file = null) {

		$contents = @file_get_contents($path);
		if (empty($contents)) {
			return;
		}

		if (!isset($file)) {
			$file = new ElggFile();
			$file->subtype = 'file';
			$file->owner_guid = elgg_get_logged_in_user_guid();
		}

		if (!$file instanceof ElggFile || !$file->owner_guid) {
			// files need an owner to load a filestore
			return false;
		}

		if ($file->guid && $file->exists()) {
			// remove file written to the filestore previously
			unlink($file->getFilenameOnFilestore());
		}

		if (filter_var($path, FILTER_VALIDATE_URL)) {
			$path = parse_url($path, PHP_URL_PATH);
		}

		$originalfilename = pathinfo($path, PATHINFO_BASENAME);
		$basename = elgg_strtolower(time() . $originalfilename);
		$directory = $this->getDirectory($file);
		$filename = $this->getFilename($file, $basename);

		$file->setFilename("$directory/$filename");

		$file->open('write');
		$file->write($contents);
		$file->close();

		$file->mimetype = $file->detectMimeType();
		$file->simpletype = 'image';
		$file->originafilename = $originalfilename;
		if (!isset($file->title)) {
			$file->title = $file->originalfilename;
		}

		if (!$this->isImage($file) || !$file->exists() || !$file->save()) {
			// written file is not an image or write failed
			$file->delete();
			return false;
		}

		return $file;
	}

	/**
	 * Get file directory name
	 *
	 * @param ElggFile $file File entity
	 * @return string
	 */
	public function getDirectory(ElggFile $file) {
		$default = 'file';
		$params = [
			'entity' => $file,
		];
		$directory = elgg_trigger_plugin_hook('directory', 'object', $params, $default);
		return trim($directory, '/');
	}

	/**
	 * Get file flename
	 *
	 * @param ElggFile $file     File entity
	 * @param string   $basename Default filename
	 * @return string
	 */
	public function getFilename(ElggFile $file, $basename = '') {

		$filestorename = $file->getFilename();
		if ($filestorename) {
			$basename = pathinfo($filestorename, PATHINFO_BASENAME);
		}

		$params = [
			'entity' => $file,
		];

		return elgg_trigger_plugin_hook('thumb:filename', 'object', $params, $basename);
	}

	/**
	 * Check if an entity is an image, and if this plugin is allowed to treat it as one
	 *
	 * @param ElggFile $entity File entity
	 * @return bool
	 */
	public function isImage($entity = null) {

		if (!$entity instanceof ElggFile) {
			return false;
		}

		$mimetype = $entity->mimetype ? : $entity->detectMimeType(null, 'application/otcet-stream');
		if (preg_match('~^image/(jpeg|gif|png)~', $mimetype)) {
			// Imagine doesn't support other image types
			return true;
		}

		return false;
	}

	/**
	 * Retrieve a thumbnail image of an image file
	 *
	 * @param ElggEntity $entity Entity
	 * @param string     $size   Thumb size
	 * @return boolean|\Thumb
	 */
	public function getThumb(ElggEntity $entity, $size = 'medium') {

		if (!$this->isImage($entity)) {
			return false;
		}

		if (!array_key_exists($size, $this->getThumbSizes($entity))) {
			return false;
		}

		$directory = $this->getThumbDirectory($entity);
		$filename = $this->getThumbFilename($entity, $size);

		$thumb = new Thumb();
		$thumb->owner_guid = $entity->icon_owner_guid ? : $entity->owner_guid;
		$thumb->setFilename("$directory/$filename");
		if (!$thumb->exists()) {
			return false;
		}

		return $thumb;
	}

	/**
	 * Get thumbs sizes config
	 *
	 * @param ElggEntity $entity Entity
	 * @return array
	 */
	public function getThumbSizes(ElggEntity $entity) {
		$defaults = (array) elgg_get_config('icon_sizes');
		$params = [
			'entity' => $entity,
		];
		return elgg_trigger_plugin_hook('thumb:sizes', $entity->getType(), $params, $defaults);
	}

	/**
	 * Get thumb directory name
	 *
	 * @param ElggEntity $entity Entity
	 * @return string
	 */
	public function getThumbDirectory(ElggEntity $entity) {
		$default = 'icons';
		$params = [
			'entity' => $entity,
		];
		$directory = elgg_trigger_plugin_hook('thumb:directory', 'object', $params, $default);
		return trim($directory, '/');
	}

	/**
	 * Get thumb flename
	 *
	 * @param ElggEntity $entity Entity
	 * @param string     $size   Thumb size
	 * @return string
	 */
	public function getThumbFilename(ElggEntity $entity, $size = 'medium') {
		$mimetype = $entity->detectMimeType(null, $entity->mimetype);
		switch ($mimetype) {
			default :
				$ext = 'jpg';
				break;
			case 'image/png' :
				$ext = 'png';
				break;
			case 'image/gif' :
				$ext = 'gif';
				break;
		}

		$default = "{$entity->guid}/{$size}.{$ext}";
		$params = [
			'entity' => $entity,
			'size' => $size,
			'extension' => $ext,
		];

		return elgg_trigger_plugin_hook('thumb:filename', 'object', $params, $default);
	}

	/**
	 * Crop source image
	 *
	 * @param ElggEntity $entity  Entity
	 * @param int        $x1 Upper left crooping coordinate
	 * @param int        $y1 Upper left crooping coordinate
	 * @param int        $x2 Lower right cropping coordinate
	 * @param int        $y2 Lower right cropping coordinate
	 * @return bool
	 */
	public function crop(ElggEntity $entity, $x1, $y1, $x2, $y2) {
		if (!$this->isImage($entity)) {
			return false;
		}

		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;

		if ($crop_width <= 0 && $crop_height <= 0) {
			return false;
		}

		$params = [
			'entity' => $entity,
			'thumb' => $entity,
		];
		$options = elgg_trigger_plugin_hook('options', 'imagine', $params, []);
		try {
			ini_set('memory_limit', '256M');

			$image = $this->imagine->open($entity->getFilenameOnFilestore());
			$image = $image->crop(new Point($x1, $y1), new Box($crop_width, $crop_height));
			$image->save($entity->getFilenameOnFilestore(), $options);
			return true;
		} catch (Exception $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
		}

		return false;
	}

	/**
	 * Create image thumbnails
	 * If coordinates are not set, $entity metadata will be used
	 *
	 * @param ElggEntity $entity  Entity
	 * @param int        $x1 Upper left crooping coordinate
	 * @param int        $y1 Upper left crooping coordinate
	 * @param int        $x2 Lower right cropping coordinate
	 * @param int        $y2 Lower right cropping coordinate
	 * @return Thumb[]|false
	 */
	public function createThumbs(ElggEntity $entity, $x1 = null, $y1 = null, $x2 = null, $y2 = null) {

		if (!$this->isImage($entity)) {
			return false;
		}

		$x1 = isset($x1) ? (int) $x1 : (int) $entity->x1;
		$y1 = isset($y1) ? (int) $y1 : (int) $entity->y1;
		$x2 = isset($x2) ? (int) $x2 : (int) $entity->x2;
		$y2 = isset($y2) ? (int) $y2 : (int) $entity->y2;

		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;

		$error = false;
		$thumbs = [];

		$sizes = $this->getThumbSizes($entity);
		foreach ($sizes as $size => $opts) {

			$width = elgg_extract('w', $opts);
			$height = elgg_extract('h', $opts);
			$square = elgg_extract('square', $opts);
			$croppable = elgg_extract('croppable', $opts, $square);
			$mode = elgg_extract('mode', $opts);

			$directory = $this->getThumbDirectory($entity);
			$filename = $this->getThumbFilename($entity, $size);

			$thumb = new Thumb();
			$thumb->owner_guid = $entity->owner_guid;
			$thumb->setFilename("$directory/$filename");
			if (!$thumb->exists()) {
				$thumb->open('write');
				$thumb->close();
			}

			$thumbs[] = $thumb;

			$params = [
				'entity' => $entity,
				'thumb' => $thumb,
			];
			$options = elgg_trigger_plugin_hook('options', 'imagine', $params, []);
			try {

				ini_set('memory_limit', '256M');

				if ($mode != 'outbound' && $mode != 'inset') {
					$mode = ($square) ? 'outbound' : 'inset';
				}

				$box = new Box($width, $height);
				$image = $this->imagine->open($entity->getFilenameOnFilestore());
				if ($croppable && $crop_width > 0 && $crop_height > 0) {
					$image = $image->crop(new Point($x1, $y1), new Box($crop_width, $crop_height));
				}
				$image = $image->thumbnail($box, $mode);
				$image->save($thumb->getFilenameOnFilestore(), $options);
				unset($image);
			} catch (Exception $ex) {
				elgg_log($ex->getMessage(), 'ERROR');
				$error = true;
			}
		}

		if ($error) {
			foreach ($thumbs as $thumb) {
				$thumb->delete();
			}
			return false;
		}

		$entity->icon_owner_guid = $entity->owner_guid;
		return $thumbs;
	}

	/**
	 * Remove file thumbs
	 *
	 * @param ElggEntity $entity Image file entity
	 * @return void
	 */
	public function clearThumbs(ElggEntity $entity) {

		if (!$this->isImage($entity)) {
			return;
		}

		$sizes = $this->getThumbSizes($entity);
		foreach ($sizes as $size => $opts) {
			$thumb = $this->getThumb($entity, $size);
			if ($thumb) {
				$thumb->delete();
			}
		}
		unset($entity->icontime);
		unset($entity->icon_owner_guid);
		touch($entity->getFilenameOnFilestore());
	}

}
