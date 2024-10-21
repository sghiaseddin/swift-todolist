<?php
/**
 * SWIFT_TodoList
 * 
 * Main plugin class that initializes the setup, registers hooks, shortcodes,
 * custom post types, and manages script and style enqueues.
 */

 class SWIFT_TodoList {
    public function __construct() {
        register_activation_hook(__FILE__, ['SWIFT_TodoList_Activator', 'activate']);
        add_action('init', [$this, 'setup']);
        add_action('wp_enqueue_scripts', [$this, 'conditionally_enqueue_scripts']);
        $this->register_ajax_handlers();
    }

    /**
     * Customize the WooCommerce My Account Dashboard content.
     */
    public function myaccount_dashboard_content() {
        // Add custom dashboard content. ?>
        <a href="<?php echo esc_url(SWIFT_TODOLIST_BASE_URL) ?>" class="button goto-todolist-page" style="margin-top: 40px;"><?php echo esc_html__('Go to Your Todo List', 'swift-todolist'); ?></a><?php
    }

    /**
     * Register the shortcodes and other setups.
     */
    public function setup() {
        add_shortcode('swift_todolist', [$this, 'render_todolist']);
        $this->check_woocommerce();
        $this->register_task_post_type();
        $this->register_assets();
        add_filter( 'query_vars', [$this, 'add_query_vars'] );
        
    }

    /**
     * Check if WooCommerce is installed and active. 
     * Redirect users to the WooCommerce login/registration page or standard WordPress login page.
     */
    public function check_woocommerce() {
        if (class_exists('WooCommerce')) {
            // WooCommerce is installed, redirect requests for the [swift_todolist] page to the WooCommerce login page.
            add_filter('template_redirect', function () {
                if (is_page() && has_shortcode(get_post()->post_content, 'swift_todolist') && !is_user_logged_in()) {
                    // Redirect to WooCommerce login/registration page
                    wp_redirect(wc_get_page_permalink('myaccount') . '?redirect_to=' . urlencode(get_permalink()));
                    exit;
                }
            });
            // Set the dynamic WooCommerce login URL
            define('SWIFT_TODOLIST_LOGIN_URL', wc_get_page_permalink('myaccount'));
            add_action( 'woocommerce_account_dashboard', [$this, 'myaccount_dashboard_content'], 5 );
        } else {
            // WooCommerce is not installed, use the standard WordPress login page.
            add_filter('template_redirect', function () {
                if (is_page() && has_shortcode(get_post()->post_content, 'swift_todolist') && !is_user_logged_in()) {
                    // Redirect to WordPress login page
                    wp_redirect(wp_login_url(get_permalink()));
                    exit;
                }
            });
            // Set the dynamic WordPress login URL
            define('SWIFT_TODOLIST_LOGIN_URL', wp_login_url());
        }
    }

/**
 * 
 * Register JavaScript and CSS files.
 */
public function register_assets() {
    // Register the script with WordPress
    wp_register_script('swift-todolist', plugin_dir_url(__FILE__) . '../assets/js/swift-todolist.js', ['jquery'], SWIFT_TODOLIST_VERSION, true);
    
    // Register the style with WordPress
    wp_register_style('swift-todolist', plugin_dir_url(__FILE__) . '../assets/css/swift-todolist.css', [], SWIFT_TODOLIST_VERSION);
    
    // Register the dashicons style with WordPress
    wp_register_style('dashicons', plugin_dir_url(__FILE__) . '../assets/css/dashicons.css', [], SWIFT_TODOLIST_VERSION);
}

/**
 * Check if the plugin shortcode is used in the load.
 *
 * @return boolian 
 */
public function conditionally_enqueue_scripts() {
    // Check if the current page has the 'swift_todolist' shortcode.
    if (is_singular('swifttask') || has_shortcode(get_post()->post_content, 'swift_todolist')) {
        $this->enqueue_scripts();
    }
}

/**
 * 
 * Enqueue JavaScript and CSS files.
 */
public function enqueue_scripts() {
    // Localize the script with custom variables
    wp_localize_script('swift-todolist', 'swiftTodoList', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'swiftTodolistBase' => esc_html(SWIFT_TODOLIST_BASE_URL),
        'nonce'   => wp_create_nonce('swift_todolist_nonce')
    ) );
    
    // Enqueue the script with WordPress
    wp_enqueue_script('swift-todolist');
    
    // Enqueue the style with WordPress
    wp_enqueue_style('swift-todolist');
    
    // Enqueue the dashicons style with WordPress
    wp_enqueue_style('dashicons');
}

    /**
     * Register the 'task' custom post type for the To-Do List plugin.
     */
    public function register_task_post_type() {
        $labels = array(
            'name'               => _x('Tasks', 'post type general name', 'swift-todolist'),
            'singular_name'      => _x('Task', 'post type singular name', 'swift-todolist'),
            'menu_name'          => _x('Tasks', 'admin menu', 'swift-todolist'),
            'name_admin_bar'     => _x('Task', 'add new on admin bar', 'swift-todolist'),
            'add_new'            => _x('Add New', 'swifttask', 'swift-todolist'),
            'add_new_item'       => __('Add New Task', 'swift-todolist'),
            'new_item'           => __('New Task', 'swift-todolist'),
            'edit_item'          => __('Edit Task', 'swift-todolist'),
            'view_item'          => __('View Task', 'swift-todolist'),
            'all_items'          => __('All Tasks', 'swift-todolist'),
            'search_items'       => __('Search Tasks', 'swift-todolist'),
            'parent_item_colon'  => __('Parent Tasks:', 'swift-todolist'),
            'not_found'          => __('No tasks found.', 'swift-todolist'),
            'not_found_in_trash' => __('No tasks found in Trash.', 'swift-todolist')
        );
    
        $capabilities = array(
            'edit_post'          => 'edit_swifttask',
            'read_post'          => 'read_swifttask',
            'delete_post'        => 'delete_swifttask',
            'edit_posts'         => 'edit_swifttasks',
            'edit_others_posts'  => 'edit_others_swifttasks',
            'publish_posts'      => 'publish_swifttasks',
            'read_private_posts' => 'read_private_swifttasks',
            'delete_posts'       => 'delete_swifttasks',
            'delete_private_posts' => 'delete_private_swifttasks',
            'delete_published_posts' => 'delete_published_swifttasks',
            'delete_others_posts' => 'delete_others_swifttasks',
            'edit_private_posts' => 'edit_private_swifttasks',
            'edit_published_posts' => 'edit_published_swifttasks',
            'create_posts'       => 'create_swifttasks', // For Gutenberg
        );
    
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => false, // Set to false to make it not publicly queryable
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'swifttask'),
            'capability_type'     => array('swifttask', 'swifttasks'),
            'capabilities'        => $capabilities,
            'map_meta_cap'        => true, // Use the custom capabilities defined above
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor'),
            'exclude_from_search' => true, // Exclude from search
            'show_in_admin_bar'   => false, // Hide from +New in top bar
            'menu_icon'           => 'dashicons-editor-ul', // Set dashicon
        );
    
        register_post_type('swifttask', $args);
    }


    /**
     * Add string query for task-actions.
     *
     * @return array query variables
     */
    public function add_query_vars( $vars ) {
        $vars[] = "task-action";
        $vars[] = "task-id";
        return $vars;
    }


    /**
     * Render the todo list for the current user.
     *
     * @return string HTML output of the to-do list.
     */
    public function render_todolist() {
        ob_start();

        // Check if the action is to view a specific task
        if ( get_query_var('task-action') && get_query_var('task-action') === 'view' && get_query_var('task-id') ) {
            $task_id = intval( get_query_var('task-id') );
            $current_user = wp_get_current_user();
            $task = $this->get_task($current_user->ID, $task_id);

            // Load the task template directly
            if ($task) {
                include plugin_dir_path(__FILE__) . '../templates/task-template.php';
            } else { ?>
                <div class="swift-todo-wrapper">
                    <p><?php echo esc_html__('Task not found or you do not have permission to view this task.', 'swift-todolist'); ?></p>;
                    <a class="button" href="<?php echo esc_url(SWIFT_TODOLIST_BASE_URL) ?>"><?php echo esc_html__('Go back to your Todo List', 'swift-todolist'); ?></a>
                </div><?php
            }
            return ob_get_clean(); // Stop further execution to only show the task view
        }
        $this->render_tasks(get_current_user_id());
        return ob_get_clean();
    }

    /**
     * Render the tasks for a specific user.
     *
     * @param int $user_id User ID.
     */
    public function render_tasks($user_id) {
        $args = array(
            'post_type'   => 'swifttask',
            'post_status' => 'publish',
            'author'      => $user_id,
            'orderby'     => 'modified',
            'order'       => 'DESC',
        );
    
        $tasks_query = new WP_Query($args);
        $tasks = $tasks_query->posts;
    
        include plugin_dir_path(__FILE__) . '../templates/todolist-template.php';
    }
    
    /**
     * Retrieve a task for a specific user.
     *
     * @param int $user_id User ID.
     * @param int $post_id Task post ID.
     * @return array|null Task data, or null if not found.
     */
    public function get_task($user_id, $post_id) {
        $args = array(
            'post_type'   => 'swifttask',
            'post_status' => 'publish',
            'author'      => $user_id,
            'p'           => $post_id,
            'posts_per_page' => 1,
        );
    
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $query->the_post();
            $task = array(
                'id'      => get_the_ID(),
                'title'   => get_the_title(),
                'content' => get_the_content(),
            );
            wp_reset_postdata();
            return $task;
        }
    
        return false;
    }
    
    /**
     * Render the task view.
     *
     * @param int $post_id Task post ID.
     * @return string HTML output of the task view.
     */
    public function task_view($post_id) {
        $current_user = wp_get_current_user();
        $task = $this->get_task($current_user->ID, $post_id);

        include plugin_dir_path(__FILE__) . 'templates/todolist-template.php'; // Render tasks using a template file.
    }

    private function register_ajax_handlers() {
        require_once plugin_dir_path(__FILE__) . 'class-swift-ajax-handler.php';
        new SWIFT_Ajax_Handler();
    }
}
