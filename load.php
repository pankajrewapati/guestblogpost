<?php
if ( is_admin() ) {
    // inside this file we have hook for add menu in admin
	require_once ADDBLOG_PLUGIN_DIR . '/admin/admin.php'; // we wiil add menu and add one plugin setting page 
}

// this is add shotrcode hook , we will use it in frontend for show blog form
add_shortcode( 'BlogForm', 'BlogFormCB' );
function BlogFormCB( $atts ) {	
    return BlogFormHtml($atts);
}

// this is list blog post shotrcode hook , we will use it in frontend for show blog post in list form
add_shortcode( 'BlogShow', 'BlogShowCB' );
function BlogShowCB( $atts ) {	
    return BlogShowHtml($atts);
}

// This si for show post html code
function BlogShowHtml($atts){
    extract(shortcode_atts(array(
        'bgcolor' => '#efefef',
        'post_per_page' => 10
    ), $atts));
    $paged = 1;
    $form = '';
    $form .= '<div class="outer_div" id="outer_div" style="background-color:'.$bgcolor.'">';
    $form .= '<input type="hidden" value="'.$post_per_page.'" id="post_per_page">';
    $form .= '<div class="addblog-form">';    

    // here we will get all post and show in loop form
    $loopdata = getBlogPostLoopHTML($post_per_page,$paged);    

    $allPage = $loopdata['allPage'];
    $form .= $loopdata['form'];
    $form .= '</div>';
    // this is for pagination
    $form .= getMyPagination($allPage,$post_per_page);
    
    $form .= '</div>';
    return $form;
}

// inside this function we will run loop and return post
function getBlogPostLoopHTML($post_per_page,$paged){

    $isAdministrator = getUserRoleText();
    if($isAdministrator == 'administrator'){
        $isAdministrator = true;
    }else{
        $isAdministrator = false;
    }

    $post_allpost = getBlogPosts($post_per_page,$paged);
    $posts = $post_allpost['posts'];
    $allPage = $post_allpost['allPage'];
    $form = '';
    if(!empty($posts)){
        foreach ($posts as $key => $value) {
            $is_approved = false;
            if($value->post_status == 'pending'){
                $is_approved = false;
            }else if($value->post_status == 'publish'){
                $is_approved = true;
            }

            $thumb = get_the_post_thumbnail( $value->ID, 'thumbnail', array( 'class' => 'alignleft' ) );;
            $permalink = get_permalink($value->ID);
            $exp = wp_trim_words( $value->post_excerpt, 10, '...' );
            $con = wp_trim_words( $value->post_content, 10, '...' );
            if($thumb){
                $thumb_class = 'have_img';
            }else{
                $thumb_class = '';
            }

            $form .= '<div class="repeated_div clearfix '.$thumb_class.'">';
            
            $form .= '<div class="img_div">';
            $form .= $thumb;
            $form .= '</div>';

            $form .= '<div class="content_div">';
            $form .= '<h4><a href="'.$permalink.'">'.$value->post_title.'</a></h4>';
            $form .= '<p class="largetext">'.$con.'</p>';
            $form .= '<p class="smalltext">'.$exp.'</p>';
            $form .= '</div>';

            $form .= '<div class="btns">';
            $form .= '<a class="viewmore mybutton" href="'.$permalink.'">View More</a>';
            if($isAdministrator){
                if($is_approved){
                    $form .= '<a data="unapproved" post_id="'.$value->ID.'" class="viewmore mybutton approve approvdone">Un Approved</a>';
                }else{
                    $form .= '<a data="approved" post_id="'.$value->ID.'" class="viewmore mybutton approve approvpending">Approved</a>';
                }
            }
            $form .= '</div>';

            $form .= '</div>';
        }
    }else{
        $form = " <h4> Didn't have any publish post yet! <a href='#'>Contact Us</a></h4>";   
    }
    return array('form'=>$form,'allPage'=>$allPage);
}

// on click pagination function
add_action('wp_ajax_getPaginationPostRecord', 'getPaginationPostRecord');
add_action('wp_ajax_nopriv_getPaginationPostRecord', 'getPaginationPostRecord');
function getPaginationPostRecord(){
    $post_per_page = isset($_POST['post_per_page']) ? $_POST['post_per_page'] : 10;
    $paged = isset($_POST['pn']) ? $_POST['pn'] : 1;
    $loopdata = getBlogPostLoopHTML($post_per_page,$paged);
    echo $loopdata['form']; die;
}

