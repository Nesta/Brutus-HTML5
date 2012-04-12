<?php

/**
 * @file
 * Contains theme override functions and process & preprocess functions for ninesixtyfive
 */

function brutus_form_system_theme_settings_alter(&$form, $form_state) {

  /*
   * Create the form using Forms API
   */

  $form['brutus_zen_tabs'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use Zen Tabs'),
    '#default_value' => theme_get_setting('brutus_zen_tabs'),
    '#description'   => t('Replace the default tabs by the Zen Tabs.'),
    '#prefix'        => '<strong>' . t('Zen Tabs:') . '</strong>',
  );

  $form['brutus_breadcrumb'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Breadcrumb settings'),
    '#attributes'    => array('id' => 'brutus-breadcrumb'),
  );
  $form['brutus_breadcrumb']['brutus_breadcrumb'] = array(
    '#type'          => 'select',
    '#title'         => t('Display breadcrumb'),
    '#default_value' => theme_get_setting('brutus_breadcrumb'),
    '#options'       => array(
                          'yes'   => t('Yes'),
                          'admin' => t('Only in admin section'),
                          'no'    => t('No'),
                        ),
  );
  $form['brutus_breadcrumb']['brutus_breadcrumb_separator'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Breadcrumb separator'),
    '#description'   => t('Text only. Don’t forget to include spaces.'),
    '#default_value' => theme_get_setting('brutus_breadcrumb_separator'),
    '#size'          => 5,
    '#maxlength'     => 10,
    '#prefix'        => '<div id="div-brutus-breadcrumb-collapse">', // jquery hook to show/hide optional widgets
  );
  $form['brutus_breadcrumb']['brutus_breadcrumb_home'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Show home page link in breadcrumb'),
    '#default_value' => theme_get_setting('brutus_breadcrumb_home'),
  );
  $form['brutus_breadcrumb']['brutus_breadcrumb_trailing'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Append a separator to the end of the breadcrumb'),
    '#default_value' => theme_get_setting('brutus_breadcrumb_trailing'),
    '#description'   => t('Useful when the breadcrumb is placed just before the title.'),
  );
  $form['brutus_breadcrumb']['brutus_breadcrumb_title'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Append the content title to the end of the breadcrumb'),
    '#default_value' => theme_get_setting('brutus_breadcrumb_title'),
    '#description'   => t('Useful when the breadcrumb is not placed just before the title.'),
    '#suffix'        => '</div>', // #div-zen-breadcrumb
  );

 // Search Settings
  if (module_exists('search')) {
    $form['brutus_search_container'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search results'),
      '#description' => t('What additional information should be displayed on your search results page?'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['brutus_search_container']['search_results']['search_snippet'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display text snippet'),
      '#default_value' => theme_get_setting('search_snippet'),
    );
    $form['brutus_search_container']['search_results']['search_info_type'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display content type'),
      '#default_value' => theme_get_setting('search_info_type'),
    );
    $form['brutus_search_container']['search_results']['search_info_user'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display author name'),
      '#default_value' => theme_get_setting('search_info_user'),
    );
    $form['brutus_search_container']['search_results']['search_info_date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display posted date'),
      '#default_value' => theme_get_setting('search_info_date'),
    );
    $form['brutus_search_container']['search_results']['search_info_comment'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display comment count'),
      '#default_value' => theme_get_setting('search_info_comment'),
    );
    $form['brutus_search_container']['search_results']['search_info_upload'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display attachment count'),
      '#default_value' => theme_get_setting('search_info_upload'),
    );
  }
}
