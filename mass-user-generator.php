<?php
/*
Plugin Name: Mass User Generator
Plugin URI: https://github.com/aamirpali/Mass-User-Generator-WordPress
Description: A simple WordPress plugin to generate multiple users with options.
Version: 1.0
Author: Ameer Baksh
Author URI: https://ameerbaksh.cloud/ 
*/


include plugin_dir_path(__FILE__) . 'include/init.php';
// Add an admin menu option
function mass_user_generator_menu() {
    add_menu_page('User Generator', 'User Generator', 'manage_options', 'user-generator', 'user_generator_page');
}

add_action('admin_menu', 'mass_user_generator_menu');

// Define the user generator page
function user_generator_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_POST['generate_users'])) {
        $password = sanitize_text_field($_POST['password']);
        $gender = sanitize_text_field($_POST['gender']);
        $count = intval($_POST['count']);

        for ($i = 0; $i < $count; $i++) {
            $username = generate_random_username();
            $name = generate_random_name($gender);
            $bio = generate_random_bio();

            $userdata = array(
                'user_login' => $username,
                'user_pass' => $password,
                'user_nicename' => $name,
                'display_name' => $name,
                'user_email' => $username . '@example.com',
                'user_url' => 'http://example.com/' . $username,
                'description' => $bio,
                'role' => 'subscriber',
            );

            wp_insert_user($userdata);
        }

        echo '<div class="updated"><p>' . $count . ' users generated successfully!</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h2>User Generator</h2>';
    echo '<form method="post">';
echo '<div class="form-group">';
echo '<label for="password">Password</label>';
echo '<input type="password" id="password" name="password" required class="form-control">';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="gender">Gender</label>';
echo '<select id="gender" name="gender" required class="form-control">';
echo '<option value="male">Male</option>';
echo '<option value="female">Female</option>';
echo '<option value="both">Both</option>';
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="user_role">User Role</label>';
echo '<select id="user_role" name="user_role" class="form-control">';
echo '<option value="subscriber">Subscriber</option>';
echo '<option value="author">Author</option>';
echo '<option value="editor">Editor</option>';
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="count">Number of Users</label>';
echo '<input type="number" id="count" name="count" min="5" max="5000" required class="form-control">';
echo '</div>';
echo '<button type="submit" name="generate_users" class="btn btn-primary">Generate Users</button>';
echo '</form>';

    echo '</div>';
}

// Function to generate a random username
function generate_random_username() {
    return strtolower(wp_generate_password(8, false));
}

// Function to generate a random name
function generate_random_name($gender) {
    $first_names = ($gender === 'female') ? array('Alice', 'Bob', 'Charlie') : array('David', 'Emily', 'Fiona');
    $last_names = array('Johnson', 'Smith', 'Brown');

    $first_name = $first_names[array_rand($first_names)];
    $last_name = $last_names[array_rand($last_names)];

    return $first_name . ' ' . $last_name;
}

// Function to generate a random bio
function generate_random_bio() {
    $adjectives = array('Enthusiastic', 'Passionate', 'Adventurous', 'Tech-savvy', 'Creative');
    $hobbies = array('Hiking', 'Reading', 'Photography', 'Cooking', 'Gaming');
    $random_adjective = $adjectives[array_rand($adjectives)];
    $random_hobby = $hobbies[array_rand($hobbies)];

    return 'I am a ' . $random_adjective . ' ' . $random_hobby . ' enthusiast.';
}
