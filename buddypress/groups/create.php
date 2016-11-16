<?php /**
 * Create a group
 *
 */ ?>
<div class="col-sm-18">

    <?php
    // re-direct to courses page if user does not have permissions for course creation page
    $account_type = xprofile_get_field_data('Account Type', get_current_user_id());
    $group_type = isset($_GET['type']) ? $_GET['type'] : 'club';
    if ('course' === $group_type && !is_super_admin() && $account_type != "Faculty") {
        wp_redirect(home_url('courses'));
    }

    global $bp;

    //get group type
    if (!empty($_GET['type'])) {
        $group_type = $_GET['type'];
    } else {
        $group_type = 'club';
    }

    //this function doesn't work - explore for deprecation or fixing
    /* $group_type = openlab_get_current_group_type(); */

    // Set a group label. The (e)Portfolio logic means we have to do an extra step
    if ('portfolio' == $group_type) {
        $group_label = openlab_get_portfolio_label('case=upper&user_id=' . bp_loggedin_user_id());
        $page_title = 'Create ' . openlab_get_portfolio_label('case=upper&leading_a=1&user_id=' . bp_loggedin_user_id());
    } else {
        $group_label = $group_type;
        $page_title = 'Create a ' . ucwords($group_type);
    }

    $group_id_to_clone = 0;
    if ('course' === $group_type && !empty($_GET['clone'])) {
        $group_id_to_clone = intval($_GET['clone']);
    }
    ?>
    <h1 class="entry-title mol-title"><?php bp_loggedin_user_fullname() ?>'s Profile</h1>
    <?php
    // get account type to see if they're faculty
    $faculty = xprofile_get_field_data('Account Type', get_current_user_id());
    ?>

    <?php echo openlab_create_group_menu($group_type); ?>

    <div id="single-course-body" class="<?php echo ( 'course' == $group_type ? 'course-create' : '' ); ?>">
        <div id="openlab-main-content"></div>

        <form action="<?php bp_group_creation_form_action() ?>" method="post" id="create-group-form" class="standard-form form-panel" enctype="multipart/form-data">

            <?php do_action('bp_before_create_group') ?>

            <?php do_action('template_notices') ?>

	    <input type="hidden" id="new-group-type" value="<?php echo esc_attr( $group_type ); ?>" />

                <?php /* Group creation step 1: Basic group details */ ?>
                <?php if (bp_is_group_creation_step('group-details')) : ?>

                    <?php do_action('bp_before_group_details_creation_step'); ?>

                    <?php /* Create vs Clone for Courses */ ?>
                    <?php if ('course' == $group_type) : ?>
                        <div class="panel panel-default create-or-clone-selector">
                            <div class="panel-heading semibold">Create New or Clone Existing?</div>
                            <div class="panel-body">
                            <p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-2">If you taught the same course in a previous semester or year, cloning can save you time.</p>

                            <ul class="create-or-clone-options">
                                <li class="radio">
                                    <label for="create-or-clone-create"><input type="radio" name="create-or-clone" id="create-or-clone-create" value="create" <?php checked(!(bool) $group_id_to_clone) ?> />
                                        Create a New Course</label>
                                </li>

                                <?php
                                //this is to see if the user has an courses under My Courses - if not, the Clone an Existing Course option is disabled
                                $filters['wds_group_type'] = $group_type;
                                $group_args = array(
                                    'per_page' => 12,
                                    'show_hidden' => true,
                                    'user_id' => $bp->loggedin_user->id
                                );

                                $course_num = openlab_group_post_count($filters, $group_args);
                                ?>

                                <li class="disable-if-js form-group radio form-inline">
                                    <label for="create-or-clone-clone" <?php echo ($course_num < 1 ? 'class="disabled-opt"' : ''); ?>><input type="radio" name="create-or-clone" id="create-or-clone-clone" value="clone" <?php checked((bool) $group_id_to_clone) ?> <?php echo ($course_num < 1 ? 'disabled' : ''); ?> />
                                        Clone an Existing Course</label>

                                    <?php $user_groups = openlab_get_courses_owned_by_user(get_current_user_id()) ?>
                                    
                                    <label class="sr-only" for="group-to-clone">Choose a Course</label>
                                    <select class="form-control" id="group-to-clone" name="group-to-clone">
                                        <option value="" <?php selected($group_id_to_clone, 0) ?>>- choose a course -</option>

                                        <?php foreach ($user_groups['groups'] as $user_group) : ?>
                                            <option value="<?php echo esc_attr($user_group->id) ?>" <?php selected($group_id_to_clone, $user_group->id) ?>><?php echo esc_attr($user_group->name) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </li>
                            </ul>

                            <p class="ol-clone-description italics" id="ol-clone-description">Note: The cloned course will copy the course profile, site set-up, and all documents, files, discussions and posts you've created. Posts will be set to "draft" mode. The cloned course will not copy course membership or member-created documents, files, discussions, comments or posts.</p>
                            </div>
                        </div>

                    <?php endif; ?>

                    <?php /* Name/Description */ ?>

                    <div class="panel panel-default">
                        <div class="panel-heading semibold"><label for="group-name"><?php echo ucfirst($group_type); ?> Name <?php _e('(required)', 'buddypress') ?></label></div>
                            <div class="panel-body">
                    <?php if ('course' == $group_type) : ?>
                        <p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-4">Please take a moment to consider the name of your new or cloned Course. We recommend keeping your Course Name under 50 characters. You can always change it later. We recommend the following format:</p>
                        <ul class="ol-tooltip" id="clone-course-tooltip-3">
                            <li>CourseCode CourseName, Semester Year</li>
                            <li>ARCH3522 NYC Arch, FA2013</li>
                        </ul>

                        <input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" placeholder="Course Name" required />

                    <?php elseif ('portfolio' == $group_type) : ?>
                        <p class="ol-tooltip">The suggested <?php echo $group_label ?> Name below uses your first and last name. If you do not wish to use your full name, you may change it now or at any time in the future.</p>

                        <ul class="ol-tooltip">
                            <li>FirstName LastName's <?php echo $group_label ?> </li>
                            <li>Jane Smith's <?php echo $group_label ?> (Example)</li>
                        </ul>

                        <input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" required />

                    <?php else : ?>
                        <p class="ol-tooltip">Please take a moment to consider the name of your <?php echo ucwords($group_type) ?>.  Choosing a name that clearly identifies your  <?php echo ucwords($group_type) ?> will make it easier for others to find your <?php echo ucwords($group_type) ?> profile. We recommend keeping your  <?php echo ucwords($group_type) ?> name under 50 characters.</p>
                        <input class="form-control" size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" required />

                    <?php endif ?>
                            </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading semibold"><label for="group-desc"><?php echo ucfirst($group_type); ?> Description <?php _e('(required)', 'buddypress') ?></label></div>
                        <div class="panel-body">
                            <textarea class="form-control" name="group-desc" id="group-desc" required><?php bp_new_group_description() ?></textarea>
                        </div>
                    </div>

                    <?php do_action('bp_after_group_details_creation_step') ?>

                    <?php wp_nonce_field('groups_create_save_group-details') ?>

                <?php endif; ?>

                <?php /* Group creation step 2: Group settings */ ?>
                <?php if (bp_is_group_creation_step('group-settings')) : ?>

                    <?php do_action('bp_before_group_settings_creation_step'); ?>

                    <?php if ( function_exists( 'bbpress' ) && ! openlab_is_portfolio() ) : ?>
                        <input type="hidden" name="group-show-forum" value="1" />
                    <?php endif; ?>

                    <?php openlab_group_privacy_settings($group_type); ?>

                <?php endif; ?>

                <?php /* Group creation step 3: Avatar Uploads */ ?>

                <?php if (bp_is_group_creation_step('group-avatar')) : ?>

                    <?php do_action('bp_before_group_avatar_creation_step'); ?>

    <?php if (!bp_get_avatar_admin_step() || 'upload-image' == bp_get_avatar_admin_step()) : ?>

                        <div class="panel panel-default">
                        <div class="panel-heading">Upload Avatar</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div id="avatar-wrapper">
                                    <?php bp_new_group_avatar() ?>
                                </div>
                            </div>
                            <div class="col-sm-16">

                                <p class="italics"><?php _e("Upload an image to use as an avatar for this " . $group_type . ". The image will be shown on the main " . $group_type . " page, and in search results.", 'buddypress') ?></p>

                                <p id="avatar-upload">
                                    <div class="form-group form-inline">
                                            <div class="form-control type-file-wrapper">
                                                <input type="file" name="file" id="file" />
                                            </div>
                                            <input class="btn btn-primary top-align" type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
                                            <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                    </div>
                                </p>

                                <p class="italics">To skip the avatar upload process, click the "Next Step" button.</p>
                            </div>
                        </div>
                </div>
                        </div>

                    <?php endif; ?>

    <?php if ('crop-image' == bp_get_avatar_admin_step()) : ?>

                        <div class="panel panel-default">
                        <div class="panel-heading">Crop Avatar</div>
                        <div class="panel-body">

                            <img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e('Avatar to crop', 'buddypress') ?>" />

                            <div id="avatar-crop-pane">
                                <img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e('Avatar preview', 'buddypress') ?>" />
                            </div>

                            <input class="btn btn-primary" type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e('Crop Image', 'buddypress') ?>" />

                            <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
                            <input type="hidden" name="upload" id="upload" />
                            <input type="hidden" id="x" name="x" />
                            <input type="hidden" id="y" name="y" />
                            <input type="hidden" id="w" name="w" />
                            <input type="hidden" id="h" name="h" />

                        </div>
                        </div>

                    <?php endif; ?>

                    <?php do_action('bp_after_group_avatar_creation_step'); ?>

                    <?php wp_nonce_field('groups_create_save_group-avatar') ?>

                <?php endif; ?>

                <?php /* Group creation step 4: Invite friends to group */ ?>
                <?php if (bp_is_group_creation_step('group-invites')) : ?>

                    <?php do_action('bp_before_group_invites_creation_step'); ?>

    <?php if (function_exists('bp_get_total_friend_count') && bp_get_total_friend_count(bp_loggedin_user_id())) : ?>
                        <div class="left-menu">
                            <div id="invite-list">
                                <ul>
        <?php bp_new_group_invite_friend_list() ?>
                                </ul>

        <?php wp_nonce_field('groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user'); ?>
                            </div>
                        </div><!-- .left-menu -->

                        <div class="main-column">

                            <div id="message" class="info">
                                <p><?php _e('Select people to invite from your friends list.', 'buddypress'); ?></p>
                            </div>

                                <?php /* The ID 'friend-list' is important for AJAX support. */ ?>
                            <ul id="friend-list" class="item-list">
                                <?php if (bp_group_has_invites()) : ?>
            <?php while (bp_group_invites()) : bp_group_the_invite(); ?>

                                        <li id="<?php bp_group_invite_item_id() ?>">
                <?php bp_group_invite_user_avatar() ?>

                                            <h4><?php bp_group_invite_user_link() ?></h4>
                                            <span class="activity"><?php bp_group_invite_user_last_active() ?></span>

                                            <div class="action">
                                                <a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e('Remove Invite', 'buddypress') ?></a>
                                            </div>
                                        </li>

                                    <?php endwhile; ?>

                                    <?php wp_nonce_field('groups_send_invites', '_wpnonce_send_invites') ?>
        <?php endif; ?>
                            </ul>

                        </div><!-- .main-column -->

    <?php else : ?>
                        <div id="message" class="info">
                            <p><?php _e('Once you have built up friend connections you will be able to invite others to your ' . $group_type . '. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new ' . $group_type . '.', 'buddypress'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php wp_nonce_field('groups_create_save_group-invites') ?>
                    <?php do_action('bp_after_group_invites_creation_step'); ?>

                <?php endif; ?>

                <?php do_action('groups_custom_create_steps') // Allow plugins to add custom group creation steps  ?>

                <?php do_action('bp_before_group_creation_step_buttons'); ?>

                    <?php if ('crop-image' != bp_get_avatar_admin_step()) : ?>
                        <?php /* Previous Button */ ?>
                        <?php if ( ! bp_is_first_group_creation_step() && 'group-settings' !== bp_get_groups_current_create_step() ) : ?>
                            <input class="btn btn-primary prev-btn btn-margin btn-margin-top" type="button" value="&#xf137; <?php _e('Previous Step', 'buddypress') ?>" id="group-creation-previous" name="previous" onclick="location.href = '<?php bp_group_creation_previous_link() ?>'" />
                        <?php endif; ?>

                        <?php /* Next Button */ ?>
                        <?php if (!bp_is_last_group_creation_step() && !bp_is_first_group_creation_step()) : ?>
                            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e('Next Step', 'buddypress') ?> &#xf138;" id="group-creation-next" name="save" />
                        <?php endif; ?>

                        <?php /* Create Button */ ?>
                        <?php if (bp_is_first_group_creation_step()) : ?>
                            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e('Create ' . ucfirst($group_type) . ' and Continue ', 'buddypress'); ?> &#xf138;" id="group-creation-create" name="save" />
                        <?php endif; ?>

                        <?php /* Finish Button */ ?>
                        <?php if (bp_is_last_group_creation_step()) : ?>
                            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" value="<?php _e('Finish', 'buddypress') ?> &#xf138;" id="group-creation-finish" name="save" />
                    <?php endif; ?>
                <?php endif; ?>

                <?php do_action('bp_after_group_creation_step_buttons'); ?>

<?php /* Don't leave out this hidden field */ ?>
                <input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" />

<?php do_action('bp_directory_groups_content') ?>

<?php do_action('bp_after_create_group') ?>

        </form>
    </div>
</div>
<?php openlab_bp_sidebar('members'); ?>
