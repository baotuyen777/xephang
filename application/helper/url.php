<?php
/**
 * Display link for an array actor or tag
 * 
 * @param array $list_arr
 * @param string $url
 * @return string
 */
function makeLinkArray($list_arr,$url){
	if ( count($list_arr) <= 0)
		return '';
	$link_display = '';
	foreach ($list_arr as $key => $name){
		$link_display .= '<a class="link_display" href="' . "$url/$key" . '" title="'. $name .'">' .$name . '</a>' . ' ,';
	}
	
	$link_display = substr($link_display,0,strlen($link_display) - 1);
	return $link_display;
}

function friendlyUrl($title)
{
    $title = preg_replace('/[àáảãạăằắẳẵặâầấẩẫậ]/u', 'a', $title);
    $title = preg_replace('/[ÀÁẢÃẠĂẰẮẲẴẶÂẦẤẨẪẬ]/u', 'A', $title);
    $title = preg_replace('/[òóỏõọôồốổỗộơờớởỡợ]/u', 'o', $title);
    $title = preg_replace('/[ÒÓỎÕỌÔỒỐỔỖỘƠỜỚỞỠỢ]/u', 'O', $title);
    $title = preg_replace('/[èéẻẽẹêềếểễệ]/u', 'e', $title);
    $title = preg_replace('/[ÈÉẺẼẸÊỀẾỂỄỆ]/u', 'e', $title);
    $title = preg_replace('/[ìíỉĩị]/u', 'i', $title);
    $title = preg_replace('/[ÌÍỈĨỊ]/u', 'i', $title);
    $title = preg_replace('/[ỳýỷỹỵ]/u', 'y', $title);
    $title = preg_replace('/[ỲÝỶỸỴ]/u', 'y', $title);
    $title = preg_replace('/[ùúủũụưừứửữự]/u', 'u', $title);
    $title = preg_replace('/[ÙÚỦŨỤƯỪỨỬỮỰ]/u', 'u', $title);
    $title = preg_replace('/[Đ]/u', 'D', $title);
    $title = preg_replace('/[đ]/u', 'd', $title);        
    $title = preg_replace('/[^a-z0-9_]/i', '-', $title);
    $title = preg_replace('/-[-]*/i', '-', $title);      
    if (strlen($title)>100){
        $title = substr($title, 0, 100); 
    }
    $title = preg_replace('/-$/i', '', $title);
    $title = preg_replace('/^-/i', '', $title);
    return $title . ".html";
}


function truncateStr($str, $maxlen = 60){
    if ( strlen($str) <= $maxlen ){
        return $str;     
    }               
    $newstr = substr($str, 0, $maxlen);
    if (substr($newstr, -1, 1) != ' '){
        $newstr = substr($newstr, 0, strrpos($newstr, ' '));    
    } 
    return $newstr;
}


function get_video_secure_tocken($vid, $t)
{
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
		$remote_host = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else 
		$remote_host = $_SERVER["REMOTE_ADDR"];

	$sec="tamtay_video_123456788";
	return md5($t . $sec . $vid . $remote_host);
}

/**
 * Generate a URL from a Drupal menu path. Will also pass-through existing URLs.
 *
 * @param $path
 *   The Drupal path being linked to, such as "admin/content/node", or an existing URL
 *   like "http://drupal.org/".
 * @param $query
 *   A query string to append to the link or URL.
 * @param $fragment
 *   A fragment identifier (named anchor) to append to the link. If an existing
 *   URL with a fragment identifier is used, it will be replaced. Note, do not
 *   include the '#'.
 * @param $absolute
 *   Whether to force the output to be an absolute link (beginning with http:).
 *   Useful for links that will be displayed outside the site, such as in an
 *   RSS feed.
 * @return
 *   a string containing a URL to the given path.
 *
 * When creating links in modules, consider whether l() could be a better
 * alternative than url().
 */
function create_video_url($path = NULL, $query = NULL, $fragment = NULL, $absolute = FALSE) {
  if (isset($fragment)) {
    $fragment = '#'. $fragment;
  }

  // Return an external link if $path contains an allowed absolute URL.
  // Only call the slow filter_xss_bad_protocol if $path contains a ':' before any / ? or #.
  $colonpos = strpos($path, ':');
  if ($colonpos !== FALSE && !preg_match('![/?#]!', substr($path, 0, $colonpos)) && filter_xss_bad_protocol($path, FALSE) == check_plain($path)) {
    // Split off the fragment
    if (strpos($path, '#') !== FALSE) {
      list($path, $old_fragment) = explode('#', $path, 2);
      if (isset($old_fragment) && !isset($fragment)) {
        $fragment = '#'. $old_fragment;
      }
    }
    // Append the query
    if (isset($query)) {
      $path .= (strpos($path, '?') !== FALSE ? '&' : '?') . $query;
    }
    // Reassemble
    return $path . $fragment;
  }

  global $base_url;
  static $script;

  if (!isset($script)) {
    // On some web servers, such as IIS, we can't omit "index.php". So, we
    // generate "index.php?q=foo" instead of "?q=foo" on anything that is not
    // Apache.
    $script = (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === FALSE) ? 'index.php' : '';
  }

  $base = ($absolute ? $base_url . '/' : base_path());

  // The special path '<front>' links to the default front page.
  if (!empty($path) && $path != '<front>') {
    //$path = drupal_get_path_alias($path);
    $path = drupal_urlencode($path);

     if (isset($query)) {
       return $base . $path .'?'. $query . $fragment;
     }
     else {
       return $base . $path . $fragment;
     }
  }
  else {
    if (isset($query)) {
      return $base . $script .'?'. $query . $fragment;
    }
    else {
      return $base . $fragment;
    }
  }
}

function drupal_urlencode($text) {
    return str_replace(array('%2F', '%26', '%23', '//'),
                       array('/', '%2526', '%2523', '/%252F'),
                       urlencode($text));
}

function word_limiter($str, $limit = 100, $end_char = '&#8230;')
  {
    if (trim($str) == '')
    {
      return $str;
    }

    preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

    if (strlen($str) == strlen($matches[0]))
    {
      $end_char = '';
    }

    return rtrim($matches[0]).$end_char;
  }
  function encodeStr($str){
  return htmlspecialchars($str);
}