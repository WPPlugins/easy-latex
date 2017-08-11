<?php
/*
Plugin Name: Easy WP LaTeX
Plugin URI: http://www.thulasidas.com/latex
Description: Easiest way to show mathematical equations on your blog. Go to <a href="options-general.php?page=easy-latex.php">Settings &rarr; Easy WP LaTeX</a> to set it up, or use the "Settings" link on the right.
Version: 2.00
Author: Manoj Thulasidas
Author URI: http://www.thulasidas.com
*/

/*
Copyright (C) 2008 www.thulasidas.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (!class_exists("ezLaTeX")) {
  class ezLaTeX {
    function ezLaTeX() { //constructor
    }
    function init() {
      $this->getAdminOptions();
    }
    //Returns an array of admin options
    function getAdminOptions() {
      $mThemeName = get_option('stylesheet') ;
      $mOptions = "ezLaTeX" . $mThemeName ;
      $ezLaTeXAdminOptions =
        array(
              'text_color' => 'FFFFFF',
              'bg_color' => '00000',
              'tag' => 'math',
              'size' => '0',
              'cache' => true,
              'links' => true) ;

      $ezTeXOptions = get_option($mOptions);
      if (empty($ezTeXOptions)) {
        // try loading the default from the pre 1.3 version, so as not to annoy
        // the dudes who have already been using ezAdsenser
        $adminOptionsName = "ezLaTeXAdminOptions";
        $ezTeXOptions = get_option($adminOptionsName);
      }
      if (!empty($ezTeXOptions)) {
        foreach ($ezTeXOptions as $key => $option)
          $ezLaTeXAdminOptions[$key] = stripslashes($option);
      }
      update_option($mOptions, $ezLaTeXAdminOptions);
      return $ezLaTeXAdminOptions;
    }
    //Prints out the admin page
    function printAdminPage() {
      $mThemeName = get_option('stylesheet') ;
      $mOptions = "ezLaTeX" . $mThemeName ;
      $ezTeXOptions = $this->getAdminOptions();
      $cache = dirname(__FILE__) . '/cache/';
      $cache_url = get_option('siteurl') . '/' . PLUGINDIR . '/' .
        basename(dirname(__FILE__)) . '/cache/' ;

      if (isset($_POST['update_ezLaTeXSettings'])) {
        if (isset($_POST['ezLaTeX_textColor'])) {
          $ezTeXOptions['text_color'] = $_POST['ezLaTeX_textColor'];
        }
        if (isset($_POST['ezLaTeX_bgColor'])) {
          $ezTeXOptions['bg_color'] =
            $ezTeXOptions['bg_color'] = $_POST['ezLaTeX_bgColor'];
        }
        if (isset($_POST['ezLaTeX_tag'])) {
          $ezTeXOptions['tag'] = $_POST['ezLaTeX_tag'];
        }
        if (isset($_POST['ezLaTeX_size'])) {
          $ezTeXOptions['size'] = $_POST['ezLaTeX_size'];
        }
        $ezTeXOptions['cache'] = isset($_POST['ezLaTeX_cache']);
        $ezTeXOptions['links'] = isset($_POST['ezLaTeX_links']);

        update_option($mOptions, $ezTeXOptions);

?>
<div class="updated"><p><strong>"Settings Updated."</strong></p></div>
<?php
      }

      $caching = $ezTeXOptions['cache'] ;
      $msg = '';
      if ($caching) {
        if (!file_exists($cache)) {
          if (!@mkdir($cache))
            $msg = '<font color="red">The cache doesn\'t exist. Please create ' . $cache_url .
              ' and set its permission to 777. Use a command like:<br /> ' .
              ' $ chmod 777 ' . $cache .'<br /> ' .
              'See <a href="http://codex.wordpress.org/Changing_File_Permissions">' .
              'how to change file permissions at WordPress</a>.</font>' ;
        }
        else {
          if (fileperms($cache) != 16895)
            $msg = '<font color="red">' .
              'I cannot change the premission of the cache ' . $cache_url .
              '.<br /> Please set its permission to 777. Use a command like:<br /> ' .
              ' $ chmod 777 ' . $cache .'<br /> ' .
              'See <a href="http://codex.wordpress.org/Changing_File_Permissions">' .
              'how to change file permissions at WordPress</a>.</font>' ;
        }
      }
      echo '<script type="text/javascript" src="'. get_option('siteurl') . '/' .
        PLUGINDIR . '/' .  basename(dirname(__FILE__)) . '/jscolor/jscolor.js"></script>' ;
      echo '<script type="text/javascript" src="'. get_option('siteurl') . '/' .
        PLUGINDIR . '/' .  basename(dirname(__FILE__)) . '/wz_tooltip.js"></script>' ;
?>

<div class="wrap" style="width:800px">
<h2>Easy WP LaTeX Setup
<a href="http://validator.w3.org/" target="_blank"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" title="Easy AdSense Admin Page is certified Valid XHTML 1.0 Transitional" height="31" width="88" class="alignright"/></a>
</h2>

   <?php if (strlen($msg)>0) echo '<div class="error">' . $msg . '></div>' ; ?>

<table class="form-table">
<tr><th scope="row"><h3>Instructions</h3></th></tr>
<tr valign="top">
<td>
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
Bracket your LaTeX formula with the tags. Example, type in
<code>[math](a+b)^2 = a^2 + b^2 + 2ab[/math]</code> and you will get:
<img src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg=EAF3FA&s=0'); ?>" alt="latex" />
</li>
<li>Use the exclamation mark as the first character to generate a displayed equation (i.e., centered, on its own line): <code>[math]!(a+b)^2[/math]</code>.
</li>
<li>Use the exclamation mark as the last character to suppress formula output: <code>[math](a+b)^2![/math]</code>.
</li>
</ul>
</td>

<?php @include (dirname (__FILE__).'/head-text.php'); ?>

</tr>
</table>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<table class="form-table">
<tr><th scope="row" colspan="2">
<h3>Options (for the <?php echo  $mThemeName; ?> theme)</h3>
</th></tr>
<tr valign="top">

<td width="35%">
<table class="form-table">
<tr><th scope="row" colspan="2" title="Decide the text and background color for your equations to match your theme."><b>Colors</b></th></tr>
<tr>
<td width="10">&nbsp;</td>
<td>
Text Color:
</td>
<td>
<input type="text" style="border:0px solid;" class="color {hash:false,caps:true,pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'black'}" value="<?php echo $ezTeXOptions['text_color']; ?>" name="ezLaTeX_textColor" size="6" />
</td>
</tr>
<tr>
<td width="10">&nbsp;</td>
<td>
Background Color:
</td>
<td>
<input type="text" style="border:0px solid;" class="color {hash:false,caps:true,pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'black'}" value="<?php echo $ezTeXOptions['bg_color']; ?>" name="ezLaTeX_bgColor" size="6" />
</td>
</tr>
</table>

<table class="form-table">
<tr><th scope="row"  colspan="2" title="Decide the tags to bracket your LaTeX code."><b>Bracketting Tags</b></th></tr>
<tr>
<td width="10">&nbsp;</td>
<td>
<label for="ezLaTeX_tag_math">
<input type="radio" id="ezLaTeX_tag_math" name="ezLaTeX_tag" value="math" <?php if ($ezTeXOptions['tag'] == "math") { echo 'checked="checked"'; }?> /> &nbsp; [math] ... [/math]&nbsp; phpBB Style</label><br />
<label for="ezLaTeX_tag_dollar">
<input type="radio" id="ezLaTeX_tag_dollar" name="ezLaTeX_tag" value="latex" <?php if ($ezTeXOptions['tag'] == "latex") { echo 'checked="checked"' ; }?> /> &nbsp; $$ ... $$  &nbsp;&nbsp; LaTeX Style</label><br />
<label for="ezLaTeX_tag_mtype">
<input type="radio" id="ezLaTeX_tag_mtype" name="ezLaTeX_tag" value="mtype" <?php if ($ezTeXOptions['tag'] == "mtype") { echo 'checked="checked"' ; }?> /> &nbsp; \[ ... \] &nbsp;&nbsp; MathType Style</label><br />
</td>
</tr>
</table>

<table class="form-table">
<tr><th scope="row"><b>Other Options</b></th></tr>
<tr>
<td>
<label for="ezLaTeX_cache" title="Easy WP LaTeX can use a disk cache. It makes page serving fast, but requires you to leave the cache directory unprotected."><input type="checkbox" id="ezLaTeX_cache" name="ezLaTeX_cache"  value="false" <?php if ($ezTeXOptions['cache']) { echo('checked="checked"'); }?> /> Enable cache. </label>&nbsp;

<label for="ezLaTeX_links" title="Easy WP LaTeX links the first two formula objects to the author's blog. Use this option to suppress the link backs, if you must."><input type="checkbox" id="ezLaTeX_links" name="ezLaTeX_links" value="false"  <?php if ($ezTeXOptions['links']) { echo('checked="checked"'); }?> /> Kill links to author. </label>&nbsp;
</td>
</tr>
</table>

</td>

<td>

<table class="form-table">
<tr><th scope="row" colspan="2" title="Choose the font size for your equations."><b>LaTeX Equation Font Size</b></th></tr>
<tr>
<td width="15"></td>
<td>
<label for="ezLaTeX_size0">
<input type="radio" id="ezLaTeX_size0" name="ezLaTeX_size" value="0" <?php if ($ezTeXOptions['size'] == "0") { echo 'checked="checked"'; }?> /> &nbsp;&nbsp; Small &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img style="vertical-align:-40%;" alt="latex" src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg=' . $ezTeXOptions['bg_color'] . '&fg=' .$ezTeXOptions['text_color'] . '&s=0') ; ?>" /></label><br />

<br />
<label for="ezLaTeX_size1">
<input type="radio" id="ezLaTeX_size1" name="ezLaTeX_size" value="1" <?php if ($ezTeXOptions['size'] == "1") { echo 'checked="checked"' ; }?> /> &nbsp;&nbsp; Medium &nbsp;&nbsp; <img style="vertical-align:-40%;" alt="latex" src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg' . $ezTeXOptions['bg_color'] . '&fg=' .$ezTeXOptions['text_color'] . '&s=1') ; ?>" /></label><br />

<br />
<label for="ezLaTeX_size2">
<input type="radio" id="ezLaTeX_size2" name="ezLaTeX_size" value="2" <?php if ($ezTeXOptions['size'] == "2") { echo 'checked="checked"' ; }?> /> &nbsp;&nbsp; Large &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img style="vertical-align:-40%;" alt="latex" src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg' .$ezTeXOptions['bg_color'] . '&fg=' .$ezTeXOptions['text_color'] . '&s=2') ; ?>" /></label><br />

<br />
<label for="ezLaTeX_size3">
<input type="radio" id="ezLaTeX_size3" name="ezLaTeX_size" value="3" <?php if ($ezTeXOptions['size'] == "3") { echo 'checked="checked"' ; }?> /> &nbsp;&nbsp; X-Large &nbsp;&nbsp; <img style="vertical-align:-40%;" alt="latex" src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg' .$ezTeXOptions['bg_color'] . '&fg=' .$ezTeXOptions['text_color'] . '&s=3') ; ?>" /></label><br />

<br />
<label for="ezLaTeX_size4">
<input type="radio" id="ezLaTeX_size4" name="ezLaTeX_size" value="4" <?php if ($ezTeXOptions['size'] == "4") { echo 'checked="checked"' ; }?> /> &nbsp;&nbsp; XX-Large &nbsp; <img style="vertical-align:-40%;" alt="latex" src="http://l.wordpress.com/latex.php<?php echo htmlspecialchars('?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&bg' .$ezTeXOptions['bg_color'] . '&fg=' .$ezTeXOptions['text_color'] . '&s=4') ; ?>" /></label><br />

</td>
</tr>
</table>

</td>
</tr>
</table>

<div class="submit">
<input type="submit" name="update_ezLaTeXSettings" value="Save Changes" /></div>
</form>
<br />
<hr />

<?php @include (dirname (__FILE__).'/tail-text.php'); ?>


<table class="form-table" >
<tr><th scope="row"><h3><?php _e('Credits', 'easy-adsenser'); ?></h3></th></tr>
<tr><td>
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<b>Easy WP LaTeX</b> is based on <b>Latex for WordPess</b> by zhiqiang, and shares some features and core engine code with it.
</li>
<li>
<?php printf(__('%s uses the excellent Javascript/DHTML tooltips by %s', 'easy-adsenser'), '<b>Easy WP LaTeX</b>', '<a href="http://www.walterzorn.com" target="_blank" title="Javascript, DTML Tooltips"> Walter Zorn</a>.') ;
?>
</li>
<li>
It also uses the excellent Javascript color picker by <a href="http://jscolor.com" target="_blank" title="Javascript color picker"> JScolor</a>.
</li>
</ul>
</td>
</tr>
</table>

</div>

<?php
   }//End function printAdminPage()

  var $server = "http://l.wordpress.com/latex.php?latex=";
  var $img_format = "png";
  var $ezCount = 0 ;
  // $img_format should be 'gif' when using mimetex service.
  // Other servers: (note that the syntax for color may be different)
  // "http://l.wordpress.com/latex.php?latex=";
  // "http://www.bytea.net/cgi-bin/mimetex.cgi?formdata=";

  function parseTex ($toParse)
  {
    // tag specification (which tags are to be replaced)
    // change it to
    $ezTeXOptions = $this->getAdminOptions();
    $tag = $ezTeXOptions['tag'] ;
    // $regex = '#' . $tag1 . '  *(.*?)' . $tag2 . '#si';
    $regex = '#\[math\] *(.*?)\[/math\]#si';
    if ($tag == 'latex') $regex = '#\$\$(.*?)\$\$#si';
    if ($tag == 'mtype') $regex = '#\\\[(.*?)\\\]#si';
    $this->ezCount = 0;
    return preg_replace_callback($regex, array(&$this, 'createTex'), $toParse);
  }

  function createTex($toTex)
  {
    // clean up <br /> and other junk
    $formula_text = str_replace(array("\r\n", "\n", "\r"), "", $toTex[1]);
    $imgtext=false;

    if (substr($formula_text, -1, 1) == "!")
      return "$$".substr($formula_text, 0, -1)."$$";

    if (substr($formula_text, 0, 1) == "!") {
      $imgtext=true;
      $formula_text=substr($formula_text, 1);
    }

    $ezTeXOptions = $this->getAdminOptions();
    $textColor = $ezTeXOptions['text_color'] ;
    $bgColor = $ezTeXOptions['bg_color'] ;
    $Size = $ezTeXOptions['size'] ;
    $caching = $ezTeXOptions['cache'] ;

    $formula_hash = md5($formula_text . $bgColor . $textColor . $Size);
    $formula_filename = 'tex_'.$formula_hash.'.'.$this->img_format;
    $formula_url =  $this->server.rawurlencode($formula_text) .
      '&bg=' . $bgColor .
      '&fg=' . $textColor .
      '&s=' . $Size ;

    $cache_formula_path = '' ;
    $cache_formula_url = '' ;

    if ($caching) {
      $cache_path = dirname(__FILE__) . '/cache/';
      if (fileperms($cache_path) != 16895) $caching = false ;
      $cache_formula_path = $cache_path . $formula_filename;
      $cache_url = get_option('siteurl') . '/' . PLUGINDIR . '/' .
        basename(dirname(__FILE__)) . '/cache/' ;
      $cache_formula_url = $cache_url . $formula_filename;
    }
    if ($caching && !is_file($cache_formula_path))
    {
      if (!class_exists('Snoopy')) require_once (ABSPATH.'wp-includes/class-snoopy.php');
      $snoopy = new Snoopy;
      $snoopy->fetch($formula_url);
      // this will copy the created tex-image to your cache-folder
      if(strlen($snoopy->results))
      {
        $cache_file = fopen($cache_formula_path, 'w');
        fputs($cache_file, $snoopy->results);
        fclose($cache_file);
      }
    }
    // if the formula image is not in the cache for whatever reason, do live fetch
    if (!is_file($cache_formula_path))
      $cache_formula_url = $formula_url ;

    $unreal =
      array
      ('href="http://wordpress.org/extend/plugins/easy-latex/" target="_blank" title="' .
       $formula_text  . '"',
       'href="http://www.Thulasidas.com/latex" target="_blank" title="' .
       $formula_text . '"') ;

    if ($this->ezCount < 3 && !$ezTeXOptions['links']) {
      $formula_output = '<a ' . $unreal[$this->ezCount] . '><img src="' . $cache_formula_url .
        '" style="vertical-align:-20%;" class="tex" alt="' . $formula_text . '" /></a>' ;
    }
    else {
      $formula_output =  '<img src="' . $cache_formula_url .  '" title="' . $formula_text .
        '" style="vertical-align:-20%;" class="tex" alt="' . $formula_text . '" />' ;
    }
    $this->ezCount++;

    // returning the image-tag, referring to the image in your cache folder
    if($imgtext) return '<center>' . $formula_output . '</center>' ;

    return $formula_output ;
  }

  function plugin_action($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__).'/easy-latex.php')){
      $settings_link = "<a href='options-general.php?page=easy-latex.php'>Settings</a>";
      array_unshift( $links, $settings_link );
    }
    return $links;
  }
 }
} //End Class ezLaTeX

if (class_exists("ezLaTeX")) {
  $ez_TeX = new ezLaTeX();
  if (isset($ez_TeX)) {
    if (!function_exists("ezLaTeX_ap")) {
      function ezLaTeX_ap() {
        global $ez_TeX ;
        if (function_exists('add_options_page')) {
          add_options_page('Easy WP LaTeX', 'Easy WP LaTeX', 9,
                           basename(__FILE__), array(&$ez_TeX, 'printAdminPage'));
        }
      }
    }
    add_filter('the_title', array($ez_TeX, 'parseTex'), 1);
    add_filter('the_content', array($ez_TeX, 'parseTex'), 1);
    add_filter('the_excerpt', array($ez_TeX, 'parseTex'), 1);
    add_filter('comment_text', array($ez_TeX, 'parseTex'), 1);

    add_action('admin_menu', 'ezLaTeX_ap');
    add_action('activate_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
               array(&$ez_TeX, 'init'));
    add_filter('plugin_action_links', array($ez_TeX, 'plugin_action'), -10, 2);
  }
}


/*
To do:
1. Make the Admin page optional (if add_filter('plugin_action_links',...) returns good.
2. User defined bracketing tags, allowing multiple tags.

*/

?>