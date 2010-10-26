<?php
/**
 * Controller for serving photos from the application directory
 * 
 * You can avoid using this method completely if you set a webserver alias to
 * point to the photos directory:
 * 
 * Apache: Alias /photos /path/to/application/photos
 * nginx:  <TODO>
 * 
 * This will result in much better performance.
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Controller_Photo extends Controller
{
	/**
	 * Index method to serve a file
	 *
	 * @return null
	 */
	public function action_index($photo = NULL)
	{
		$photo = new Model_Vendo_Photo($photo);

		if ( ! $photo->id)
		{
			throw new Vendo_404('Photo not found');
		}

		$path_info = pathinfo($photo->path().$photo->filename);
		$ext = $path_info['extension'];

		header('Content-Type: image/'.$path_info['extension']);
		header('Content-Length: '.filesize($photo->path().$photo->filename));

		readfile($photo->path().$photo->filename);
		die;
	}
}