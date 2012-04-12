<?php

/**
 * @file
 * Contains theme override functions and process & preprocess functions
 */

// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('brutus_rebuild_registry')) {
  system_rebuild_theme_data();
  drupal_theme_rebuild();
}

/**
 * Implements template_html_head_alter();
 *
 * Changes the default meta content-type tag to the shorter HTML5 version
 */
function brutus_html_head_alter(&$head_elements) {
  $head_elements['system_meta_content_type']['#attributes'] = array(
    'charset' => 'utf-8'
  );
}

/**
 * Implements template_proprocess_search_block_form().
 *
 * Changes the search form to use the HTML5 "search" input attribute
 */
function brutus_preprocess_search_block_form(&$vars) {
  $vars['search_form'] = str_replace('type="text"', 'type="search"', $vars['search_form']);
}

/**
 * Implements template_preprocess().
 */
function brutus_preprocess(&$vars, $hook) {
  $vars['brutus_path'] = base_path() . path_to_theme();
}

/**
 * Implements template_preprocess_html().
 */
function brutus_preprocess_html(&$vars) {
  
  $vars['doctype'] = '<!DOCTYPE html>' . "\n";
  $vars['html5shim'] = '<!--[if lt IE 9]><script src="'. base_path() . path_to_theme() .'/scripts/html5forIE.js"></script><![endif]-->';
  
  if (isset($vars['node'])) {
    // For full nodes.
    $vars['classes_array'][] = ($vars['node']) ? 'full-node' : '';
    // For forums.
    $vars['classes_array'][] = (($vars['node']->type == 'forum') || (arg(0) == 'forum')) ? 'forum' : '';
  }
  else {
    // Forums.
    $vars['classes_array'][] = (arg(0) == 'forum') ? 'forum' : '';
  }
  if (module_exists('panels') && function_exists('panels_get_current_page_display')) {
    $vars['classes_array'][] = (panels_get_current_page_display()) ? 'panels' : '';
  }
  
  // Since menu is rendered in preprocess_page we need to detect it here to add body classes
  $has_main_menu = theme_get_setting('toggle_main_menu');
  $has_secondary_menu = theme_get_setting('toggle_secondary_menu');

  /* Add extra classes to body for more flexible theming */

  if ($has_main_menu or $has_secondary_menu) {
    $vars['classes_array'][] = 'with-navigation';
  }

  if ($has_secondary_menu) {
    $vars['classes_array'][] = 'with-subnav';
  }

  if (!empty($vars['page']['featured'])) {
    $vars['classes_array'][] = 'featured';
  }

  if ($vars['is_admin']) {
    $vars['classes_array'][] = 'admin';
  }

  if (!empty($vars['page']['sidebar_first'])) {
    $vars['classes_array'][] = 'two-sidebar';
  }
  if (!empty($vars['page']['sidebar_second'])) {
    $vars['classes_array'][] = 'two-sidebar';
  }
  if (!empty($vars['page']['sidebar_first'])) {
    $vars['classes_array'][] = 'sidebar-left';
  }

  if (!empty($vars['page']['sidebar_second'])) {
    $vars['classes_array'][] = 'sidebar-right';
  }

  if (!empty($vars['page']['header_top'])) {
    $vars['classes_array'][] = 'header_top';
  }

  if ($vars['is_admin']) {
    $vars['classes_array'][] = 'admin';
  }

  if (!$vars['is_front']) {
    // Add unique classes for each page and website section
    $path = drupal_get_path_alias($_GET['q']);
    $temp = explode('/', $path, 2);
    $section = array_shift($temp);
    $page_name = array_shift($temp);

    if (isset($page_name)) {
      $vars['classes_array'][] = brutus_id_safe('page-' . $page_name);
    }

    $vars['classes_array'][] = brutus_id_safe('section-' . $section);

    // add template suggestions
    $vars['theme_hook_suggestions'][] = "page__section__" . $section;
    $vars['theme_hook_suggestions'][] = "page__" . $page_name;

    if (arg(0) == 'node') {
      if (arg(1) == 'add') {
        if ($section == 'node') {
          array_pop($vars['classes_array']); // Remove 'section-node'
        }
        $vars['classes_array'][] = 'section-node-add'; // Add 'section-node-add'
      } elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
        if ($section == 'node') {
          array_pop($vars['classes_array']); // Remove 'section-node'
        }
        $vars['classes_array'][] = 'section-node-' . arg(2); // Add 'section-node-edit' or 'section-node-delete'
      }
    }
  }
}

