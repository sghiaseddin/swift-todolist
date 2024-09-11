<?php
/**
 * SH_TodoList
 * 
 * Main plugin class that initializes the setup, registers hooks, shortcodes,
 * custom post types, and manages script and style enqueues.
 */

 class SH_TodoList {
    public function __construct() {
        register_activation_hook(__FILE__, ['SH_TodoList_Activator', 'activate']);
        add_action('init', [$this, 'setup']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        $this->register_ajax_handlers();
    }

    /**
     * Customize the WooCommerce My Account Dashboard content.
     */
    public function myaccount_dashboard_content() {
        // Add custom dashboard content. ?>
        <a href="<?= esc_url(SH_TODOLIST_BASE_URL) ?>" class="button goto-todolist-page" style="margin-top: 40px;">Go your Todo List</a><?php
    }

    /**
     * Register the shortcodes and other setups.
     */
    public function setup() {
        add_shortcode('sh_todolist', [$this, 'render_todolist']);
        $this->check_woocommerce();
        $this->register_task_post_type();
    }

    /**
     * Check if WooCommerce is installed and active. 
     * Redirect users to the WooCommerce login/registration page or standard WordPress login page.
     */
    public function check_woocommerce() {
        if (class_exists('WooCommerce')) {
            // WooCommerce is installed, redirect requests for the [sh_todolist] page to the WooCommerce login page.
            add_filter('template_redirect', function () {
                if (is_page() && has_shortcode(get_post()->post_content, 'sh_todolist') && !is_user_logged_in()) {
                    // Redirect to WooCommerce login/registration page
                    wp_redirect(wc_get_page_permalink('myaccount') . '?redirect_to=' . urlencode(get_permalink()));
                    exit;
                }
            });
            // Set the dynamic WooCommerce login URL
            define('SH_TODOLIST_LOGIN_URL', wc_get_page_permalink('myaccount'));
            add_action( 'woocommerce_account_dashboard', [$this, 'myaccount_dashboard_content'], 5 );
        } else {
            // WooCommerce is not installed, use the standard WordPress login page.
            add_filter('template_redirect', function () {
                if (is_page() && has_shortcode(get_post()->post_content, 'sh_todolist') && !is_user_logged_in()) {
                    // Redirect to WordPress login page
                    wp_redirect(wp_login_url(get_permalink()));
                    exit;
                }
            });
            // Set the dynamic WordPress login URL
            define('SH_TODOLIST_LOGIN_URL', wp_login_url());
        }
    }

    /**
     * Enqueue JavaScript and CSS files.
     */
    public function enqueue_scripts() {
        wp_enqueue_script('sh-todolist-js', plugin_dir_url(__FILE__) . '../assets/js/sh-todolist.js', ['jquery'], SH_TODOLIST_VERSION, true);
        wp_localize_script('sh-todolist-js', 'shTodoList', ['ajaxurl' => admin_url('admin-ajax.php')]);
        wp_enqueue_style('sh-todolist-css', plugin_dir_url(__FILE__) . '../assets/css/sh-todolist.css', [], SH_TODOLIST_VERSION);
        wp_enqueue_style( 'dashicons' );
    }

    /**
     * Register the 'task' custom post type for the To-Do List plugin.
     */
    public function register_task_post_type() {
        $labels = array(
            'name'               => _x('Tasks', 'post type general name', 'sh-todolist'),
            'singular_name'      => _x('Task', 'post type singular name', 'sh-todolist'),
            'menu_name'          => _x('Tasks', 'admin menu', 'sh-todolist'),
            'name_admin_bar'     => _x('Task', 'add new on admin bar', 'sh-todolist'),
            'add_new'            => _x('Add New', 'task', 'sh-todolist'),
            'add_new_item'       => __('Add New Task', 'sh-todolist'),
            'new_item'           => __('New Task', 'sh-todolist'),
            'edit_item'          => __('Edit Task', 'sh-todolist'),
            'view_item'          => __('View Task', 'sh-todolist'),
            'all_items'          => __('All Tasks', 'sh-todolist'),
            'search_items'       => __('Search Tasks', 'sh-todolist'),
            'parent_item_colon'  => __('Parent Tasks:', 'sh-todolist'),
            'not_found'          => __('No tasks found.', 'sh-todolist'),
            'not_found_in_trash' => __('No tasks found in Trash.', 'sh-todolist')
        );
    
        $capabilities = array(
            'edit_post'          => 'edit_task',
            'read_post'          => 'read_task',
            'delete_post'        => 'delete_task',
            'edit_posts'         => 'edit_tasks',
            'edit_others_posts'  => 'edit_others_tasks',
            'publish_posts'      => 'publish_tasks',
            'read_private_posts' => 'read_private_tasks',
            'delete_posts'       => 'delete_tasks',
            'delete_private_posts' => 'delete_private_tasks',
            'delete_published_posts' => 'delete_published_tasks',
            'delete_others_posts' => 'delete_others_tasks',
            'edit_private_posts' => 'edit_private_tasks',
            'edit_published_posts' => 'edit_published_tasks',
            'create_posts'       => 'create_tasks', // For Gutenberg
        );
    
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => false, // Set to false to make it not publicly queryable
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'task'),
            'capability_type'     => array('task', 'tasks'),
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
    
        register_post_type('task', $args);
    }

    /**
     * Render the todo list for the current user.
     *
     * @return string HTML output of the to-do list.
     */
    public function render_todolist() {
        ob_start();

        // Check if the action is to view a specific task
        if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
            $task_id = intval($_GET['id']);
            $current_user = wp_get_current_user();
            $task = $this->get_task($current_user->ID, $task_id);

            // Load the task template directly
            if ($task) {
                include plugin_dir_path(__FILE__) . '../templates/task-template.php';
            } else { ?>
                <div class="sh-todo-wrapper">
                    <p>Task not found or you do not have permission to view this task.</p>;
                    <a class="button" href="<?= esc_url(SH_TODOLIST_BASE_URL) ?>">Go back to your Todo List</a>
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
        global $wpdb;
        $tasks = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID AS id, p.post_title AS title, p.post_content AS content
                FROM {$wpdb->prefix}posts AS p 
                WHERE p.post_author = %d
                AND p.post_status = 'publish'
                AND p.post_type = 'task'
                ORDER BY p.post_modified DESC;",
                $user_id
            ),
            ARRAY_A
        );

        include plugin_dir_path(__FILE__) . '../templates/todolist-template.php'; // Render tasks using a template file.
    }

    /**
     * Retrieve a task for a specific user.
     *
     * @param int $user_id User ID.
     * @param int $post_id Task post ID.
     * @return array|null Task data, or null if not found.
     */
    public function get_task($user_id, $post_id) {
        global $wpdb;

        // Retrieve task from the database.
        $task = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT p.ID AS id, p.post_title AS title, p.post_content AS content
                FROM {$wpdb->prefix}posts AS p 
                WHERE p.post_author = %d
                AND p.ID = %d
                AND p.post_status = 'publish'
                AND p.post_type = 'task'
                ORDER BY p.post_modified DESC;",
                $user_id, $post_id
            ),
            ARRAY_A
        );

        return $task;
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
        require_once plugin_dir_path(__FILE__) . 'class-sh-ajax-handler.php';
        new SH_Ajax_Handler();
    }
}