// we can get pagination html code by this plugin
function getMyPagination($allPage,$post_per_page){
    global $post;
    if($allPage <= $post_per_page){
        return false;
    }
    $get_number_pages = 1;
    if($post_per_page && $allPage){
        $get_number_pages = ceil($allPage/$post_per_page);
    }
    $current_page = isset($_GET['pn']) ? $_GET['pn'] : 1;
    $current_page_link = get_permalink($post->ID);
    $form = '';
    $form .= '<div class="mypagination">';

    $form .= '<ul>';
    for ($i=1; $i <= $get_number_pages; $i++) { 
        $active_class = '';
        if($current_page == $i){
            $active_class = 'active';
        }
        $form .= '<li pn="'.$i.'" class="'.$active_class.'"><a>'.$i.'</a></li>';
    }
    $form .= '</ul>';
    
    return $form .= '</div>';

}



// include css and js files based on condition
function addblog_add_scripts_and_styles() {
	global $post;
	$page_content = isset($post->post_content)? $post->post_content : '';
    if($page_content == ''){
        return false;
    }
	$shortcode = '[BlogForm';
    $shortcode2 = '[BlogShow';
	if (false !== strpos($page_content, $shortcode) || false !== strpos($page_content, $shortcode2)) {
		$handle = 'addblog';
		$addblod_css = plugins_url( '/css/addblog.css', ADDBLOG_PLUGIN );
		$addblod_js = plugins_url( '/js/addblog.js', ADDBLOG_PLUGIN );

	    wp_enqueue_script( $handle, $addblod_js, array( 'jquery' ), ADDBLOG_VERSION, true );
	    wp_enqueue_style( $handle, $addblod_css);

	    wp_localize_script( 'addblog', 'my_ajax_object',
            array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
}
add_action('wp_enqueue_scripts', 'addblog_add_scripts_and_styles');

// here we will show add blog post html form
function BlogFormHtml($atts){

    extract(shortcode_atts(array(
        'bgcolor' => '#efefef'
    ), $atts));

	$form = '';
    if(!is_user_logged_in()){
        return  '<div class="notice"><b>Add Blog Post </b>Please Login by author!</div>'; 
    }
    $is_author = getUserRoleText();
    if($is_author != 'author'){
        return  '<div class="notice"><b>Add Blog Post </b>Please Login by author!</div>'; 
    }
    $onkeyup1 = "disallowhtml('decription')";
    $onkeyup2 = "disallowhtml('excerpt')";
	$form .= '<div class="addblog-form" style="background-color:'.$bgcolor.'">
	<form action="" id="addblog_form">
  <div class="container">
    <h4>Add Post</h4>
    <p>Please fill in this form to create an post.</p>
    <hr>

    <label for="title"><b>Title</b></label>
    <input type="text" placeholder="Title" name="title" required/>

    <label for="dsc"><b>Description</b></label>
    <textarea onkeyup="'.$onkeyup1.'" id="decription" placeholder="Description" name="decription" required></textarea>

    <label for="excerpt"><b>Excerpt</b></label>
    <textarea onkeyup="'.$onkeyup2.'" id="excerpt" placeholder="Excerpt" name="excerpt" required></textarea>

    <div class="feature_img_div">
    <a class="remove_img">X</a>
    <label class="custom-file-upload">
    	<img id="feature_image_show"/>
	    <input type="file" id="feature_image" accept="image/png, image/gif, image/jpeg" onchange="loadFile(event)"/>
	    <span class="upload_img">Upload Image</span>
	</label>
	</div>
	<input type="hidden" name="feature_image_hidden" id="feature_image_hidden" />
	<input type="hidden" name="feature_image_extension" id="feature_image_extension" />

    <p class="rightlink">All blog posts <a href="" style="color:dodgerblue">View Blog</a>.</p>

	<div class="clear"></div>
    <div class="mt-20 clearfix">
      <a href="" class="mybutton cancelbtn">Cancel</a>
      <button type="submit" class="addBlogBtn">Add Post</button>
    </div>
  </div>
</form><div>';
	return $form;
}

// save blog post by ajax
add_action('wp_ajax_addBlogAjax', 'addBlogAjax');
add_action('wp_ajax_nopriv_addBlogAjax', 'addBlogAjax');

function addBlogAjax(){
	$data = [];
	$user_id = get_current_user_id();
	$is_author = getUserRoleText();
	if($is_author != 'author'){
		echo 'This is not author'; die;
	}
	if(!is_user_logged_in()){
		echo  'Please Login!'; die;
	}	 	
	$base64_image = $extension = '';
    if(!empty($_POST) && isset($_POST['formdata'])){
        parse_str($_POST['formdata'], $data);
    }
    if(!empty($_POST) && isset($_POST['uploadimg'])){
    	$base64_image = $_POST['uploadimg'];
    }
    if(!empty($_POST) && isset($_POST['extension'])){
    	$extension = $_POST['extension'];
    }

    if(!empty($data)){
    	$title = isset( $data['title']) ? $data['title'] : ''; 
    	$decription = isset($data['decription']) ? $data['decription'] : ''; 
    	$excerpt = isset($data['excerpt']) ? $data['excerpt'] : '';

        $title = sanitize_text_field($title); 
        $decription = sanitize_text_field($decription); 
        $excerpt = sanitize_text_field($excerpt); 
    	$my_post = array(
            'post_title' => $title,
            'post_content' => $decription,
            'post_excerpt' => $excerpt,
            'post_type' => 'post',
            'post_status' => 'pending',
            'post_author' => $user_id
        );
        $post_id = wp_insert_post($my_post);
	    if($base64_image && $post_id){
	    	$attached_id = setFeatureImage($base64_image,$post_id,$extension);
	    }
    }



}

// we can set uploaded function as a feature image of post
function setFeatureImage($image_url,$post_id,$extension='png'){
	$user_id = get_current_user_id();
	$upload_dir = wp_upload_dir();

    if(wp_mkdir_p($upload_dir["path"])){
        $file = $upload_dir["path"]."/";
    }else{
        $file = $upload_dir["basedir"]."/";
    }

	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $length = 5;
    $str_random = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    $post_title = $str_random;
    $safeName = $str_random.'.'.$extension;
    if (preg_match('/^data:image\/(\w+);base64,/', $image_url)) {
        $dataII = substr($image_url, strpos($image_url, ',') + 1);
        $dataII = base64_decode($dataII);
        $filename = $profilename = 'feature'.$safeName;
        $post_title = 'feature-'.$post_title;
        $root_dir = $file.$profilename;
        file_put_contents($root_dir, $dataII);

	    $wp_filetype = wp_check_filetype($filename,null);
	    $post_author = get_post_field("post_author",$post_id);
	    $attachment = array(
	        "post_author" => $user_id,
	        "post_mime_type" => $wp_filetype["type"],
	        "post_title" => sanitize_file_name($post_title),
	        "post_content" => "",
	        "post_status" => "inherit"
	    );
	    $attach_id = wp_insert_attachment($attachment,$root_dir,$post_id);
	    require_once(ABSPATH."wp-admin/includes/image.php");
	    $attach_data = wp_generate_attachment_metadata($attach_id,$root_dir);
	    $res1 = wp_update_attachment_metadata($attach_id,$attach_data);
	    $res2 = set_post_thumbnail($post_id, $attach_id);
    }
}

// we can get login user lore slug by this function
function getUserRoleText(){
	$current_user = wp_get_current_user();
    if ( in_array( 'author', (array) $current_user->roles )) {
        return 'author';
    }else if ( in_array( 'administrator', (array) $current_user->roles )) {
        return 'administrator';
    }
    return 'unserrole';
}

// here we have get post function for ger posts
function getBlogPosts($post_per_page,$paged=1){
    $isAdministrator = getUserRoleText();
    if($isAdministrator == 'administrator'){
        $status_arr = array('publish', 'pending');
    }else{
        $status_arr = array('publish');
    }

    $argsALL = array(
      'post_status'  => $status_arr, 
      'posts_per_page'  => -1,
      'post_type'       => 'post'
    );
    $get_posts = new WP_Query( $argsALL );
    $allPage = $get_posts->found_posts;

    $args = array(
      'post_status'  => $status_arr, 
      'posts_per_page'  => $post_per_page,
      'paged'  => $paged,
      'post_type'       => 'post'
    );
    $get_posts = new WP_Query( $args );
    $posts = $get_posts->get_posts();
    return array('posts'=>$posts,'allPage'=>$allPage);
}


// approve un approved ajax function
add_action('wp_ajax_approvedUnApproved', 'approvedUnApproved');
add_action('wp_ajax_nopriv_approvedUnApproved', 'approvedUnApproved');

function approvedUnApproved(){
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $data_type = isset($_POST['data_type']) ? $_POST['data_type'] : '';
    if($post_id){
        if($data_type == 'approved'){
            $status = 'publish';
        }else{
            $status = 'pending';
        }

        $update_status = array(
            'post_type' => 'post',
            'ID' => $post_id,
            'post_status' => $status
        );
        $statusTest = wp_update_post($update_status);
        if($status == 'publish'){
            echo 'publish'; die;
        }else{
            echo 'pending'; die;
        }
    }
}
?>