/**
 * Page preprocessing
 */
function brutus_preprocess_page(&$vars, $hook) {
  if (isset($vars['node_title'])) {
    $vars['title'] = $vars['node_title'];
  }
  // Site navigation links.
  $vars['main_menu_links'] = '';
  if (isset($vars['main_menu'])) {
    $vars['main_menu_links'] = theme('links__system_main_menu', array(
      'links' => $vars['main_menu'],
      'attributes' => array(
        'id' => 'main-menu',
        'class' => array('inline', 'main-menu'),
      ),
      'heading' => array(
        'text' => t('Main menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    ));
  }
  $vars['secondary_menu_links'] = '';
  if (isset($vars['secondary_menu'])) {
    $vars['secondary_menu_links'] = theme('links__system_secondary_menu', array(
      'links' => $vars['secondary_menu'],
      'attributes' => array(
        'id'    => 'secondary-menu',
        'class' => array('inline', 'secondary-menu'),
      ),
      'heading' => array(
        'text' => t('Secondary menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    ));
  }
  // Adding classes wether #navigation is here or not
  if (!empty($vars['main_menu']) or !empty($vars['sub_menu'])) {
    $vars['classes_array'][] = 'with-navigation';
  }
  if (!empty($vars['secondary_menu'])) {
    $vars['classes_array'][] = 'with-subnav';
  }
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($vars['title_suffix']['add_or_remove_shortcut']) && $vars['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $vars['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $vars['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $vars['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
  
  if(!theme_get_setting('brutus_feed_icons')) {
    $vars['feed_icons'] = '';
  }

  // Add first/last classes to node listings about to be rendered.
  if (isset($vars['page']['content']['system_main']['nodes'])) {
    // All nids about to be loaded (without the #sorted attribute).
    $nids = element_children($vars['page']['content']['system_main']['nodes']);
    // Only add first/last classes if there is more than 1 node being rendered.
    if (count($nids) > 1) {
      $first_nid = reset($nids);
      $last_nid = end($nids);
      $first_node = $vars['page']['content']['system_main']['nodes'][$first_nid]['#node'];
      $first_node->classes_array = array('first');
      $last_node = $vars['page']['content']['system_main']['nodes'][$last_nid]['#node'];
      $last_node->classes_array = array('last');
    }
  }
}
  

/**
 * Node preprocessing
 */
function brutus_preprocess_node(&$vars) {

 // Add to array of handy node classes
  $vars['classes_array'][] = $vars['zebra'];                              // Node is odd or even
  $vars['classes_array'][] = (!$vars['teaser']) ? 'full-node' : '';       // Node is teaser or full-node

  $node_top_blocks = block_get_blocks_by_region('node_top');
  $node_bottom_blocks = block_get_blocks_by_region('node_bottom');
  if ($node_top_blocks) {
    $vars['node_top'] = $node_top_blocks; 
  }
  if ($node_bottom_blocks) {
    $vars['node_bottom'] = $node_bottom_blocks;
  }

  // Node is published
  $vars['classes_array'][] = ($vars['status']) ? 'published' : 'unpublished';

  // Node has comments?
  $vars['classes_array'][] = ($vars['comment']) ? 'with-comments' : 'no-comments';

  if ($vars['sticky']) {
    $vars['classes_array'][] = 'sticky'; // Node is sticky
  }

  if ($vars['promote']) {
    $vars['classes_array'][] = 'promote'; // Node is promoted to front page
  }

  if ($vars['uid'] && $vars['uid'] === $GLOBALS['user']->uid) {
    $classes[] = 'node-mine'; // Node is authored by current user.
  }
  
  $vars['submitted'] = t('Submitted by !username on', array('!username' => $vars['name']));
  $vars['submitted_date'] = t('!datetime', array('!datetime' => $vars['date']));
  $vars['submitted_pubdate'] = format_date($vars['created'], 'custom', 'Y-m-d\TH:i:s');
}

/**
 * Views preprocessing
 * Add view type class (e.g., node, teaser, list, table)
 */
function brutus_preprocess_views_view(&$vars) {
  $vars['css_name'] = $vars['css_name'] . ' view-style-' . drupal_clean_css_identifier(strtolower($vars['view']->plugin_name));
}

/*
 *	This function create the EDIT LINKS for blocks and menus blocks.
 *	When overing a block (except in IE6), some links appear to edit
 *	or configure the block. You can then edit the block, and once you are
 *	done, brought back to the first page.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called ("block" in this case.)
 */ 

function brutus_preprocess_block(&$vars, $hook) {
  // Add a striping class.
  $vars['classes_array'][] = 'block-' . $vars['block_zebra'];

  // Add first/last block classes
  $first_last = "";
  // If block id (count) is 1, it's first in region.
  if ($vars['block_id'] == '1') {
    $first_last = "first";
    $vars['classes_array'][] = $first_last;
  }
  // Count amount of blocks about to be rendered in that region.
  $block_count = count(block_list($vars['elements']['#block']->region));
  if ($vars['block_id'] == $block_count) {
    $first_last = "last";
    $vars['classes_array'][] = $first_last;
  }
  if ($vars['block']->region == 'header') {
    $vars['title_attributes_array']['class'][] = 'element-invisible';
  }
}

/**
 * Implements theme_menu_tree().
 */
function brutus_menu_tree($vars) {
  return '<ul class="menu clearfix">' . $vars['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().
 */
function brutus_field__taxonomy_term_reference($vars) {
  $output = '';

  // Render the label, if it's not hidden.
  if (!$vars['label_hidden']) {
    $output .= '<h3 class="field-label">' . $vars['label'] . ': </h3>';
  }

  // Render the items.
  $output .= ( $vars['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
  foreach ($vars['items'] as $delta => $item) {
    $output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $vars['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }
  $output .= '</ul>';

  // Render the top-level DIV.
  $output = '<div class="' . $vars['classes'] . (!in_array('clearfix', $vars['classes_array']) ? ' clearfix' : '') . '">' . $output . '</div>';

  return $output;
}

function brutus_id_safe($string) {
  // Strip accents
  $accents = '/&([A-Za-z]{1,2})(tilde|grave|acute|circ|cedil|uml|lig);/';
  $string = preg_replace($accents, '$1', htmlentities(utf8_decode($string)));
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id' . $string;
  }
  return $string;
}

/**
 * Generate doctype for templates
 */
function _brutus_doctype() {
  return '<!DOCTYPE html>' . "\n";
}

/**
 * Generate the HTML output for a menu link and submenu.
 *
 * @param $vars
 *   An associative array containing:
 *   - element: Structured array data for a menu link.
 *
 * @return
 *   A themed HTML string.
 *
 * @ingroup themeable
 */
function brutus_menu_link(array $vars) {
  $element = $vars['element'];
  $sub_menu = '';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  // Adding a class depending on the TITLE of the link (not constant)
  //$element['#attributes']['id'][] = brutus_id_safe($element['#title']);
  $element['#attributes']['class'][] = brutus_id_safe($element['#title']);
  // Adding a class depending on the ID of the link (constant)
  $element['#attributes']['class'][] = 'mid-' . $element['#original_link']['mlid'];
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

function brutus_preprocess_menu_local_task(&$vars) {
  $link = & $vars['element']['#link'];

  // If the link does not contain HTML already, check_plain() it now.
  // After we set 'html'=TRUE the link will not be sanitized by l().
  if (empty($link['localized_options']['html'])) {
    $link['title'] = check_plain($link['title']);
  }

  $link['localized_options']['html'] = TRUE;
  $link['title'] = '<span class="tab">' . $link['title'] . '</span>';
}

/**
 *  Duplicate of theme_menu_local_tasks() but adds clearfix to tabs.
 */
function brutus_menu_local_tasks(&$vars) {
  $output = '';

  if (!empty($vars['primary'])) {
    $vars['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $vars['primary']['#prefix'] .= '<ul class="tabs primary clearfix">';
    $vars['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['primary']);
  }

  if (!empty($vars['secondary'])) {
    $vars['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $vars['secondary']['#prefix'] .= '<ul class="tabs secondary clearfix">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
  }

  return $output;
}

// Add Zen Tabs styles
if (theme_get_setting('brutus_tabs')) {
  drupal_add_css( drupal_get_path('theme', 'brutus') . '/css/tabs.css');
}

/*
 *	 This function creates the body classes that are relative to each page
 *	
 *	@param $vars
 *	  A sequential array of variables to pass to the theme template.
 *	@param $hook
 *	  The name of the theme function being called ("page" in this case.)
 */


function brutus_preprocess_comment_wrapper(&$vars) {
  $classes = array();
  $classes[] = 'comment-wrapper';
  
  // Provide skinr support.
/*  if (module_exists('skinr')) {
    $classes[] = $vars['skinr'];
}*/
  $vars['classes'] = implode(' ', $classes);
}



/**
 * Comment preprocessing
 */
function brutus_core_preprocess_comment(&$vars) {
  static $comment_odd = TRUE;                                                                             // Comment is odd or even

  // Add to array of handy comment classes
  $vars['classes_array'][] = $comment_odd ? 'odd' : 'even';
  $comment_odd = !$comment_odd;
}

/**
* Return a themed breadcrumb trail.
*
* @param $breadcrumb
* An array containing the breadcrumb links.
* @return
* A string containing the breadcrumb output.
*/
function brutus_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  // Determine if we are to display the breadcrumb.
  $show_breadcrumb = theme_get_setting('brutus_breadcrumb');
  if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = theme_get_setting('brutus_breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    // Return the breadcrumb with separators.
    if (!empty($breadcrumb)) {
      $breadcrumb_separator = theme_get_setting('brutus_breadcrumb_separator');
      $trailing_separator = $title = '';
      if (theme_get_setting('brutus_breadcrumb_title')) {
        $item = menu_get_item();
        if (!empty($item['tab_parent'])) {
          // If we are on a non-default tab, use the tab's title.
          $title = check_plain($item['title']);
        }
        else {
          $title = drupal_get_title();
        }
        if ($title) {
          $trailing_separator = $breadcrumb_separator;
        }
      }
      elseif (theme_get_setting('brutus_breadcrumb_trailing')) {
        $trailing_separator = $breadcrumb_separator;
      }

      // Provide a navigational heading to give context for breadcrumb links to
      // screen-reader users. Make the heading invisible with .element-invisible.
      $heading = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

      return $heading . '<div class="breadcrumb">' . implode($breadcrumb_separator, $breadcrumb) . $trailing_separator . $title . '</div>';
    }
  }
  // Otherwise, return an empty string.
  return '';
}

/**
 * Search result preprocessing
 */
function brutus_preprocess_search_result(&$vars) {
  static $search_zebra = 'even';

  $search_zebra = ($search_zebra == 'even') ? 'odd' : 'even';
  $vars['search_zebra'] = $search_zebra;
  $result = $vars['result'];
  $vars['url'] = check_url($result['link']);
  $vars['title'] = check_plain($result['title']);

  // Check for snippet existence. User search does not include snippets.
  $vars['snippet'] = '';
  if (isset($result['snippet']) && theme_get_setting('search_snippet')) {
    $vars['snippet'] = $result['snippet'];
  }

  $info = array();
  if (!empty($result['type']) && theme_get_setting('search_info_type')) {
    $info['type'] = check_plain($result['type']);
  }
  if (!empty($result['user']) && theme_get_setting('search_info_user')) {
    $info['user'] = $result['user'];
  }
  if (!empty($result['date']) && theme_get_setting('search_info_date')) {
    $info['date'] = format_date($result['date'], 'small');
  }
  if (isset($result['extra']) && is_array($result['extra'])) {
    // $info = array_merge($info, $result['extra']);  Drupal bug?  [extra] array not keyed with 'comment' & 'upload'
    if (!empty($result['extra'][0]) && theme_get_setting('search_info_comment')) {
      $info['comment'] = $result['extra'][0];
    }
    if (!empty($result['extra'][1]) && theme_get_setting('search_info_upload')) {
      $info['upload'] = $result['extra'][1];
    }
  }

  // Provide separated and grouped meta information.
  $vars['info_split'] = $info;
  $vars['info'] = implode(' - ', $info);

  // Provide alternate search result template.
  $vars['template_files'][] = 'search-result-' . $vars['module'];
}
