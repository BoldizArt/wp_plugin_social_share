<?php
/**
 * @file
 * use BoldizArt\SocialShare\SocialShare;
 */
namespace BoldizArt\SocialShare;

/*
Copyright 2023 Boldizar Santo

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
class SocialShare
{
    /** @param string $path; */
    public $path;

    /** @param string $version; */
    public $version;

    /** @param array $socialShareOptions; */
    public $socialShareOptions;

    /** @param array $socialShareButtons; */
    public $socialShareButtons;

    /**
     * Constructor
     * @param string $path
     */
    function __construct(string $path)
    {
        // Set the plugin path
        $this->path = $path;

        // Set the plugin url
        $this->version = '0.0.1';

        // Set the social share options
        $this->socialShareOptions = get_option('social_share_options', []);

        // Set social media buttons
        $this->socialShareButtons = [
            'facebook' => [
                'id' => 'facebook',
                'name' => 'Facebook',
                'link' => 'https://www.facebook.com/sharer/sharer.php?u='
            ],
            'linkedin' => [
                'id' => 'linkedin',
                'name' => 'LinkedIn',
                'link' => 'https://www.linkedin.com/sharing/share-offsite/?url='
            ],
            'twitter' => [
                'id' => 'twitter',
                'name' => 'Twitter',
                'link' => 'http://twitter.com/share?url='
            ],
            'viber' => [
                'id' => 'viber',
                'name' => 'Viber',
                'link' => 'viber://forward?text='
            ],
            'whatsapp' => [
                'id' => 'whatsapp',
                'name' => 'Whatsapp',
                'link' => 'whatsapp://send?text='
            ],
            'pinterest' => [
                'id' => 'pinterest',
                'name' => 'Pinterest',
                'link' => 'http://pinterest.com/pin/create/button/?url='
            ],
        ];

        // Add styles
        add_action('wp_enqueue_scripts', [$this, 'addStyles']);
        add_action('admin_init', [$this, 'addAdminStyles']);

        // Add scripts
        add_action('wp_enqueue_scripts', [$this, 'addScripts']);
        add_action('admin_enqueue_scripts', [$this, 'addAdminScripts']);

        // Add admin menu item
        add_action('admin_menu', [$this, 'socialShareAdminMenuItem']);

        // Test function
        add_action('init', [$this, 'testFunction']);

        // Place the social share bar below the post title
        add_filter('the_title', [$this, 'addBarBelowThePostTitle'], 10, 1);

        // Place the social share bar floating on the left area
        add_action('wp_footer', [$this, 'addFloatingBar']);

        // Place the social share bar after the post content
        add_filter('the_content', [$this, 'addBarAfterThePostContent'] );

        // Place the social share bar inside the featured image
        add_filter('post_thumbnail_html', [$this, 'addBarInsideTheFeaturedImage'], 10, 1);
    }

    /**
     * Place the social share bar below the post title
     * @param string $title
     */
    function addBarBelowThePostTitle($title)
    {
        // Get allowed positions
        $positions = isset($this->socialShareOptions['position']) && is_array($this->socialShareOptions['position']) ? 
           $this->socialShareOptions['position'] : [];

        // Only modify the title on single pages
        if (in_array('below', $positions) && is_singular()) {
            wp_enqueue_style('social_share_style');
            wp_enqueue_script('social_share_script');
            $title .= $this->createSocialShareHtml();
        }
    
        return $title;
    }

    /**
     * Place the social share bar floating on the left area
     */
    function addFloatingBar()
    {
        // Get allowed positions
        $positions = isset($this->socialShareOptions['position']) && is_array($this->socialShareOptions['position']) ? 
           $this->socialShareOptions['position'] : [];

        // Only add the div on single pages
        if (in_array('float', $positions) && is_singular()) {
            wp_enqueue_style('social_share_style');
            wp_enqueue_script('social_share_script');
            echo $this->createSocialShareHtml('float');
        }
    }
    
    /**
     * Place the social share bar after the post content
     * @param string $content
     */
    function addBarAfterThePostContent($content)
    {
        // Get allowed positions
        $positions = isset($this->socialShareOptions['position']) && is_array($this->socialShareOptions['position']) ? 
           $this->socialShareOptions['position'] : [];

        // Only modify the content on single pages
        if (in_array('after', $positions) && is_singular()) {
            wp_enqueue_style('social_share_style');
            wp_enqueue_script('social_share_script');
            $content .= $this->createSocialShareHtml('block');
        }
    
        return $content;
    }

    /**
     * Place the social share bar inside the featured image
     * @param string $html
    */
    function addBarInsideTheFeaturedImage($html)
    {
        // Get allowed positions
        $positions = isset($this->socialShareOptions['position']) && is_array($this->socialShareOptions['position']) ? 
           $this->socialShareOptions['position'] : [];

        // Only modify the content on single pages
        if (in_array('inside', $positions) && is_singular()) {
            wp_enqueue_style('social_share_style');
            wp_enqueue_script('social_share_script');
            $html = '<div class="social-share-image-wrapper">'.$html.$this->createSocialShareHtml('inside').'</div>';
        }
    
        return $html;
    }
    
    /**
     * Test function
     * @todo Remove this function
     */
    public function testFunction()
    {
        wp_enqueue_style('social_share_style');
        wp_enqueue_script('social_share_script');
    }

    /**
     * Create social share HTML
     * @param string $type
     * @return string
     */
    public function createSocialShareHtml(string $type = 'inline')
    {
        $response = '';
        $fullUrl = urlencode(get_permalink() . '?' . $_SERVER['QUERY_STRING']);
        $socialMedias = array_key_exists('media', $this->socialShareOptions) ? $this->socialShareOptions['media'] : [];
        $color = isset($this->socialShareOptions['custom_color_enabled'], $this->socialShareOptions['custom_color']) && 
            $this->socialShareOptions['custom_color_enabled'] ? 
            $this->socialShareOptions['custom_color'] : 
            false;
        $size = array_key_exists('button_size', $this->socialShareOptions) ? $this->socialShareOptions['button_size'] : 'medium';
        usort($socialMedias, [$this, 'comparePositions']);
        foreach ($socialMedias as $socialMedia) {
            if (isset($socialMedia['name'], $socialMedia['enabled']) && $socialMedia['enabled']) {
                if (array_key_exists($socialMedia['name'], $this->socialShareButtons)) {
                    $media = $this->socialShareButtons[$socialMedia['name']];
                    $response .= '
                        <a href="'.$media['link'].$fullUrl.'" 
                            title="'.$media['name'].'" 
                            aria-label="'.$media['name'].'" 
                            rel="noreferrer nofollow" 
                            target="_blank" 
                            class="social-share-link ' . $size . ' ' . $media['id'] . '"
                        >
                            '.file_get_contents("{$this->path}/assets/icons/{$media['id']}.svg").'
                        </a>
                    ';
                }
            }
        }

        // Set style
        $style = $color ? '
            <style>
                .social-share-icons .social-share-link {
                    border: 1px solid '.$color.';
                    color: '.$color.';
                }
                .social-share-icons .social-share-link:hover {
                    background-color: '.$color.';
                    color: #fff;
                }
            </style>
        ' : '';

        return $response ? "
            <div class='social-share-icons {$type}'>
                {$response}
                {$style}
            </div>
        " : '';
    }

    // create the main menu page
    function socialShareAdminMenuItem() {
        add_menu_page( 
            __('Social share', 'social_share'),
            __('Social share', 'social_share'),
            'manage_options',
            'social-share',
            [$this, 'socialShareOptions'],
            'dashicons-share',
            25
        );
    }

    /**
     * Compare positions
     * @param array $a
     * @param array $b
     * @return int
     */
    public function comparePositions($a, $b)
    {
        return $a['position'] - $b['position'];
    }

    // callback function for the main menu page
    function socialShareOptions() {
        // Set message
        $messages = [];

        // Check for post request
        if (array_key_exists('social_share_options', $_POST)) {
            $socialShare = $_POST['social_share_options'];
            if (update_option('social_share_options', $socialShare)) {
                $this->socialShareOptions = get_option('social_share_options', []);
                $messages['success'] = __('Successfully saved.', 'social_share');
            } else {
                $messages['error'] = __('Something went wrong. Please try again.', 'social_share');
            }
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Social Share options', 'social_share'); ?></h1>
            <?php if ($messages && is_array($messages) && !empty($messages)): ?>
                <?php foreach ($messages as $type => $message): ?>
                    <div class="notice notice-<?php echo $type; ?>"> 
                        <p><strong><?php echo $message; ?></strong></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form method="post" class="social-share-form" action="<?php echo admin_url('/admin.php?page=social-share'); ?>">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label><?php _e('Display on', 'social_share'); ?></label><br />
                                <small>(<?php _e('Select one or more', 'social_share'); ?>)</small>
                            </th>
                            <td>
                                <?php
                                    $postTypes = get_post_types(['public' => true]);
                                    foreach ($postTypes as $postType): 
                                        if (in_array($postType, ['attachment', 'revision', 'nav_menu_item'])) {
                                            continue;
                                        }
                                ?>
                                    <label>
                                        <input type="checkbox" 
                                            name="social_share_options[post_types][]" 
                                            value="<?php echo $postType; ?>" 
                                            <?php echo isset($this->socialShareOptions['post_types']) && 
                                                is_array($this->socialShareOptions['post_types']) && 
                                                in_array($postType, $this->socialShareOptions['post_types']) ? 'checked' : ''; 
                                            ?>
                                        ><?php echo ucfirst($postType); ?></label><br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="buttonSize"><?php _e('Button size', 'social_share'); ?></label></th>
                            <td>
                                <?php $size = isset($this->socialShareOptions['button_size']) ? $this->socialShareOptions['button_size'] : 'small'; ?>
                                <select name="social_share_options[button_size]" id="buttonSize">
                                    <option value="small" <?php echo $size == 'small' ? 'selected' : ''; ?>>
                                        <?php _e('Small', 'social_share'); ?>
                                    </option>
                                    <option value="medium" <?php echo $size == 'medium' ? 'selected' : ''; ?>>
                                        <?php _e('Medium', 'social_share'); ?>
                                    </option>
                                    <option value="large" <?php echo $size == 'large' ? 'selected' : ''; ?>>
                                        <?php _e('Large', 'social_share'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e('Social media share buttons', 'social_share'); ?></label>
                            </th>
                            <td>
                                <?php foreach ($this->socialShareButtons as $name => $socialShareButton): ?>
                                    <div class="media <?php echo $name; ?>">
                                        <p><strong> <?php echo $socialShareButton['name']; ?></strong></p>
                                        <input type="hidden" name="social_share_options[media][<?php echo $name; ?>][name]" value="<?php echo $name; ?>">
                                        <input type="checkbox" 
                                            name="social_share_options[media][<?php echo $name; ?>][enabled]" 
                                            value="1"
                                            id="<?php echo $postType; ?>Enabled"
                                            <?php echo isset($this->socialShareOptions['media'],
                                                $this->socialShareOptions['media'][$name], 
                                                $this->socialShareOptions['media'][$name]['enabled']) &&
                                                $this->socialShareOptions['media'][$name]['enabled'] ? 'checked' : ''; 
                                            ?>
                                        >
                                        <label for="<?php echo $postType; ?>Enabled">
                                            <?php _e('Enabled', 'social_share'); ?>
                                        </label>
                                        <br>
                                        <label for="<?php echo $name; ?>">Position</label><br>
                                        <input name="social_share_options[media][<?php echo $name; ?>][position]" 
                                            type="number" 
                                            id="<?php echo $name; ?>" 
                                            value="<?php echo isset($this->socialShareOptions['media'],
                                                $this->socialShareOptions['media'][$name], 
                                                $this->socialShareOptions['media'][$name]['position']) ?
                                                $this->socialShareOptions['media'][$name]['position'] : 0; 
                                            ?>"
                                        >
                                        <hr />
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <th scope="row">
                            <label><?php _e('Use custom color for social mebia icons', 'social_share'); ?></label><br />
                            <small>(<?php _e('Disable if you want to use their original color', 'social_share'); ?>)</small>
                        </th>
                        <td class="media">
                            <input type="checkbox" 
                                name="social_share_options[custom_color_enabled]" 
                                value="1"
                                id="customColorEnabled"
                                <?php echo isset($this->socialShareOptions['custom_color_enabled']) &&
                                    $this->socialShareOptions['custom_color_enabled'] ? 'checked' : ''; 
                                ?>
                            >
                            <label for="customColorEnabled">
                                <?php _e('Enable custom color', 'social_share'); ?>
                            </label>
                            <br/>
                            <input type="color" 
                                name="social_share_options[custom_color]" 
                                value="<?php echo isset($this->socialShareOptions['custom_color']) ? 
                                    $this->socialShareOptions['custom_color'] : ''; ?>"
                            >
                        </td>
                        <tr>
                            <th scope="row">
                                <label><?php _e('Position of the social media share buttons', 'social_share'); ?></label><br />
                                <small>(<?php _e('Select one or more', 'social_share'); ?>)</small>
                            </th>
                            <td>
                                <?php $selectedPositions = isset($this->socialShareOptions['position']) && 
                                    is_array($this->socialShareOptions['position']) ? 
                                        $this->socialShareOptions['position'] : 
                                        []; 
                                ?>
                                <?php $positions = [
                                    'below' => __('Below the post title', 'social_share'),
                                    'float' => __('Floating on the left area', 'social_share'),
                                    'after' => __('After the post content', 'social_share'),
                                    'inside' => __('Inside the featured image', 'social_share'),
                                ]; ?>
                                <?php foreach ($positions as $key => $position): ?>
                                <label>
                                    <input type="checkbox" 
                                        name="social_share_options[position][]" 
                                        value="<?php echo $key; ?>" 
                                        <?php echo in_array($key, $selectedPositions) ? 'checked' : ''; ?>
                                    >
                                    <?php echo $position; ?>
                                </label>
                                <br />
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
            </form>
        </div>  
    <?php 
    }

    /**
     * Add stylesheets to the website
     */
    function addStyles()
    {
        wp_register_style('social_share_style', plugins_url('./../assets/css/minified/social-share.css', __FILE__), [], $this->version, 'all');
    }

    /**
     * Add admin stylesheets to the website
     */
    function addAdminStyles()
    {
        wp_register_style('social_share_style', plugins_url('./../assets/css/minified/social-share-admin.css', __FILE__), [], $this->version, 'all');
    }

    /**
     * Add scripts to the website
     */
    function addScripts()
    {
        wp_register_script('social_share_script', plugins_url('./../assets/js/minified/social-share.js', __FILE__), [], $this->version, 'all');
    }

    /**
     * Add admin scripts to the website
     */
    function addAdminScripts()
    {
        wp_register_script('social_share_script', plugins_url('./../assets/js/minified/social-share-admin.js', __FILE__), [], $this->version, 'all');
    }

    /** 
     * Activation
     */
    function activate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Deactivation
     */
    function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Uninstall
     */
    function uninstall()
    {
        // Security checks
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            exit;
        }
    }
}
