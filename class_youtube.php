<?php

/*********************************************************
 *
 *  	@project   YouTube URL Parser
 *		@author    Tyler Bailey
 *		@version   1.0
 *
 *		@desc	   This class contains functions for 
 *				   parsing YouTube Video URLS and getting the video information
 *
 *********************************************************/

class YouTubeParser
{
    private $source    = ''; // video source
    private $unique    = FALSE; // unique video?
    private $suggested = FALSE; // display suggested? -- default FALSE
    private $secure    = FALSE; // secure connection? -- default FALSE
    private $privacy   = FALSE; // make private? -- default FALSE
    public $width     = 640; // video width
    public $height    = 360; // video height
	
	// empty __construct() function
    function __construct() {
    }
	
	/*************************************
	 *
	 *	set the object settings by calling the init() function
	 *
	 *	EX: 	$youtube->init('source', 'http://www.youtube.com/watch?v=_i3C2RuKpBw');
	 *			$youtube->init('unique', true);
	 *			$youtube->init('suggested', true);
	 *			$youtube->init('secure', true);
	 *			$youtube->init('privacy', true);
	 *			$youtube->init('width', 720);
	 *			$youtube->init('height', 480);
	 *
	 *************************************/
    function init($key,$val) {
        return $this->$key = $val;
    }
	
	/*************************************
	 *
	 *	parse the supplied URL and return 
	 *	the video embed HTML & the video images
	 *
	 *	EX: 	$videoArray = $youtube->getInfo();
	 *			$videoThumb = $videoArray['0']['thumb'];
	 *			$videoEmbed = $videoArray['0']['embed'];
	 *
	 *************************************/	
    function getInfo() {
		// set the return array
        $return = array();
		// check for secure domain
        $domain = 'http'.($this->secure?'s':'').'://www.youtube'.($this->privacy?'-nocookie':'').'.com';
		// set the video height and size
        $size   = 'width="'.$this->width.'" height="'.$this->height.'"';
		
		// match the provided source URL to the regular expression
        preg_match_all('/(youtu.be\/|\/watch\?v=|\/embed\/)([a-z0-9\-_]+)/i',$this->source,$matches);
		
		// if the source URL matches the regular expression
        if(isset($matches[2])) {
		
			// if the unique setting is set to true
            if($this->unique) {
                $matches[2] = array_values(array_unique($matches[2])); // remove all duplicates
            }
			
			// foreach source URL that matched the regular expression
            foreach($matches[2] as $key=>$id) {
			
				// get the video ID
                $return[$key]['id']       = $id;
				
				// return the embed HTML
                $return[$key]['embed']    = '<iframe '.$size.' src="'.$domain.'/embed/'.$id.($this->suggested?'':'?rel=0').'" frameborder="0" allowfullscreen></iframe>'; // 2011-2013 embed HTML code
				
                $return[$key]['embedold'] = '<object '.$size.'>
                <param name="movie" value="'.$domain.'/v/'.$id.'?version=3'.($this->suggested?'':'&amp;rel=0').'"></param>
                <param name="allowFullScreen" value="true"></param>
                <param name="allowscriptaccess" value="always"></param>
                <embed src="'.$domain.'/v/'.$id.'?version=3'.($this->suggested?'':'&amp;rel=0').'" type="application/x-shockwave-flash" '.$size.' allowscriptaccess="always" allowfullscreen="true"></embed>
                </object>'; // 2006-2011 embed HTML code (for older systems/browsers)
				
				// return the video thumbnails supplied by YouTube
                $return[$key]['thumb']    = 'http://i4.ytimg.com/vi/'.$id.'/default.jpg'; // standard definition img
                $return[$key]['hqthumb']  = 'http://i4.ytimg.com/vi/'.$id.'/hqdefault.jpg'; // high definition img
            }
        }
        return $return;
    }
}

?>
