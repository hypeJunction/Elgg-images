<?php

namespace hypeJunction\Images;

use Elgg\Http\Request;
use ElggEntity;
use ElggFile;
<<<<<<< master
use ElggUser;
use Exception;
=======
>>>>>>> migrate/elgg-7.x
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\File\UploadedFile;
<<<<<<< master
=======
use Exception;
>>>>>>> migrate/elgg-7.x

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
<<<<<<< master
	 * @param ImagineInterface $imagine
=======
	 *
	 * @param Request          $request HTTP request
	 * @param ImagineInterface $imagine Imagine service
>>>>>>> migrate/elgg-7.x
	 */
	public function __construct(Request $request, ImagineInterface $imagine) {
		$this->request = $request;
		$this->imagine = $imagine;
	}

	/**
	 * Write uploaded file to a file object
	 * If no $file is provided, a new object of subtype 'file' will be created
<<<<<<< master
	 * 
=======
	 *
>>>>>>> migrate/elgg-7.x
	 * @param string   $input_name Input name
	 * @param ElggFile $file       Optional file object to write to
	 * @return ElggFile|false
	 */
	public function createFromUpload($input_name, ElggFile $file = null) {
<<<<<<< master

		$upload = $this->request->files->get($input_name);

		if (!$upload instanceof UploadedFile) {
			return $file ? : false;
=======
		$upload = $this->request->files->get($input_name);
		if (!$upload instanceof UploadedFile) {
			return $file ?: false;
>>>>>>> migrate/elgg-7.x
		}

		if (!$upload->isValid() || !preg_match('~^image/(jpeg|gif|png)~', $upload->getClientMimeType())) {
			return false;
		}

		if (!isset($file)) {
			$file = new ElggFile();
<<<<<<< master
			$file->subtype = 'file';
=======
			$file->setSubtype('file');
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master

		$file->setFilename("$directory/$filename");

		$file->open('write');
		$file->close();
		move_uploaded_file($upload->getPathname(), $file->getFilenameOnFilestore());

		$file->mimetype = ElggFile::detectMimeType($upload->getPathname(), $upload->getClientMimeType());
=======
		$file->setFilename("{$directory}/{$filename}");

		// Elgg 5.x removed ElggFile::detectMimeType — use the OS-level
		// detector and fall back to the client-supplied mimetype.
		$file->mimetype = @mime_content_type($upload->getPathname()) ?: $upload->getClientMimeType();
>>>>>>> migrate/elgg-7.x
		$file->simpletype = 'image';
		$file->originalfilename = $originalfilename;
		if (!isset($file->title)) {
			$file->title = $file->originalfilename;
		}

<<<<<<< master
		if (!$file->exists() || !$file->save()) {
			// faled to write the file
=======
		// Save FIRST so the file gets its GUID. In Elgg 5.x, DiskFilestore uses
		// the entity GUID (not owner_guid) to compute paths for `file`-subtype
		// entities, so writing before save would land bytes at a pre-save path
		// that no longer exists once the GUID is assigned.
		if (!$file->save()) {
			return false;
		}

		$file->open('write');
		$file->close();
		if (!move_uploaded_file($upload->getPathname(), $file->getFilenameOnFilestore())) {
			$file->delete();
			return false;
		}

		if (!$file->exists()) {
>>>>>>> migrate/elgg-7.x
			$file->delete();
			return false;
		}

		return $file;
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
<<<<<<< master

=======
>>>>>>> migrate/elgg-7.x
		$contents = @file_get_contents($path);
		if (empty($contents)) {
			return;
		}

		if (!isset($file)) {
			$file = new ElggFile();
<<<<<<< master
			$file->subtype = 'file';
=======
			$file->setSubtype('file');
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master

		$file->setFilename("$directory/$filename");

		$file->open('write');
		$file->write($contents);
		$file->close();

		$file->mimetype = $file->detectMimeType();
=======
		$file->setFilename("{$directory}/{$filename}");

		// detect mime from the source file before write so we can short-circuit
		// non-image inputs without touching the filestore at all
		$file->mimetype = @mime_content_type($path) ?: 'application/octet-stream';
>>>>>>> migrate/elgg-7.x
		$file->simpletype = 'image';
		$file->originalfilename = $originalfilename;
		if (!isset($file->title)) {
			$file->title = $file->originalfilename;
		}

<<<<<<< master
		if (!$this->isImage($file) || !$file->exists() || !$file->save()) {
			// written file is not an image or write failed
=======
		if (!$this->isImage($file)) {
			return false;
		}

		// Save FIRST so the file gets its GUID. In Elgg 5.x, DiskFilestore uses
		// the entity GUID (not owner_guid) to compute paths for `file`-subtype
		// entities, so writing before save would land bytes at a pre-save path
		// that no longer exists once the GUID is assigned.
		if (!$file->save()) {
			return false;
		}

		$file->open('write');
		$file->write($contents);
		$file->close();

		if (!$file->exists()) {
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master
	protected function getDirectory(ElggFile $file) {
		$default = 'file';
		$params = [
			'entity' => $file,
		];
		$directory = elgg_trigger_plugin_hook('directory', 'object', $params, $default);
=======
	public function getDirectory(ElggFile $file) {
		$default = 'file';
		$params = ['entity' => $file];
		$directory = elgg_trigger_event_results('directory', 'object', $params, $default);
>>>>>>> migrate/elgg-7.x
		return trim($directory, '/');
	}

	/**
	 * Get file flename
	 *
	 * @param ElggFile $file     File entity
	 * @param string   $basename Default filename
	 * @return string
	 */
<<<<<<< master
	protected function getFilename(ElggFile $file, $basename = '') {

=======
	public function getFilename(ElggFile $file, $basename = '') {
>>>>>>> migrate/elgg-7.x
		$filestorename = $file->getFilename();
		if ($filestorename) {
			$basename = pathinfo($filestorename, PATHINFO_BASENAME);
		}

<<<<<<< master
		$params = [
			'entity' => $file,
		];

		return elgg_trigger_plugin_hook('filename', 'object', $params, $basename);
=======
		$params = ['entity' => $file];
		return elgg_trigger_event_results('thumb:filename', 'object', $params, $basename);
>>>>>>> migrate/elgg-7.x
	}

	/**
	 * Check if an entity is an image, and if this plugin is allowed to treat it as one
	 *
	 * @param ElggFile $entity File entity
	 * @return bool
	 */
	public function isImage($entity = null) {
<<<<<<< master

=======
>>>>>>> migrate/elgg-7.x
		if (!$entity instanceof ElggFile) {
			return false;
		}

<<<<<<< master
		$ext = pathinfo($entity->getFilenameOnFilestore(), PATHINFO_EXTENSION);
		if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
			return true;
		}
		
		$mimetype = $entity->mimetype ? : $entity->detectMimeType(null, 'application/otcet-stream');
		if (preg_match('~^image/(jpeg|gif|png)~', $mimetype)) {
=======
		// detectMimeType() was removed in Elgg 4.x. Fall back to mime_content_type()
		// only when the file actually exists on the filestore.
		$mimetype = $entity->mimetype;
		if (empty($mimetype) && $entity->exists()) {
			$path = $entity->getFilenameOnFilestore();
			$mimetype = @mime_content_type($path) ?: 'application/octet-stream';
		}

		if (!empty($mimetype) && preg_match('~^image/(jpeg|gif|png)~', $mimetype)) {
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master
	 * @return boolean|Thumb
	 */
	public function getThumb(ElggEntity $entity, $size = 'medium') {

=======
	 * @return boolean|\Thumb
	 */
	public function getThumb(ElggEntity $entity, $size = 'medium') {
>>>>>>> migrate/elgg-7.x
		if (!$this->isImage($entity)) {
			return false;
		}

<<<<<<< master
		$sizes = $this->getThumbSizes($entity);
		if (!array_key_exists($size, $sizes)) {
			return false;
		}

		if (isset($sizes[$size]['metadata_name'])) {
			$md_name = $sizes[$size]['metadata_name'];
			$filestorename = $entity->$md_name;
		} else {
			$directory = $this->getThumbDirectory($entity);
			$filename = $this->getThumbFilename($entity, $size);
			$filestorename = "$directory/$filename";
		}

		$thumb = new Thumb();
		$thumb->owner_guid = $entity->icon_owner_guid ? : $entity->owner_guid;
		$thumb->setFilename($filestorename);

=======
		if (!array_key_exists($size, $this->getThumbSizes($entity))) {
			return false;
		}

		$directory = $this->getThumbDirectory($entity);
		$filename = $this->getThumbFilename($entity, $size);
		$thumb = new Thumb();
		$thumb->owner_guid = $entity->icon_owner_guid ?: $entity->owner_guid;
		$thumb->setFilename("{$directory}/{$filename}");
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master
		$params = [
			'entity' => $entity,
		];
		return elgg_trigger_plugin_hook('thumb:sizes', $entity->getType(), $params, $defaults);
=======
		$params = ['entity' => $entity];
		return elgg_trigger_event_results('thumb:sizes', $entity->getType(), $params, $defaults);
>>>>>>> migrate/elgg-7.x
	}

	/**
	 * Get thumb directory name
	 *
	 * @param ElggEntity $entity Entity
	 * @return string
	 */
<<<<<<< master
	protected function getThumbDirectory(ElggEntity $entity) {
		$default = 'icons';
		$params = [
			'entity' => $entity,
		];
		$directory = elgg_trigger_plugin_hook('thumb:directory', 'object', $params, $default);
=======
	public function getThumbDirectory(ElggEntity $entity) {
		$default = 'icons';
		$params = ['entity' => $entity];
		$directory = elgg_trigger_event_results('thumb:directory', 'object', $params, $default);
>>>>>>> migrate/elgg-7.x
		return trim($directory, '/');
	}

	/**
	 * Get thumb flename
	 *
	 * @param ElggEntity $entity Entity
	 * @param string     $size   Thumb size
	 * @return string
	 */
<<<<<<< master
	protected function getThumbFilename(ElggEntity $entity, $size = 'medium') {
		if ($entity instanceof ElggFile) {
			$mimetype = $entity->detectMimeType(null, $entity->mimetype);
		} else {
			$mimetype = 'image/jpeg';
		}
		switch ($mimetype) {
			default :
				$ext = 'jpg';
				break;
			case 'image/png' :
				$ext = 'png';
				break;
			case 'image/gif' :
=======
	public function getThumbFilename(ElggEntity $entity, $size = 'medium') {
		// detectMimeType() was removed in Elgg 4.x — fall back to the entity's
		// stored mimetype, then to mime_content_type() if the file exists on the
		// filestore.
		$mimetype = $entity->mimetype;
		if (empty($mimetype) && $entity instanceof ElggFile && $entity->exists()) {
			$mimetype = @mime_content_type($entity->getFilenameOnFilestore()) ?: 'application/octet-stream';
		}

		switch ($mimetype) {
			default:
				$ext = 'jpg';
				break;
			case 'image/png':
				$ext = 'png';
				break;
			case 'image/gif':
>>>>>>> migrate/elgg-7.x
				$ext = 'gif';
				break;
		}

		$default = "{$entity->guid}/{$size}.{$ext}";
<<<<<<< master
		$params = [
			'entity' => $entity,
			'size' => $size,
			'extension' => $ext,
		];

		return elgg_trigger_plugin_hook('thumb:filename', 'object', $params, $default);
=======
		$params = ['entity' => $entity, 'size' => $size, 'extension' => $ext];
		return elgg_trigger_event_results('thumb:filename', 'object', $params, $default);
>>>>>>> migrate/elgg-7.x
	}

	/**
	 * Crop source image
	 *
<<<<<<< master
	 * @param ElggEntity $entity  Entity
	 * @param int        $x1 Upper left crooping coordinate
	 * @param int        $y1 Upper left crooping coordinate
	 * @param int        $x2 Lower right cropping coordinate
	 * @param int        $y2 Lower right cropping coordinate
=======
	 * @param ElggEntity $entity Entity
	 * @param int        $x1     Upper left crooping coordinate
	 * @param int        $y1     Upper left crooping coordinate
	 * @param int        $x2     Lower right cropping coordinate
	 * @param int        $y2     Lower right cropping coordinate
>>>>>>> migrate/elgg-7.x
	 * @return bool
	 */
	public function crop(ElggEntity $entity, $x1, $y1, $x2, $y2) {
		if (!$this->isImage($entity)) {
			return false;
		}

		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;
<<<<<<< master

=======
>>>>>>> migrate/elgg-7.x
		if ($crop_width <= 0 && $crop_height <= 0) {
			return false;
		}

<<<<<<< master
		$params = [
			'entity' => $entity,
			'thumb' => $entity,
		];
		$options = elgg_trigger_plugin_hook('options', 'imagine', $params, []);
		try {
			ini_set('memory_limit', '256M');

=======
		$params = ['entity' => $entity, 'thumb' => $entity];
		$options = elgg_trigger_event_results('options', 'imagine', $params, []);
		try {
			ini_set('memory_limit', '256M');
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master
	 * @param ElggEntity $entity  Entity
	 * @param int        $x1 Upper left crooping coordinate
	 * @param int        $y1 Upper left crooping coordinate
	 * @param int        $x2 Lower right cropping coordinate
	 * @param int        $y2 Lower right cropping coordinate
	 * @return Thumb[]|false
	 */
	public function createThumbs(ElggEntity $entity, $x1 = null, $y1 = null, $x2 = null, $y2 = null) {

=======
	 * @param ElggEntity $entity Entity
	 * @param int        $x1     Upper left crooping coordinate
	 * @param int        $y1     Upper left crooping coordinate
	 * @param int        $x2     Lower right cropping coordinate
	 * @param int        $y2     Lower right cropping coordinate
	 * @return Thumb[]|false
	 */
	public function createThumbs(ElggEntity $entity, $x1 = null, $y1 = null, $x2 = null, $y2 = null) {
>>>>>>> migrate/elgg-7.x
		if (!$this->isImage($entity)) {
			return false;
		}

<<<<<<< master
		$this->clearThumbs($entity);
		
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

=======
		$coords = $entity->getIconCoordinates();
		$x1 = isset($x1) ? (int) $x1 : (int) elgg_extract('x1', $coords, 0);
		$y1 = isset($y1) ? (int) $y1 : (int) elgg_extract('y1', $coords, 0);
		$x2 = isset($x2) ? (int) $x2 : (int) elgg_extract('x2', $coords, 0);
		$y2 = isset($y2) ? (int) $y2 : (int) elgg_extract('y2', $coords, 0);
		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;
		$error = false;
		$thumbs = [];
		$sizes = $this->getThumbSizes($entity);
		foreach ($sizes as $size => $opts) {
>>>>>>> migrate/elgg-7.x
			$width = elgg_extract('w', $opts);
			$height = elgg_extract('h', $opts);
			$square = elgg_extract('square', $opts);
			$croppable = elgg_extract('croppable', $opts, $square);
			$mode = elgg_extract('mode', $opts);
<<<<<<< master
			$metadata_name = elgg_extract('metadata_name', $opts);

			if ($metadata_name && $entity->$metadata_name) {
				$filestorename = $entity->$metadata_name;
			} else {
				$directory = $this->getThumbDirectory($entity);
				$filename = $this->getThumbFilename($entity, $size);
				$filestorename = "$directory/$filename";
			}

			$thumb = new Thumb();
			$thumb->owner_guid = $entity->owner_guid;
			$thumb->setFilename($filestorename);
=======
			$directory = $this->getThumbDirectory($entity);
			$filename = $this->getThumbFilename($entity, $size);
			$thumb = new Thumb();
			$thumb->owner_guid = $entity->owner_guid;
			$thumb->setFilename("{$directory}/{$filename}");
>>>>>>> migrate/elgg-7.x
			if (!$thumb->exists()) {
				$thumb->open('write');
				$thumb->close();
			}

			$thumbs[] = $thumb;
<<<<<<< master

			$params = [
				'entity' => $entity,
				'thumb' => $thumb,
			];
			$options = elgg_trigger_plugin_hook('options', 'imagine', $params, []);
			try {

				ini_set('memory_limit', '256M');

				if ($mode != 'outbound' && $mode != 'inset') {
					$mode = ($square) ? 'outbound' : 'inset';
=======
			$params = ['entity' => $entity, 'thumb' => $thumb];
			$options = elgg_trigger_event_results('options', 'imagine', $params, []);
			try {
				ini_set('memory_limit', '256M');
				if ($mode != 'outbound' && $mode != 'inset') {
					$mode = $square ? 'outbound' : 'inset';
>>>>>>> migrate/elgg-7.x
				}

				$box = new Box($width, $height);
				$image = $this->imagine->open($entity->getFilenameOnFilestore());
				if ($croppable && $crop_width > 0 && $crop_height > 0) {
					$image = $image->crop(new Point($x1, $y1), new Box($crop_width, $crop_height));
				}
<<<<<<< master
				$image = $image->thumbnail($box, $mode);
				$image->save($thumb->getFilenameOnFilestore(), $options);
				unset($image);

				if (!empty($opts['metadata_name'])) {
					$md_name = $opts['metadata_name'];
					$entity->$md_name = $thumb->getFilename();
				}
=======

				$image = $image->thumbnail($box, $mode);
				$image->save($thumb->getFilenameOnFilestore(), $options);
				unset($image);
>>>>>>> migrate/elgg-7.x
			} catch (Exception $ex) {
				elgg_log($ex->getMessage(), 'ERROR');
				$error = true;
			}
		}

		if ($error) {
			foreach ($thumbs as $thumb) {
				$thumb->delete();
			}
<<<<<<< master
=======

>>>>>>> migrate/elgg-7.x
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
<<<<<<< master

=======
>>>>>>> migrate/elgg-7.x
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
<<<<<<< master
		unset($entity->icontime);
		unset($entity->icon_owner_guid);
		touch($entity->getFilenameOnFilestore());
	}

	/**
	 * Create an avatar object from an upload
	 *
	 * @param ElggEntity $entity     Entity to which avatar will belong
	 * @param string     $input_name Input name
	 * @return Avatar|false
	 */
	function createAvatarFromUpload(ElggEntity $entity, $input_name = 'avatar') {

		$avatars = $this->getAvatars($entity);

		$avatar = new Avatar();
		$avatar->owner_guid = $entity instanceof ElggUser ? $entity->guid : $entity->owner_guid;
		$avatar->container_guid = $entity->guid;
		$avatar->access_id = $entity->access_id;
		$avatar->setFilename("avatars/$entity->guid/" . time() . $_FILES[$input_name]['name']);

		$avatar = $this->createFromUpload($input_name, $avatar);

		if ($avatar && $avatar->save()) {
			if ($avatars) {
				// clear old avatars
				foreach ($avatars as $a) {
					$a->delete();
				}
			}
			$entity->avatar_last_modified = $avatar->time_created;
		}

		return $avatar;
	}

	/**
	 * Create an avatar from a file resource
	 *
	 * @param ElggEntity $entity Entity to which avatar will belong
	 * @param type       $path   Path to file
	 * @return Avatar|false
	 */
	function createAvatarFromResource(ElggEntity $entity, $path) {

		$avatars = $this->getAvatars($entity);

		$basename = pathinfo($path, PATHINFO_BASENAME);

		$avatar = new Avatar();
		$avatar->owner_guid = $entity instanceof ElggUser ? $entity->guid : $entity->owner_guid;
		$avatar->container_guid = $entity->guid;
		$avatar->access_id = $entity->access_id;
		$avatar->setFilename("avatars/$entity->guid/" . time() . $basename);

		$avatar = $this->createFromResource($path, $avatar);

		if ($avatar && $avatar->save()) {
			if ($avatars) {
				// clear old avatars
				foreach ($avatars as $a) {
					$a->delete();
				}
			}
			$entity->avatar_last_modified = $avatar->time_created;
		}

		return $avatar;
	}

	/**
	 * Clear all entity avatars
	 *
	 * @param ElggEntity $entity Entity
	 * @return void
	 */
	function clearAvatars(ElggEntity $entity) {
		$avatars = $this->getAvatars($entity);

		if ($avatars) {
			foreach ($avatars as $avatar) {
				$avatar->delete();
			}
		}

		unset($entity->avatar_last_modified);
	}

	/**
	 * Returns entity avatar
	 *
	 * @param ElggEntity $entity Entity
	 * @return Avatar|false
	 */
	public function getAvatar(ElggEntity $entity) {

		if (!$entity->avatar_last_modified) {
			return false;
		}

		$avatars = elgg_get_entities([
			'types' => 'object',
			'subtypes' => Avatar::SUBTYPE,
			'container_guids' => (int) $entity->guid,
			'limit' => 1,
		]);

		return !empty($avatars) ? $avatars[0] : false;
	}

	/**
	 * Returns all entity avatars
	 *
	 * @param ElggEntity $entity Entity
	 * @return Avatar[]|false
	 */
	public function getAvatars(ElggEntity $entity) {

		return elgg_get_entities([
			'types' => 'object',
			'subtypes' => Avatar::SUBTYPE,
			'container_guids' => (int) $entity->guid,
			'limit' => 0,
		]);
	}

=======

		unset($entity->icon_owner_guid);

		// Bust caches by touching the source file. Skip if the file was
		// already removed (e.g. during a delete event firing on an entity
		// whose filestore content has gone).
		if ($entity instanceof ElggFile && $entity->exists()) {
			@touch($entity->getFilenameOnFilestore());
		}
	}
>>>>>>> migrate/elgg-7.x
}
