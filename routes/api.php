<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/get-announcement', 'DashboardController@getAnnouncement');
        Route::get('/get-opening-opportunity', 'DashboardController@getOpeningOpportunity');
        Route::get('/get-opportunity', 'DashboardController@getOpportunity');
        Route::get('/get-institutional', 'DashboardController@getInstitutional');
        Route::get('/get-member', 'DashboardController@getMember');
        Route::get('/get-new-regulation', 'DashboardController@getNewRegulation');
        Route::get('/get-new-member', 'DashboardController@getNewMember');
        Route::get('/institution/{institution}', 'DashboardController@profileInstitution');
    });

    Route::group(['prefix' => 'title'], function () {
        Route::get('/', 'TitleController@index');
        Route::get('/select-list', 'TitleController@selectList');
        Route::get('/{title}', 'TitleController@show');
        Route::post('/', 'TitleController@store');
        Route::put('/{title}', 'TitleController@store');
        Route::delete('/{title}', 'TitleController@destroy');
    });

    Route::get('/v2/title/', 'TitleController@getForDatatables');

    Route::group(['prefix' => 'nationality'], function () {
        Route::get('/', 'NationalityController@index');
        Route::get('/select-list', 'NationalityController@selectList');
        Route::get('/{nationality}', 'NationalityController@show');
        Route::post('/', 'NationalityController@store');
        Route::put('/{nationality}', 'NationalityController@store');
        Route::delete('/{nationality}', 'NationalityController@destroy');
    });

    Route::group(['prefix' => 'skill'], function () {
        Route::get('/', 'SkillController@index');
        Route::get('/select-list', 'SkillController@selectList');
        Route::get('/{skill}', 'SkillController@show');
        Route::post('/', 'SkillController@store');
        Route::put('/{skill}', 'SkillController@store');
        Route::delete('/{skill}', 'SkillController@destroy');
    });

    Route::group(['prefix' => 'department'], function () {
        Route::get('/', 'DepartmentController@index');
        Route::get('/select-list', 'DepartmentController@selectList');
        Route::get('/get-list-institution', 'DepartmentController@getListInstitution');
        Route::get('/{department}', 'DepartmentController@show');
        Route::post('/', 'DepartmentController@store');
        Route::put('/{department}', 'DepartmentController@store');
        Route::delete('/{department}', 'DepartmentController@destroy');
    });

    Route::group(['prefix' => 'institution'], function () {
        Route::get('/', 'InstitutionController@index');
        Route::get('/select-list', 'InstitutionController@selectList');
        Route::get('/{institution}', 'InstitutionController@show');
        Route::post('/', 'InstitutionController@store');
        Route::put('/{institution}', 'InstitutionController@store');
        Route::delete('/{institution}', 'InstitutionController@destroy');
    });

    Route::group(['prefix' => 'regulation'], function () {
        Route::get('/', 'RegulationController@index');
        Route::get('/get-list-institution', 'RegulationController@getListInstitution');
        Route::get('/{regulation}', 'RegulationController@show');
        Route::post('/', 'RegulationController@store');
        Route::post('/{regulation}', 'RegulationController@storeFiles');
        Route::put('/{regulation}', 'RegulationController@store');
        Route::delete('/{regulation}', 'RegulationController@destroy');
        Route::get('/files/{regulationFile}', 'RegulationController@showFile');
    });

    Route::group(['prefix' => 'opportunity'], function () {
        Route::get('/', 'OpportunityController@index');
        Route::get('/get-institution', 'OpportunityController@getInstitution');
        Route::get('/get-type-opportunity', 'OpportunityController@getTypeOpportunity');
        Route::get('/{opportunity}', 'OpportunityController@show');
        Route::post('/', 'OpportunityController@store');
        Route::post('/{opportunity}', 'OpportunityController@storeFiles');
        Route::post('/{opportunity}/interest', 'OpportunityController@interest');
        Route::put('/{opportunity}', 'OpportunityController@store');
        Route::delete('/{opportunity}', 'OpportunityController@destroy');
        Route::get('/files/{opportunityFile}', 'OpportunityController@showFile');
    });

    Route::group(['prefix' => 'announcement'], function () {
        Route::get('/', 'AnnouncementController@index');
        Route::get('/{announcement}', 'AnnouncementController@show');
        Route::get('/{announcement}/show-file/', 'AnnouncementController@showFile');
        Route::get('/{announcement}/comment', 'AnnouncementController@getComment');
        Route::post('/', 'AnnouncementController@store');
        Route::post('/{announcement}/comment', 'AnnouncementController@storeComment');
        Route::put('/{announcement}', 'AnnouncementController@store');
        Route::delete('/{announcement}', 'AnnouncementController@destroy');
        Route::delete('/{announcement}/comment/{announcementComment}', 'AnnouncementController@destroyComment');
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', 'ProfileController@index');
        Route::post('/', 'ProfileController@store');
        Route::post('/institution/{institution}', 'ProfileController@storeInstitution');
        Route::get('/institution/photo/{institution}', 'ProfileController@showFileInstitution');
        Route::post('/member/{member}', 'ProfileController@storeMember');
        Route::post('/change-mail/{id}', 'ProfileController@changeMail');
        Route::get('/member/photo/{member}', 'ProfileController@showFileMember');
        Route::get('/photo', 'ProfileController@showFile');
        Route::post('/photo/update', 'ProfileController@storePhoto');
        Route::get('/files/member/{member}', 'ProfileController@showFileMember');
    });

    Route::group(['prefix' => 'menu'], function () {
        Route::get('/', 'MenuController@index');
        Route::get('/sidebar', 'MenuController@sidebar');
    });

    Route::group(['prefix' => 'role'], function () {
        Route::get('/', 'RoleController@index');
        Route::get('/select-list', 'RoleController@selectList');
        Route::get('/{role}', 'RoleController@show');
        Route::post('/', 'RoleController@store');
        Route::put('/{role}', 'RoleController@store');
        Route::delete('/{role}', 'RoleController@destroy');
    });


    Route::group(['prefix' => 'research-user'], function () {
        Route::get('/', 'ResearchUserController@index');
        Route::get('/interest', 'ResearchUserController@getInterest');
        Route::get('/skill', 'ResearchUserController@getSkill');
        Route::get('/department', 'ResearchUserController@getDepartment');
        Route::get('/{member}', 'ResearchUserController@show');
        Route::post('/send-invitation', 'ResearchUserController@sendingInvitation');
        Route::get('/accept-invitation', 'ResearchUserController@acceptInvitation');
        Route::post('/change-institution/{member}', 'ResearchUserController@changeInstitution');
        Route::post('/{member}', 'ResearchUserController@store');
        Route::patch('/{member}', 'ResearchUserController@acceptMember');
    });

    Route::group(['prefix' => 'research-group'], function () {
        Route::get('/', 'ResearchGroupController@index');
        Route::post('/', 'ResearchGroupController@store');
        Route::put('/{researchGroup}', 'ResearchGroupController@store');
        Route::delete('/{researchGroup}', 'ResearchGroupController@destroy');
        Route::get('/{researchGroup}/list-of-member', 'ResearchGroupController@listOfMember');
        Route::get('/{researchGroup}', 'ResearchGroupController@show');
        Route::post('/{researchGroup}/join', 'ResearchGroupController@join');
        Route::delete('/{researchGroup}/leave', 'ResearchGroupController@leave');
        Route::post('/{researchGroup}/select-as-admin', 'ResearchGroupController@selectAsAdmin');

        Route::get('/{researchGroup}/discussion', 'ResearchGroupController@listDiscussion');
        Route::post('/{researchGroup}/create-discussion', 'ResearchGroupController@createDiscussion');

        Route::group(['prefix' => '/discussion'], function () {
            Route::patch('/{researchGroupDiscussion}/close-discussion', 'ResearchGroupController@closeDiscussion');
            Route::delete('/{researchGroupDiscussion}/delete-discussion', 'ResearchGroupController@deleteDiscussion');

            Route::get('/{researchGroupDiscussion}/get-comment', 'ResearchGroupController@getComment');
            Route::post('/{researchGroupDiscussion}/add-comment', 'ResearchGroupController@storeComment');

            Route::group(['prefix' => '/discussion-comment'], function () {
                Route::delete('/{researchGroupComment}/delete-comment', 'ResearchGroupController@deleteComment');
                Route::get('/{researchGroupComment}/show-file', 'ResearchGroupController@getFileComment');
            });
        });

    });


    Route::group(['prefix' => 'academic-degree'], function () {
        Route::get('/', 'AcademicDegreeController@index');
        Route::get('/select-list', 'AcademicDegreeController@selectList');
        Route::get('/{academicDegree}', 'AcademicDegreeController@show');
        Route::post('/', 'AcademicDegreeController@store');
        Route::put('/{academicDegree}', 'AcademicDegreeController@store');
        Route::delete('/{academicDegree}', 'AcademicDegreeController@destroy');
    });

    Route::group(['prefix' => 'publication-type'], function () {
        Route::get('/', 'PublicationTypeController@index');
        Route::get('/select-list', 'PublicationTypeController@selectList');
        Route::get('/{publicationType}', 'PublicationTypeController@show');
        Route::post('/', 'PublicationTypeController@store');
        Route::put('/{publicationType}', 'PublicationTypeController@store');
        Route::delete('/{publicationType}', 'PublicationTypeController@destroy');
    });
    Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');
});

Route::post('/sign-up-institution', 'RegisterController@signUpInstitution');
Route::post('/sign-up-researcher', 'RegisterController@signUpResearcher');
Route::get('/change-email/approve', 'ProfileController@approveMail');

Route::group(['prefix' => 'public'], function () {
    Route::get('/title', 'TitleController@selectList');
    Route::get('/institution', 'InstitutionController@selectList');
    Route::get('/nationality', 'NationalityController@selectList');
    Route::get('/department', 'DepartmentController@selectList');
    Route::get('/announcement/{announcement}/show-file/', 'AnnouncementController@showFile');
});

Route::group(['prefix' => 'profile'], function () {
    Route::get('/institution/photo/{institution}', 'ProfileController@showFileInstitution');
    Route::get('/member/photo/{member}', 'ProfileController@showFileMember');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/', 'AuthController@login'); // login
    Route::delete('/', 'AuthController@logout'); //logout
    Route::get('/', 'AuthController@me');
    Route::get('/refresh', 'AuthController@refresh');
});

Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
