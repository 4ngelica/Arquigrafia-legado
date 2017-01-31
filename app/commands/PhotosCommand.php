<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
class PhotosCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'PhotoRegenerate';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$date = $this->option('date');
		if($date == null)
			$photos = Photo::all();
		else {
			try {
				$date = strtotime($date);				
				$date = date('Y-m-d', $date);				
				$photos = Photo::where('created_at', '>=', $date)->get();
			} catch (Exception $e){
				$this->error($e);
				return;
			}
		}
		$this->info('Verifying ' . count($photos) . ' photos...');
			
		foreach ($photos as $photo) {
			$original_image = '';
			//checks if it's video
			if($photo->type == 'video')
				continue;
			//checks if original files exists
			if(\File::exists('app/storage/original-images/' . $photo->id . '_original.jpg'))  
				$original_image = 'app/storage/original-images/' . $photo->id . '_original.jpg';
			elseif(\File::exists('app/storage/original-images/' . $photo->id . '_original.jpeg'))
				$original_image = 'app/storage/original-images/' . $photo->id . '_original.jpeg';
			elseif(\File::exists('app/storage/original-images/' . $photo->id . '_original.png'))
				$original_image = 'app/storage/original-images/' . $photo->id . '_original.png';
			elseif(\File::exists('app/storage/original-images/' . $photo->id . '_original.gif'))
				$original_image = 'app/storage/original-images/' . $photo->id . '_original.gif';
			else {
				$this->error('Original image with ID ' . $photo->id . ' does not exist.');
				continue;
			}
			
			$sizes = ['200h', 'home', 'micro', 'view'];
			for($i = 0; $i < count($sizes); $i++){
				try {
					$path = public_path().'/arquigrafia-images/'.$photo->id.'_'.$sizes[$i].'.jpg';
					if(is_file($path)) 
						continue;
					else {
						$this->error('Image '.$photo->id .'_'. $sizes[$i]. ' does not exist. Creating...');
						$image = \Image::make($original_image)->encode('jpg', 80);
						if($sizes[$i] == '200h'){
							$image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg');
						} elseif ($sizes[$i] == 'home'){
							$image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
						} elseif ($sizes[$i] == 'micro'){
							$image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
						} else { //view
							$image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
						}
						$this->info('Image ' .$photo->id . '_' .$sizes[$i]. ' created.');
					}
				}
				catch (Exception $e) {
					$this->error('Error when resizing image ' . $photo->id . '_' . $sizes[$i] . ': ' . $e->getMessage());
				}
			}
		}
	}
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('example', InputArgument::OPTIONAL, 'An example argument.'),
		);
	}
	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
			array('date', 'd', InputOption::VALUE_OPTIONAL, 'Defines the date to start regenerating photos (DD-MM-YYYY).', null),
		);
	}
}