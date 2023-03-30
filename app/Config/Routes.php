<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Site');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


################ Admin Start ################
$routes->get('{locale}/admin', 'Admin::index');

################ Admin End ################


################ School Start ################
$routes->get('{locale}', 'Site::index');
################ School End ################


$routes->get('/school', 'School::index');

$routes->get('{locale}/lang/(:any)', 'Language::set_lang_cookie/$1');
$routes->add('{locale}/site/is-cat-available-for-me', 'Site::bookingscreen1');
$routes->add('{locale}/site/signup-o-login', 'Site::bookingscreen2');
$routes->add('{locale}/site/get-the-right-level', 'Site::bookingscreen3');
$routes->add('{locale}/site/get-the-right-primary-level', 'Site::bookingscreen2a');
$routes->add('{locale}/site/recommended-primary-level', 'Site::bookingscreen2p');

// To Ignore 404 error in server
$routes->get('{locale}/site/contact',   function () {return view('errors/html/error_404');});
$routes->get('{locale}/pages/features', function () {return view('errors/html/error_404');});
$routes->get('{locale}/pages/courses',  function () {return view('errors/html/error_404');});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
