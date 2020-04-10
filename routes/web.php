<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});
Route::resource('quotes','QuoteController')->middleware('auth');
Route::resource('products','ProductController')->middleware('auth');
Route::resource('accounts','AccountController')->middleware('auth');
Route::resource('category','CategoryController')->middleware('auth');
Route::resource('place','PlaceController')->middleware('auth');
Route::resource('users','UserController')->middleware('auth');
Route::resource('locations','LocationController')->middleware('auth');
Route::resource('documentTypes','DocumentTypeController')->middleware('auth');
Route::resource('recordTypes','RecordTypeController')->middleware('auth');
Route::resource('reports','ReportController')->middleware('auth');
Route::resource('photos', 'PhotoController')->only([
    'index', 'show', 'edit', 'update'
])->middleware('auth');
Route::resource('organizations', 'OrganizationController')->except([
    'create', 'store', 'destroy'
])->middleware('auth');


// Authentication Routes...
// $this->get('admin/login', 'Auth\LoginController@showLoginForm')->name('login');
// $this->post('admin/login', 'Auth\LoginController@login');
// $this->post('admin/logout', 'Auth\LoginController@logout')->name('logout');

// // Registration Routes...
// $this->get('admin/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// $this->post('admin/register', 'Auth\RegisterController@register');

// // Password Reset Routes...
// $this->get('admin/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// $this->post('admin/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// $this->get('admin/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// $this->post('admin/password/reset', 'Auth\ResetPasswordController@reset');

// route to show the login form
// Route::get('login', 'LoginController@showLogin');
// Route::post('login/checklogin', 'MainController@checklogin');
// Route::get('login/successlogin', 'MainController@successlogin');
// Route::post('login/logout', 'LoginController@logout');
Route::post('/account/searchedData', 'QuoteController@findAccount')->middleware('auth');
Route::post('/quote/findAccount', 'QuoteController@findAccount')->middleware('auth');
Route::post('/quote/findProduct', 'QuoteController@findProduct')->middleware('auth');
Route::post('/quote/startSale', 'QuoteController@startSale')->middleware('auth');
Route::post('/quote/finishSale', 'QuoteController@finishSale')->middleware('auth');
Route::post('/quote/choiceProduct', 'QuoteController@choiceProduct')->middleware('auth');
Route::post('/quote/getSecondCategory', 'QuoteController@getSecondCategory')->middleware('auth');
Route::post('/quote/addProduct', 'QuoteController@addProduct')->middleware('auth');
Route::post('/quote/loadQuoteDetail', 'QuoteController@loadQuoteDetail')->middleware('auth');
Route::post('/quote/removeItem', 'QuoteController@removeItem')->middleware('auth');
Route::post('/quote/editQuoteDetail', 'QuoteController@editQuoteDetail')->middleware('auth');
Route::post('/quote/updateQuoteDetail', 'QuoteController@updateQuoteDetail')->middleware('auth');
Route::post('/quote/invoices/approveItem', 'QuoteController@approveItem')->middleware('auth');
Route::post('/quote/finishFactura', 'QuoteController@finishFactura')->middleware('auth');
Route::post('/quote/loadQuoteDetail2', 'QuoteController@loadQuoteDetail2')->middleware('auth');
Route::post('/quote/finishReactivar', 'QuoteController@finishReactivar')->middleware('auth');
Route::post('/quote/savePackageValue', 'QuoteController@savePackageValue')->middleware('auth');


Route::get('/location/select2-autocomplete', 'Select2AutocompleteController@layout');
Route::get('/location/select2-autocomplete-ajax', 'Select2AutocompleteController@dataAjax');


Route::post('/location/updateLocation', 'LocationController@updateLocation')->middleware('auth');
Route::post('/location/insertLocation', 'LocationController@insertLocation')->middleware('auth');
Route::post('/location/deleteLocation', 'LocationController@deleteLocation')->middleware('auth');

Route::post('/quote/uploadQuoteFiles', 'QuoteController@uploadQuoteFiles')->middleware('auth');
Route::post('/quote/removeFile', 'QuoteController@removeFile')->middleware('auth');

// images
Route::get('/uploads/{file}', [function ($file) {
	$user = auth()->user();
    $organization= \App\Organization::find($user->organization_id);
	$type = 'image/jpeg';
	if ($file === '_logo') {
		$fileExtension = array_slice(explode('.', $organization->logo),-1,1);
		$fileExtension = strtolower($fileExtension[0]);
		if ($fileExtension === 'jpg' || $fileExtension === 'jpeg') {
			$type = 'image/jpeg';
		} elseif ($fileExtension === 'png') {
			$type = 'image/png';
		}
		$file = $organization->logo;
	}
	$path = storage_path('app/uploads/'.$user->organization_id.'/'.$file);
	if (file_exists($path)) {
		return response()->file($path, array('Content-Type' => $type));
	}
	abort(404);
}])->middleware('auth');
Route::get('/download/{file}', [function ($file) {
	$user = auth()->user();
	$file_path = storage_path('app/uploads/'.$user->organization_id.'/'.$file);
    if (file_exists($file_path)) {
        return response()->download($file_path, $file, [
            'Content-Length: '. filesize($file_path)
        ]);
    }
	abort(404);
}])->middleware('auth');


// Route::group(['middleware' => 'App\Http\Middleware\UserMiddleware'], function(){
// 	Route::match(['get', 'post'], '/memberOnlyPage/', 'HomeController@member');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/quote/invoice/{id}', 'QuoteController@editInvoice')->middleware('auth');

Route::get('/quote/create', 'AccountController@create')->middleware('auth');
Route::post('/quote/createBA', 'AccountController@postCreateAccount')->middleware('auth');

Route::get('/quote/createBA', 'AccountController@createBA')->middleware('auth');
Route::post('/quote/createBA2', 'AccountController@postCreateBA')->middleware('auth');


// cambios middleware

Route::get('quote/quotes', 'QuoteController@quotes')->middleware('auth');
Route::get('quote/invoices', 'QuoteController@invoices')->middleware('auth');

Route::get('quote/quotePdf/{id}', 'QuoteController@quotePdf')->middleware('auth');
Route::get('quote/invoicePdf/{id}', 'QuoteController@invoicePdf')->middleware('auth');
Route::post('quote/quotesReportPdf', 'ReportController@quotesReportPdf')->middleware('auth');

// fin cambios middleware

// Route::get('/', function () {

//     Fpdf::AddPage();
//     Fpdf::SetFont('Courier', 'B', 18);
//     Fpdf::Cell(50, 25, 'Hello World!');
//     Fpdf::Output();

// });