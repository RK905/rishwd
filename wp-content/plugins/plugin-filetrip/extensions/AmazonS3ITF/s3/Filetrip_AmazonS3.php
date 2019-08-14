<?php


    /**
     * Class Filetrip_AmazonS3
     */
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;


require_once __DIR__.'/Filetrip_AmazonS3_Setting_Page.php';

if(!class_exists('Filetrip_AmazonS3')){
    class Filetrip_AmazonS3
    {
        private static $app_key;
        private static $app_secret;
        private static $app_info;
        private static $app_bucket;
        private static $app_bucket_region;
        private $web_auth;


        static public $amazon_csrf_token_slug = 'amazon_token';
        static public $amazon_active_slug = 'amazon_active';
        static public $amazon_client_id = 'Filetrip/1.0';
        static public $amazon_user_id_slug = 'amazon_user_id';
        static public $amazon_key_slug = 'amazon_key_ID';
        static public $amazon_secret_slug = 'amazon_secret_access';
        static public $amazon_bucket_slug = 'amazon_bucket_name';
        static public $amazon_bucket_region_slug = 'amazon_bucket_region_name';
        static public $amazon_resumable_uploads_slug = 'amazon_resumable_uploads';


        public $settings = array();

        public function __construct($setting) {


            // Exit if it is disabled
            if(Filetrip_AmazonS3_Utility::check_if_amazon_disabled())
            {
                return;
            }

            if(!isset($setting[Filetrip_AmazonS3::$amazon_key_slug]) || !isset($setting[Filetrip_AmazonS3::$amazon_secret_slug])
                || empty($setting[Filetrip_AmazonS3::$amazon_bucket_slug]))
            {
                Filetrip_AmazonS3::deactivate_amazon_s3();
                return;
            }

            // AJAX Hooks
            add_action('wp_ajax_get_s3_folder_list',array( $this, 'get_folder_list_html'));

            self::$app_key = trim($setting[Filetrip_AmazonS3::$amazon_key_slug], " \t\n\r\0");
            self::$app_secret = trim($setting[Filetrip_AmazonS3::$amazon_secret_slug], " \t\n\r\0");
            self::$app_bucket = trim($setting[Filetrip_AmazonS3::$amazon_bucket_slug], " \t\n\r\0");
            self::$app_bucket_region = trim($setting[Filetrip_AmazonS3::$amazon_bucket_region_slug], " \t\n\r\0");

            if(!Filetrip_AmazonS3::is_amazon_s3_active())
            {
                try {

                    self::$app_info = new \iTechFlare\WP\iTechFlareExtension\AmazonS3ITF();
                    self::$app_info = self::$app_info->init_s3_client(self::$app_key, self::$app_secret, self::$app_bucket_region)->listBuckets(array());
                    $is_bucket_exist = in_array(self::$app_bucket,array_column(self::$app_info['Buckets'],'Name'));

                }
                catch (S3Exception $ex) {
                    do_action('itech_error_caught', "Filetrip: Error communicating with Amazon API: " . $ex->getMessage() . "\n");
//                    die($ex->getMessage());
                    Filetrip_AmazonS3::deactivate_amazon_s3();
                    return;
                }

                if($is_bucket_exist){
                    $setting[Filetrip_AmazonS3::$amazon_active_slug] = true;
                    Filetrip_AmazonS3::update_amazon_s3_settings( $setting );
                }

            }

//            self::get_folder_list();


        }

        public function auth_start()
        {
            if(!Filetrip_AmazonS3::is_amazon_s3_active() && strlen(self::$app_key)>5 && strlen(self::$app_secret)>5)
            {
                $this->auth_url = $this->web_auth->start();
                header('location:'.$this->auth_url);
                exit();
            }else{
                do_action('itech_error_caught', 'Please fill designated forms with correct values (Amazon)');
                // Handle error
            }
        }

        static public function is_amazon_s3_active()
        {
            $temp_setting = Filetrip_AmazonS3::get_amazon_settings();

            if(isset($temp_setting[Filetrip_AmazonS3::$amazon_active_slug]) && $temp_setting[Filetrip_AmazonS3::$amazon_active_slug])
            {
                return true;
            }else
            {
                return false;
            }
        }

        static public function deactivate_amazon_s3()
        {
            $temp_setting = Filetrip_AmazonS3::get_amazon_settings();
            //unset($temp_setting[Filetrip_AmazonS3::$amazon_box_auth_code_slug]);
            unset($temp_setting[Filetrip_AmazonS3::$amazon_active_slug]);

            Filetrip_AmazonS3::update_amazon_s3_settings($temp_setting);
        }

        static public function get_amazon_settings()
        {
            $amazon_settings = (array)get_option(Filetrip_AmazonS3_Setting_page::$amazon_s3_setting_page);

            return $amazon_settings;
        }

        static public function update_amazon_s3_settings($new_setting)
        {
            update_option( Filetrip_AmazonS3_Setting_page::$amazon_s3_setting_page, $new_setting );
        }

        static public function get_folder_list($path = '')
        {
            $amazon_setting = Filetrip_AmazonS3::get_amazon_settings();
            $bucket = $amazon_setting[Filetrip_AmazonS3::$amazon_bucket_slug];

            try{

                // @Apurba, you can see the below example of how to access a specific folder
                // More resources (https://stackoverflow.com/questions/18683206/list-objects-in-a-specific-folder-on-amazon-s3)
                self::$app_info = new \iTechFlare\WP\iTechFlareExtension\AmazonS3ITF();
                $client =  self::$app_info->init_s3_client(self::$app_key,self::$app_secret, self::$app_bucket_region);
                $results = $client->getPaginator('ListObjects',['Bucket' => $bucket, 'delimiter'=>'/', 'Prefix' => $path]);
                
                $folder_lists = array();
                $folder_lists_array = array();
                $label_count = count(array_filter(explode('/',$path)));

                foreach ($results as $result) {
                    foreach($result['Contents'] as $object){
                        if (strpos($object['Key'],'/') !== false){
                            $folder_lists[] = $object['Key'];
                        }
                    }
                }

                $folder_lists = array_unique($folder_lists) ;

                foreach ($folder_lists as $folder_list){
                    $folder_name = explode('/',$folder_list);
                
                    if(count($folder_name)>$label_count){
                        array_push($folder_lists_array,$folder_name[(int)$label_count]);
                    }
                }

                return array_unique($folder_lists_array);

            }catch(S3Exception $ex)
            {
                do_action('itech_error_caught', "Error communicating with Amazon  API: " . $ex->getMessage() . "\n");
                Filetrip_AmazonS3::deactivate_amazon_s3();
                exit();
            }
        }
        // Called by ajax from meta arfaly's posts
        public function get_folder_list_html()
        {

            $switch = '_filetrip_amazon_s3_folder';
            if( isset($_POST['page']) && $_POST['page'] == 'filetrip_settings')
            {
                $switch = 'filetrip_settings\\\\[amazon_s3_folder\\\\]';
            }

            // Sanitize the whole input
            $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $path = $_POST['path'];

            $files = Filetrip_AmazonS3::get_folder_list($path);

            // Build step back one level path name
            $path_array = array_filter(explode('/',$path));

            $step_back_path = '';
            if(count($path_array)>1){
                array_pop($path_array);
                $step_back_path = implode('/',(array)$path_array);
            }

            $folder_icon = '<img src="'.ITECH_FILETRIP_PLUGIN_URL.'/assets/img/dropbox-folder.png" width="30" height="30">';
            ob_start();
            ?>
            <div class="arfaly-amazon-folder-loading"></div>
            <h3>Path: <b><?php echo ($path=='')?'/root':$path; ?></b></h3>
            <?php if ( empty( $files ) ) : ?>
            <ul>
                <li>
                    <?php echo $folder_icon;?>
                    <a href="javaScript: void(0);" onclick="javascript:arfaly_s3_get_folder_list('<?php echo $step_back_path; ?>')">../</a>
                </li>
            </ul>

            <?php echo _e( 'No folder within this folder', 'filetrip-plugin' ); ?>
        <?php else : ?>
            <ul class="arfaly-file-listing">
                <?php if($path != ''){ ?>
                    <li>
                        <?php echo $folder_icon;?>
                        <a href="javaScript: void(0);" onclick="javascript:arfaly_s3_get_folder_list('<?php echo $step_back_path; ?>')">../</a>
                    </li>
                <?php }
                foreach( $files as $file ) : ?>
                    <?php if ( count($files)>0 ) :

                        if($path==''){

                            $next_link = $file;
                        }else{
                            $next_link = $path.'/'.$file;
                        }

                        ?>

                        <li class="arfaly-folder">
                            <a href="javaScript: void(0);" onclick="javascript:arfaly_update_s3_folder('<?php echo $next_link; ?>', '<?php echo $switch; ?>')" class="button">Select</a>
                            <?php echo $folder_icon;?>
                            <a href="javaScript: void(0);" onclick="javascript:arfaly_s3_get_folder_list('<?php echo $next_link; ?>')"><?php echo $file ;?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
            </div>
            <?php

            $html = ob_get_contents();
            ob_end_clean();

            echo $html;

            die();
        }
        /**
         * @param array
         *      This function will be called as Server Side Event process, so event-stream is expected to open,
         *      and progress information should be sent back to the client side to notify users
         */
        public static function resumable_file_upload($destination_path , $file_uri, $filesize, $userTaggedFolder = false, $username = 'Guest', $sse_enabled = true,$att_id=null)
        {

            set_time_limit(0);
            session_write_close();
            ignore_user_abort( true );

            if(!Filetrip_AmazonS3::is_amazon_s3_active())
            {
                return false;
            }

            // Upload ID to resume or null (new chunked upload)
            $uploadID = null;
            // Resume the upload
            $offset = null;
            $result = null;
            $data = null;

            $amazon_setting = Filetrip_AmazonS3::get_amazon_settings();

            $filename = basename($file_uri);
            $keyname = $destination_path . $filename;

            self::$app_info = new \iTechFlare\WP\iTechFlareExtension\AmazonS3ITF();
            $s3 = self::$app_info->init_s3_client(self::$app_key,self::$app_secret,self::$app_bucket_region);
            $bucket = $amazon_setting[Filetrip_AmazonS3::$amazon_bucket_slug];

            $result = $s3->createMultipartUpload([
                'Bucket'       => $bucket,
                'Key'          => $keyname,
                'StorageClass' => 'REDUCED_REDUNDANCY',
                'ACL'          => 'public-read',
                'Metadata'     => [
                    'source' => 'FileTrip'
                ]
            ]);
            $uploadId = $result['UploadId'];

            try {
                $file = fopen($file_uri, 'r');

                $partNumber = 1;
                while (!feof($file)) {
                    $result = $s3->uploadPart([
                        'Bucket'     => $bucket,
                        'Key'        => $keyname,
                        'UploadId'   => $uploadId,
                        'PartNumber' => $partNumber,
                        'Body'       => fread($file, 5 * 1024 * 1024),
                    ]);
                    $parts['Parts'][$partNumber] = [
                        'PartNumber' => $partNumber,
                        'ETag' => $result['ETag'],
                    ];
                    $partNumber++;

                    if($sse_enabled){
                        /**
                         *  Construct SSE message and echo it to the client
                         */                   
                        $data = array(
                            'percentage' => intval($partNumber * (5 * 1024 * 1024) / $filesize * 100 ),
                            'bytes' => intval($partNumber * 5 * 1024 * 1024 )
                        );
                        Filetrip_Channel_Utility::sse_send_message($uploadID, json_encode($data,JSON_UNESCAPED_SLASHES));
                    }
                }

                fclose($file);

            } catch (MultipartUploadException $exp) {

                error_log("Filetrip: " . $exp->getMessage());

                $result = $s3->abortMultipartUpload([
                    'Bucket'   => $bucket,
                    'Key'      => $keyname,
                    'UploadId' => $uploadId
                ]);

                $shortExpMsg = substr($exp->getMessage(), 0, Filetrip_Constants::ERROR_MESSAGE_MAX_LENGHT);

                if($sse_enabled){
                    Filetrip_Channel_Utility::sse_send_message($uploadID, $shortExpMsg, "error");
                }

                return false;

            }

            $result = $s3->completeMultipartUpload([
                'Bucket'   => $bucket,
                'Key'      => $keyname,
                'UploadId' => $uploadId,
                'MultipartUpload'    => $parts,
            ]);

            /* Uploading process has finished */
            if($att_id != null){
                do_action('itf/filetrip/amazons3/file/uploded', $result, $att_id);
            }

            if($sse_enabled){
                /**
                 *  Construct SSE message and echo it to the client
                 */
                Filetrip_Channel_Utility::sse_send_message($offset, "finished", "finished");
                return true;
            }
        }

    }

    new Filetrip_AmazonS3_Setting_Page();
}

