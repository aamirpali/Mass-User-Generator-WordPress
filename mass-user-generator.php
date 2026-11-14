<?php
/*
Plugin Name: Mass User Generator
Plugin URI: https://github.com/aamirpali/Mass-User-Generator-WordPress
Description: A secure, optimized WordPress plugin to generate multiple users.
Version: 1.1
Author: Ameer Baksh
Author URI: https://ameerbaksh.in/
*/

// Block direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Load initializations
$init_file = plugin_dir_path(__FILE__) . 'include/init.php';
if (file_exists($init_file)) {
    include_once $init_file;
}

// Add an admin menu option
function mass_user_generator_menu() {
    add_menu_page('User Generator', 'User Generator', 'manage_options', 'user-generator', 'user_generator_page', 'dashicons-groups');
}
add_action('admin_menu', 'mass_user_generator_menu');

// Define the user generator page
function user_generator_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Handle User Generation
    if (isset($_POST['generate_users'])) {
        if (!isset($_POST['mass_user_nonce']) || !wp_verify_nonce($_POST['mass_user_nonce'], 'mass_user_generate_action')) {
            wp_die('Security check failed. Please refresh the page and try again.');
        }

        $password = sanitize_text_field($_POST['password']);
        $gender   = sanitize_text_field($_POST['gender']);
        $count    = intval($_POST['count']);
        
        $allowed_roles = array('subscriber', 'author', 'editor');
        $role = (isset($_POST['user_role']) && in_array($_POST['user_role'], $allowed_roles, true)) ? sanitize_text_field($_POST['user_role']) : 'subscriber';

        if ($count > 100) {
            set_time_limit(0); 
        }

        $success_count = 0;
        $error_count   = 0;

        for ($i = 0; $i < $count; $i++) {
            $username = generate_unique_random_username();
            $name     = generate_accurate_random_name($gender);
            $bio      = generate_random_bio();

            $userdata = array(
                'user_login'    => $username,
                'user_pass'     => $password,
                'user_nicename' => $name,
                'display_name'  => $name,
                'user_email'    => $username . '@example.com',
                'user_url'      => 'http://example.com/' . $username,
                'description'   => $bio,
                'role'          => $role,
            );

            $user_id = wp_insert_user($userdata);

            if (is_wp_error($user_id)) {
                $error_count++;
            } else {
                $success_count++;
            }
        }

        if ($success_count > 0) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($success_count) . ' users generated successfully!</p></div>';
        }
        if ($error_count > 0) {
            echo '<div class="notice notice-error is-dismissible"><p>Failed to generate ' . esc_html($error_count) . ' users due to database collisions.</p></div>';
        }
    }

    // Handle Settings Update
    if (isset($_POST['save_mug_settings'])) {
        if (!isset($_POST['mug_settings_nonce']) || !wp_verify_nonce($_POST['mug_settings_nonce'], 'mug_save_settings_action')) {
            wp_die('Security check failed.');
        }
        
        $ga_id = sanitize_text_field($_POST['mug_ga_id']);
        update_option('mug_ga_tracking_id', $ga_id);
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
    }

    $current_ga_id = get_option('mug_ga_tracking_id', '');

    ?>
    <div class="wrap">
        <h2>User Generator</h2>
        <form method="post">
            <?php wp_nonce_field('mass_user_generate_action', 'mass_user_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="password">Password</label></th>
                    <td><input type="password" id="password" name="password" required class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gender">Gender</label></th>
                    <td>
                        <select id="gender" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="both">Both</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="user_role">User Role</label></th>
                    <td>
                        <select id="user_role" name="user_role">
                            <option value="subscriber">Subscriber</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="count">Number of Users</label></th>
                    <td><input type="number" id="count" name="count" min="1" max="5000" required class="small-text"></td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="generate_users" class="button button-primary">Generate Users</button>
            </p>
        </form>

        <hr>

        <h2>Plugin Settings</h2>
        <form method="post">
            <?php wp_nonce_field('mug_save_settings_action', 'mug_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="mug_ga_id">Google Analytics ID</label></th>
                    <td>
                        <input type="text" id="mug_ga_id" name="mug_ga_id" value="<?php echo esc_attr($current_ga_id); ?>" class="regular-text" placeholder="e.g., G-XXXXXXXXXX">
                        <p class="description">Leave blank to disable frontend tracking.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="save_mug_settings" class="button button-secondary">Save Settings</button>
            </p>
        </form>
    </div>
    <?php
}

function generate_unique_random_username() {
    $exists = true;
    while ($exists) {
        $username = strtolower(wp_generate_password(8, false));
        $exists = username_exists($username);
    }
    return $username;
}

function generate_accurate_random_name($gender) {
    $female_names = array('Alice', 'Emily', 'Fiona', 'Sarah', 'Jessica', 'Chloe', 'Mia');
    $male_names   = array('David', 'Bob', 'Charlie', 'John', 'Michael', 'James', 'William');
    $last_names   = array('Johnson', 'Smith', 'Brown', 'Davis', 'Wilson', 'Taylor', 'Anderson');

    if ($gender === 'female') {
        $first_names = $female_names;
    } elseif ($gender === 'male') {
        $first_names = $male_names;
    } else {
        $first_names = array_merge($female_names, $male_names);
    }

    $first_name = $first_names[array_rand($first_names)];
    $last_name  = $last_names[array_rand($last_names)];

    return $first_name . ' ' . $last_name;
}

function generate_random_bio() {
    $adjectives = array('Enthusiastic', 'Passionate', 'Adventurous', 'Tech-savvy', 'Creative');
    $hobbies    = array('Hiking', 'Reading', 'Photography', 'Cooking', 'Gaming');
    
    $random_adjective = $adjectives[array_rand($adjectives)];
    $random_hobby     = $hobbies[array_rand($hobbies)];

    return 'I am a ' . $random_adjective . ' ' . $random_hobby . ' enthusiast.';
}
